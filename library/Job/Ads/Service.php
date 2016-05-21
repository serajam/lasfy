<?php

/**
 * Job_Ads_AdsService
 *
 * @author     Aleksey Kagarlykskiy
 * @copyright  Copyright (c) 2013-2015 Studio 105 (http://105.in.ua)
 */
class Job_Ads_Service extends Job_Tags_TagsService
{
    /**
     * Ads mapper class
     *
     * @var String
     */
    protected $_mapperName = 'Job_Ads_Mapper';

    /**
     * @var Job_Ads_Mapper
     */
    protected $_mapper;

    public function editAd($post, $adId, $adType)
    {
        $this->verifyTypeOfAd($adType);

        if (!empty($adId) && !$this->_canMakeChange($adId, $adType)) {
            $this->setError(Core_Messages_Message::getMessage('cantEdit'));

            return false;
        }
        $this->prepareToRequest($adType);
        parent::edit($post, $adId);
        $this->_mapper->setDefaultDbTable();
    }

    public function verifyTypeOfAd($typeAd)
    {
        if (!in_array($typeAd, ['vacancy', 'resume'])) {
            $this->setError(Core_Messages_Message::getMessage('wrongTypeOfAd'));

            return false;
        }
    }

    public function prepareToRequest($adType, $needForm = true)
    {
        $adType = ucfirst($adType);
        $this->_mapper->setRowClass($adType);
        $dbTable = 'Job_Ads_VacanciesTable';
        if (!strcmp($adType, 'Resume')) {
            $dbTable = 'Job_Ads_ResumesTable';
        }
        $this->_mapper->setDbTable($dbTable);

        if ($needForm) {
            $form = 'get' . $adType . 'Form';
            $this->setValidatorForm($this->$form());
        }
    }

    public function getVacancyForm()
    {
        if ($this->getFormLoader()->isExists('VacancyForm')) {
            return $this->getFormLoader()->getForm('VacancyForm');
        }

        $vacancyForm = $this->getFormLoader()->addForm('VacancyForm');

        !$this->isUser() ? false : $vacancyForm->removeElement('captchaCode');

        return $vacancyForm;
    }

    public function getResumeForm()
    {
        if ($this->getFormLoader()->isExists('ModelResumeForm')) {
            return $this->getFormLoader()->getForm('ModelResumeForm');
        }

        $resumeForm = $this->getFormLoader()->addForm('ModelResumeForm');

        !$this->isUser() ? false : $resumeForm->removeElement('captchaCode');

        return $resumeForm;
    }

    public function getVacanciesForPeriod($jsonShape = false, $strictCompliance = false, $page)
    {
        $this->prepareToRequest('vacancies', false);
        $res = $this->_mapper->getVacancyForPeriod($page);
        if ($jsonShape) {
            return $this->makeArrayView($res, $strictCompliance);
        }

        return $res;
    }

    public function makeArrayView($data, $strictCompliance, $tagsArray = null)
    {
        $rArray = false;
        if (empty($data)) {
            return $rArray;
        }

        foreach ($data as $d) {
            $tags = $d->getTagsArray();
            if ($strictCompliance && !empty($tagsArray)) {
                $complArray = $this->makeTagsArrayForCompliance($tags);
                sort($tagsArray);
                if (count($tagsArray) != count(array_intersect($tagsArray, $complArray))) {
                    unset($d);
                    continue;
                }
            }

            $tagsFormatted = [];
            foreach ($tags as $tag) {
                $tagsFormatted[] = ['tagId' => $tag['tagId'], 'tagName' => $tag['tagName']];
            }

            $dArray         = $d->toArray();
            $dArray['tags'] = $tagsFormatted;
            $rArray[]       = $dArray;
        }

        return $rArray;
    }

    public function makeTagsArrayForCompliance($tags)
    {
        $tagsArray = [];
        foreach ($tags as $t) {
            $tagsArray[] = $t['tagName'];
        }

        sort($tagsArray);

        return $tagsArray;
    }

    public function getResumeForPeriod($jsonShape = false, $strictCompliance = false, $page)
    {
        $this->prepareToRequest('vacancies', false);
        $res = $this->_mapper->getResumeForPeriod($page);
        if ($jsonShape) {
            return $this->makeArrayView($res, $strictCompliance);
        }

        return $res;
    }

    public function getAdsLastWeek($adType, $jsonShape = false, $strictCompliance = false, $limit)
    {
        $this->prepareToRequest($adType, false);
        $mapperFunc = 'get' . ucfirst($adType) . 'ForLastWeek';
        $res        = $this->_mapper->$mapperFunc($limit);
        if ($jsonShape) {
            return $this->makeArrayView($res, $strictCompliance);
        }

        return $res;
    }

    public function getAdsLastMonth($data, $page, $filter, $type = false)
    {
        if (!empty($type)) {
            $filter = [$type];
        }

        $response = [];

        $formatter = new Job_Ads_ResponseFormatter((boolean)$data['strictCompliance'], []);

        $starDate = date('Y-m-d', strtotime('-1 month'));
        $endDate  = date('Y-m-d');

        foreach ($filter as $type) {
            $gateway    = Job_Ads_GatewayFactory::create($type);
            $repository = Job_Ads_RepositoryFactory::create($type, $gateway);

            $response += $this->getAdsByPeriod($repository, $formatter, $page, $starDate, $endDate);
        }

        return $response;
    }

    public function getAdsByPeriod(
        IAdsRepository $repository, Job_Ads_ResponseFormatter $formatter, $page, $startDate, $endDate
    )
    {
        $tags       = [Zend_Registry::get('appConfig')["defaultRegion"]];
        $collection = $repository->fetchByPeriod($page, $startDate, $endDate, $tags);
        count($collection) ?: $this->makeEmptyResponse($repository->getType());
        $this->fillWithTags($collection, $repository->getTagsJoinTable(), $repository->getPrimary(), []);

        return $this->makeResponse($formatter, $collection, $repository->getType(), $repository->getPaginator());
    }

    protected function makeEmptyResponse($type)
    {
        return [
            $type               => '',
            $type . 'Paginator' => '',
        ];
    }

    protected function fillWithTags($collection, $tagsJoinType, $tagsJoinedColumn, $tags)
    {
        $tagsByAds = [];
        if (!empty($tagsJoinType)) {
            $tagsByAds = $this->_mapper->getTagsByJoinedAds($collection->getIds(), $tagsJoinType, $tagsJoinedColumn);
            if ($tagsByAds) {
                $collection->populateWithTags($tagsByAds);
            }
        }

        if ($tags && !count($tagsByAds)) {
            $collection->populateWithTags($tags);
        };
    }

    protected function makeResponse(Job_Ads_ResponseFormatter $formatter, $collection, $type, $paginator)
    {
        return [
            $type               => $formatter->json($collection),
            $type . 'Paginator' => $paginator,
        ];
    }

    public function getAdsByTags($data, $page, $filter, $type = false)
    {
        if (!empty($type)) {
            $filter = [$type];
        }

        $response  = [];
        $tagsArray = Job_Tags_TagModel::tagsStringToArray($data['searchTags']);

        $existedTagsArray = $this->_mapper->getExistedTagsByName($tagsArray);
        if (!$existedTagsArray) {
            return ['vacancies' => false, 'resumes' => false];
        }

        $formatter = new Job_Ads_ResponseFormatter((boolean)$data['strictCompliance'], $tagsArray);

        foreach ($filter as $type) {
            $gateway    = Job_Ads_GatewayFactory::create($type);
            $repository = Job_Ads_RepositoryFactory::create($type, $gateway);

            $response += $this->getAds($repository, $formatter, $existedTagsArray, $page);
        }

        return $response;
    }

    public function getAds(IAdsRepository $repository, Job_Ads_ResponseFormatter $formatter, $tags, $page)
    {
        $collection = $repository->fetchByTags($tags, $page);
        count($collection) ?: $this->makeEmptyResponse($repository->getType());
        $this->fillWithTags($collection, $repository->getTagsJoinTable(), $repository->getPrimary(), $tags);

        return $this->makeResponse($formatter, $collection, $repository->getType(), $repository->getPaginator());
    }

    public function getInitialTags()
    {
        if ($this->isUser()) {
            $userCompany = $this->getCompanyByUserId($this->_user->userId);
            if (!empty($userCompany)) {
                $tags      = str_replace(' ', ',', $userCompany->name);
                $tagsArray = explode(',', $tags);
                $tagsArray = array_unique($tagsArray);
                $tags      = '"' . implode('","', $tagsArray) . '"';

                return $tags;
            }
        }

        return false;
    }

    public function getAdByTypeAndId($adType, $adId)
    {
        $this->prepareToRequest($adType, false);
        $mapperFunc = 'get' . ucfirst($adType) . 'ById';
        $res        = $this->_mapper->$mapperFunc($adId);

        return $res;
    }

    public function getCityTag()
    {
    }

    public function getAddsByTags()
    {
    }

    protected function _preSave($model)
    {
        $model->createdAt = date('Y-m-d H:i:s');

        !$this->isUser() ? $model->isTemporary = 1 : false;

        return true;
    }

    protected function _postSave($model)
    {
        $tagsArray = Job_Tags_TagModel::tagsStringToArray($model->tags);
        array_push($tagsArray, mb_strtolower($model->seat));

        $tagsArray = array_unique($tagsArray);

        $existedTagsArray = $this->_mapper->getExistedTagsByName($tagsArray);
        $existedTagsArray = array_map(function ($item) {
            return mb_strtolower($item);
        }, $existedTagsArray);

        if (!empty($existedTagsArray)) {
            $tablePrefix  = $model->getName();
            $tagsArrayDif = array_diff($tagsArray, $existedTagsArray);
            if (count($tagsArrayDif) > 0) {
                $res = $this->_mapper->saveTags($tagsArrayDif);
                if (!$res) {
                    $this->delete($model->getFirstProperty());
                    $this->_mapper->deleteAdTags($model->getFirstProperty(), $tablePrefix);
                    $this->setError(Core_Messages_Message::getMessage('cantSave'));

                    return false;
                }
            }
        }

        $existedTagsArray = $this->_mapper->getExistedTagsByName($tagsArray);
        $this->setDependency($model, $existedTagsArray);

        return true;
    }

    public function setDependency($model, $existedTagsArray)
    {
        $tablePrefix = $model->getName();
        $this->_mapper->deleteAdTags($model->getFirstProperty(), $tablePrefix);
        $res = $this->_mapper->setDependecy($model, $existedTagsArray, $tablePrefix);
        if (!$res) {
            $this->delete($model->getFirstProperty());
            $this->_mapper->deleteAdTags($model->getFirstProperty(), $tablePrefix);
            $this->setError(Core_Messages_Message::getMessage('cantSaveCV'));

            return false;
        }

        if ($this->isUser()) {
            $isBinded = $this->_mapper->isAdBondToUser($this->_user->userId, $model->getFirstProperty(), $tablePrefix);
            if (!$isBinded) {
                $resBind = $this->_mapper->bindUserAndAd($this->_user->userId, $model->getFirstProperty(), $tablePrefix);
                if (!$resBind) {
                    $this->delete($model->getFirstProperty());
                    $this->_mapper->deleteAdTags($model->getFirstProperty(), $tablePrefix);
                    $this->setError(Core_Messages_Message::getMessage('cantSave'));

                    return false;
                }
            }
        }

        return true;
    }
}