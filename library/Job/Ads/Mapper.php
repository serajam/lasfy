<?php

/**
 * Job_Ads_AdsMapper class
 *
 * @author     Aleksey Kagarlykskiy
 * @copyright  Copyright (c) 2013-2015 Studio 105 (http://105.in.ua)
 */
class Job_Ads_Mapper extends Job_Tags_TagsMapper
{
    /**
     * @var Job_Ads_VacanciesTable
     */
    protected $_tableName = 'Job_Ads_VacanciesTable';

    protected $_rowClass = 'Job_Ads_Model_Vacancy';

    protected $_collectionClass = 'Job_Ads_Model_VacanciesCollection';

    public function bindUserAndAd($userId, $modelId, $tablePrefix)
    {
        $db   = $this->getAdapter();
        $info = [
            'userId'                        => $userId,
            strtolower($tablePrefix) . 'Id' => (int)$modelId
        ];
        $res  = $db->insert('Users' . $tablePrefix, $info);

        return $res;
    }

    public function isAdBondToUser($userId, $modelId, $tablePrefix)
    {
        $db = $this->getAdapter();

        $select = $db->select()
            ->from(['um' => 'Users' . $tablePrefix])
            ->where('um.userId = ?', $userId)
            ->where(strtolower($tablePrefix) . 'Id = ?', (int)$modelId);

        $res = $db->fetchRow($select);

        if (!empty($res)) {
            return true;
        }

        return false;
    }

    public function getUserVacancies($userId)
    {
        $db = $this->getAdapter();

        $select = $db->select()
            ->from(['uv' => 'UsersVacancy'], [])
            ->joinLeft(['v' => 'Vacancies'], 'v.vacancyId = uv.vacancyId')
            ->joinLeft(['vt' => 'VacancyTags'], 'vt.vacancyId = v.vacancyId', [])
            ->joinLeft(['t' => 'Tags'], 't.tagId = vt.tagId')
            ->where('uv.userId = ?', $userId);

        $res = $db->fetchAll($select);

        if (!empty($res)) {
            return $this->_formCollection($res, 'vacancy');
        }

        return false;
    }

    protected function _formCollection($dataArray, $adType)
    {
        $objName        = ucfirst($adType);
        $adsArray       = [];
        $ad             = null;
        $adId           = $adType . 'Id';
        $nameCollection = 'Job_Ads_Model_VacanciesCollection';
        if (!strcmp($objName, 'Resume')) {
            $nameCollection = 'Job_Ads_Model_ResumesCollection';
        }
        $adsCollection = new $nameCollection();
        foreach ($dataArray as $r) {
            if ((isset($ad) && $r[$adId] != $ad->$adId) || !($ad instanceof $objName)) {
                $ad = new $objName();
                $ad->populate($r);
                $adsArray[$r[$adId]] = $ad;
            }
            if (!empty($r['tagId'])) {
                $tag = new Tag();
                $tag->populate(
                    [
                        'tagId'   => $r['tagId'],
                        'tagName' => $r['tagName']
                    ]
                );
                if ($adsArray[$r[$adId]]->tags instanceof TagsCollection) {
                    $adsArray[$r[$adId]]->tags->add($tag);
                } else {
                    $adsArray[$r[$adId]]->tags = new TagsCollection();
                    $adsArray[$r[$adId]]->tags->add($tag);
                }
            }
        }

        foreach ($adsArray as $ra) {
            $adsCollection->add($ra);
        }

        return $adsCollection;
    }

    public function getUserResumes($userId)
    {
        $db = $this->getAdapter();

        $select = $db->select()
            ->from(['ur' => 'UsersResume'])
            ->joinLeft(['r' => 'Resumes'], 'r.resumeId = ur.resumeId')
            ->joinLeft(['rt' => 'ResumeTags'], 'rt.resumeId = r.resumeId', [])
            ->joinLeft(['t' => 'Tags'], 't.tagId = rt.tagId')
            ->where('ur.userId = ?', $userId);

        $res = $db->fetchAll($select);

        if (!empty($res)) {
            return $this->_formCollection($res, 'resume');
        }

        return false;
    }

    public function setPublishingParam($adId, $data, $adType)
    {
        return $this->getDbTable()->update($data, $adType . 'Id = "' . $adId . '"');
    }

    public function getVacancyForPeriod($page = 1)
    {
        $db = $this->getAdapter();

        $dateRange = date('Y-m-d', strtotime('-1 month'));
        $select    = $db->select()
            ->from(['v' => 'Vacancies'])
            ->joinLeft(['uv' => 'UsersVacancy'], 'uv.vacancyId = v.vacancyId', ['uv.userId'])
            ->where('v.isBanned = 0')
            ->where('v.isPublished = 1')
            ->where('v.createdAt > ?', $dateRange)
            ->order('v.createdAt DESC');

        if ($page) {
            $adapter     = new Zend_Paginator_Adapter_DbSelect($select);
            $selectCount = $this->getDbTable()->select()
                ->from(
                    ['v' => 'Vacancies'],
                    [
                        Zend_Paginator_Adapter_DbSelect::ROW_COUNT_COLUMN => 'count(*)'
                    ]
                );

            $selectCount
                ->where('v.isBanned = 0')
                ->where('v.isPublished = 1')
                ->where('v.createdAt > ?', $dateRange)
                ->order('v.createdAt ASC');

            $adapter->setRowCount($selectCount);
            $paginator = new Zend_Paginator($adapter);
            $config    = Zend_Registry::get('appConfig');
            $paginator->setCurrentPageNumber($page);
            $paginator->setItemCountPerPage($config['itemsPerPage']);
            $collection = ($collection = $this->_formCollection(
                $paginator->getCurrentItems(),
                'vacancy'
            )) ? $collection : new ModelVacanciesCollection();

            if (!$collection || !$collection->count()) {
                return false;
            }

            foreach ($collection as $m) {
                $mTags = $this->getTagsByModelId($m->vacancyId, 'Vacancy');
                $m->setTags($mTags);
                $m->userId = $this->getUserIdByVacancyId($m->vacancyId);
            }
            $collection->setPaginator($paginator);

            return $collection;
        }

        $db->select()
            ->joinLeft(['vt' => 'VacancyTags'], 'vt.vacancyId = v.vacancyId', [])
            ->joinLeft(['t' => 'Tags'], 't.tagId = vt.tagId')
            ->order('t.tagName ASC');

        $res = $db->fetchAll($select);

        if (!empty($res)) {
            return $this->_formCollection($res, 'vacancy');
        }

        return false;
    }

    public function getUserIdByVacancyId($vacancyId)
    {
        $db = $this->getAdapter();

        $select = $db->select()
            ->from(['uv' => 'UsersVacancy'], ['uv.userId'])
            ->where('uv.vacancyId = ?', $vacancyId);

        $res = $db->fetchOne($select);

        return $res;
    }

    public function getResumeForPeriod($page = 1)
    {
        $db = $this->getAdapter();

        $dateRange = date('Y-m-d', strtotime('-1 month'));
        $select    = $db->select()
            ->from(['r' => 'Resumes'])
            ->joinLeft(['ur' => 'UsersResume'], 'ur.resumeId = r.resumeId', ['ur.userId'])
            ->where('r.isBanned = 0')
            ->where('r.isPublished = 1')
            ->where('r.createdAt > ?', $dateRange)
            ->order('r.createdAt DESC');

        if ($page) {
            $adapter     = new Zend_Paginator_Adapter_DbSelect($select);
            $selectCount = $db->select()
                ->from(
                    ['r' => 'Resumes'],
                    [
                        Zend_Paginator_Adapter_DbSelect::ROW_COUNT_COLUMN => 'count(*)'
                    ]
                );

            $selectCount
                ->joinLeft(['ur' => 'UsersResume'], 'ur.resumeId = r.resumeId', ['ur.userId'])
                ->where('r.isBanned = 0')
                ->where('r.isPublished = 1')
                ->where('r.createdAt > ?', $dateRange)
                ->order('r.createdAt ASC');

            $adapter->setRowCount($selectCount);
            $paginator = new Zend_Paginator($adapter);
            $config    = Zend_Registry::get('appConfig');
            $paginator->setCurrentPageNumber($page);
            $paginator->setItemCountPerPage($config['itemsPerPage']);
            $collection = ($collection = $this->_formCollection(
                $paginator->getCurrentItems(),
                'resume'
            )) ? $collection : new ResumeCollection();

            if (!$collection || !$collection->count()) {
                return false;
            }

            foreach ($collection as $m) {
                $mTags = $this->getTagsByModelId($m->resumeId, 'Resume');
                $m->setTags($mTags);
                $m->userId = $this->getUserIdByResumeId($m->resumeId);
            }

            $collection->setPaginator($paginator);

            return $collection;
        }

        $db->select()
            ->joinLeft(['rt' => 'ResumeTags'], 'rt.resumeId = r.resumeId', [])
            ->joinLeft(['t' => 'Tags'], 't.tagId = rt.tagId')
            ->order('t.tagName ASC');

        $res = $db->fetchAll($select);

        if (!empty($res)) {
            return $this->_formCollection($res, 'resume');
        }

        return false;
    }

    public function getUserIdByResumeId($resumeId)
    {
        $db = $this->getAdapter();

        $select = $db->select()
            ->from(['ur' => 'UsersResume'], ['ur.userId'])
            ->where('ur.resumeId = ?', $resumeId);

        $res = $db->fetchOne($select);

        return $res;
    }

    public function getVacancyForLastWeek($limit = false)
    {
        $db = $this->getAdapter();

        $dateRange = date('Y-m-d', strtotime('-1 week'));
        $select    = $db->select()
            ->from(['v' => 'Vacancies'])
            ->joinLeft(['uv' => 'UsersVacancy'], 'uv.vacancyId = v.vacancyId', ['uv.userId'])
            ->where('v.isBanned = 0')
            ->where('v.isPublished = 1')
            ->where('v.createdAt > ?', $dateRange)
            ->orWhere('v.renewedAt > ?', $dateRange)
            ->order('v.createdAt DESC');

        $db->select()
            ->joinLeft(['vt' => 'VacancyTags'], 'vt.vacancyId = v.vacancyId', [])
            ->joinLeft(['t' => 'Tags'], 't.tagId = vt.tagId')
            ->order('t.tagName ASC');

        if ($limit) {
            $db->select()->limit($limit);
        }

        $res = $db->fetchAll($select);

        if (!empty($res)) {
            return $this->_formCollection($res, 'vacancy');
        }

        return false;
    }

    public function getResumeForLastWeek($limit = false)
    {
        $db = $this->getAdapter();

        $dateRange = date('Y-m-d', strtotime('-1 week'));
        $select    = $db->select()
            ->from(['r' => 'Resumes'])
            ->joinLeft(['ur' => 'UsersResume'], 'ur.resumeId = r.resumeId', ['ur.userId'])
            ->where('r.isBanned = 0')
            ->where('r.isPublished = 1')
            ->where('r.createdAt > ?', $dateRange)
            ->orWhere('r.renewedAt > ?', $dateRange)
            ->order('r.createdAt DESC');

        $db->select()
            ->joinLeft(['rt' => 'ResumeTags'], 'rt.resumeId = r.resumeId', [])
            ->joinLeft(['t' => 'Tags'], 't.tagId = rt.tagId')
            ->order('t.tagName ASC');

        if ($limit) {
            $db->select()->limit($limit);
        }

        $res = $db->fetchAll($select);

        if (!empty($res)) {
            return $this->_formCollection($res, 'resume');
        }

        return false;
    }

    public function getVacanciesByTagsIds($tagsIds, $page = 1)
    {
        $db = $this->getAdapter();

        $select = $db->select()
            ->from(['v' => 'Vacancies'])
            ->where(
                'v.vacancyId IN (' . new Zend_Db_Expr(
                    "SELECT vv.vacancyId
                     FROM Vacancies AS vv
                     LEFT JOIN VacancyTags AS vtt ON vtt.vacancyId = vv.vacancyId
                     WHERE (vtt.tagId IN ({$tagsIds}))"
                ) . ')'
            )
            ->where('v.isBanned = 0')
            ->where('v.isPublished = 1')
            ->order('v.createdAt ASC');

        if ($page) {
            $adapter     = new Zend_Paginator_Adapter_DbSelect($select);
            $selectCount = $db->select()
                ->from(
                    ['v' => 'Vacancies'],
                    [
                        Zend_Paginator_Adapter_DbSelect::ROW_COUNT_COLUMN => 'count(*)'
                    ]
                );

            $selectCount
                ->where(
                    'v.vacancyId IN (' . new Zend_Db_Expr(
                        'SELECT vv.vacancyId
                     FROM Vacancies AS vv
                     LEFT JOIN VacancyTags AS vtt ON vtt.vacancyId = vv.vacancyId
                     WHERE (vtt.tagId IN (' . $tagsIds . '))'
                    ) . ')'
                )
                ->where('v.isBanned = 0')
                ->where('v.isPublished = 1')
                ->order('v.createdAt ASC');

            $adapter->setRowCount($selectCount);
            $paginator = new Zend_Paginator($adapter);
            $config    = Zend_Registry::get('appConfig');
            $paginator->setCurrentPageNumber($page);
            $paginator->setItemCountPerPage($config['itemsPerPage']);
            $collection = $this->_formCollection($paginator->getCurrentItems(), 'vacancy');
            foreach ($collection as $m) {
                $mTags = $this->getTagsByModelId($m->vacancyId, 'Vacancy');
                $m->setTags($mTags);
                $m->userId = $this->getUserIdByVacancyId($m->vacancyId);
            }

            $collection->setPaginator($paginator);

            return $collection;
        }

        $db->select()
            ->joinLeft(['vt' => 'VacancyTags'], 'vt.vacancyId = v.vacancyId', [])
            ->joinLeft(['t' => 'Tags'], 't.tagId = vt.tagId')
            ->order('t.tagName ASC');

        $res = $db->fetchAll($select);

        if (!empty($res)) {
            return $this->_formCollection($res, 'vacancy');
        }

        return false;
    }

    public function getResumeByTagsIds($tagsIds, $page = 1)
    {
        $db = $this->getAdapter();

        $select = $db->select()
            ->from(['r' => 'Resumes'])
            ->where(
                'r.resumeId IN (' . new Zend_Db_Expr(
                    'SELECT rr.resumeId
                     FROM Resumes AS rr
                     LEFT JOIN ResumeTags AS rtt ON rtt.resumeId = rr.resumeId
                     WHERE (rtt.tagId IN (' . $tagsIds . '))'
                ) . ')'
            )
            ->where('r.isBanned = 0')
            ->where('r.isPublished = 1')
            ->order('r.createdAt ASC');

        if ($page) {
            $adapter     = new Zend_Paginator_Adapter_DbSelect($select);
            $selectCount = $db->select()
                ->from(
                    ['r' => 'Resumes'],
                    [
                        Zend_Paginator_Adapter_DbSelect::ROW_COUNT_COLUMN => 'count(*)'
                    ]
                );

            $selectCount
                ->where(
                    'r.resumeId IN (' . new Zend_Db_Expr(
                        'SELECT rr.resumeId
                     FROM Resumes AS rr
                     LEFT JOIN ResumeTags AS rtt ON rtt.resumeId = rr.resumeId
                     WHERE (rtt.tagId IN (' . $tagsIds . '))'
                    ) . ')'
                )
                ->where('r.isBanned = 0')
                ->where('r.isPublished = 1')
                ->order('r.createdAt ASC');

            $adapter->setRowCount($selectCount);
            $paginator = new Zend_Paginator($adapter);
            $config    = Zend_Registry::get('appConfig');
            $paginator->setCurrentPageNumber($page);
            $paginator->setItemCountPerPage($config['itemsPerPage']);
            $collection = $this->_formCollection($paginator->getCurrentItems(), 'resume');
            foreach ($collection as $m) {
                $mTags = $this->getTagsByModelId($m->resumeId, 'Resume');
                $m->setTags($mTags);
                $m->userId = $this->getUserIdByResumeId($m->resumeId);
            }

            $collection->setPaginator($paginator);

            return $collection;
        }

        $db->select()
            ->joinLeft(['rt' => 'ResumeTags'], 'rt.resumeId = r.resumeId', [])
            ->joinLeft(['t' => 'Tags'], 't.tagId = rt.tagId')
            ->order('t.tagName ASC');

        $res = $db->fetchAll($select);

        if (!empty($res)) {
            return $this->_formCollection($res, 'resume');
        }

        return false;
    }

    public function getVacancies($limit = null)
    {
        $db = $this->getAdapter();

        $select = $db->select()
            ->from(['v' => 'Vacancies'], ['vacancyId', 'seat', 'vacancyDescription', 'createdAt'])
            ->where('v.isBanned = 0')
            ->where('v.isPublished = 1')
            ->order('v.createdAt DESC');

        if (!empty($limit)) {
            $select->limit("LIMIT {$limit}");
        }

        $res = $db->fetchAll($select);

        if ($res) {
            return $res;
        }

        return false;
    }

    public function getResumes($limit = null)
    {
        $db = $this->getAdapter();

        $select = $db->select()
            ->from(['r' => 'Resumes'], ['resumeId', 'goals', 'seat', 'createdAt'])
            ->where('r.isBanned = 0')
            ->where('r.isPublished = 1')
            ->order('r.createdAt DESC');

        if (!empty($limit)) {
            $select->limit("LIMIT {$limit}");
        }

        $res = $db->fetchAll($select);

        if ($res) {
            return $res;
        }

        return false;
    }

    public function getVacancyById($vacancyId)
    {
        $db = $this->getAdapter();

        $select = $db->select()
            ->from(['v' => 'Vacancies'])
            ->joinLeft(['vt' => 'VacancyTags'], 'vt.vacancyId = v.vacancyId', [])
            ->joinLeft(['t' => 'Tags'], 't.tagId = vt.tagId')
            ->where('v.vacancyId = ?', $vacancyId)
            ->where('v.isBanned = 0')
            ->where('v.isPublished = 1')
            ->order('t.tagName ASC');

        $res = $db->fetchAll($select);

        if ($res) {
            return $this->_formCollection($res, 'vacancy');
        }

        return false;
    }

    public function getResumeById($resumeId)
    {
        $db = $this->getAdapter();

        $select = $db->select()
            ->from(['r' => 'Resumes'])
            ->joinLeft(['rt' => 'ResumeTags'], 'rt.resumeId = r.resumeId', [])
            ->joinLeft(['t' => 'Tags'], 't.tagId = rt.tagId')
            ->where('r.resumeId = ?', $resumeId)
            ->where('r.isBanned = 0')
            ->where('r.isPublished = 1')
            ->order('t.tagName ASC');

        $res = $db->fetchAll($select);

        if ($res) {
            return $this->_formCollection($res, 'resume');
        }

        return false;
    }
}
