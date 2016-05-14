<?php

/**
 *
 * The profile service class
 *
 * @author     Alexey Kagarlykskiy
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class ManagementService extends Job_Ads_Service
{
    /**
     * Management mapper object
     *
     * @var ManagementMapper
     */
    protected $_mapperName = 'ManagementMapper';

    /**
     * @var ManagementMapper
     */
    protected $_mapper;

    /**
     * @var Core_Model_User
     */
    protected $_user;

    public function __construct()
    {
        parent::__construct();
        $this->_user = Core_Model_User::getInstance();
    }

    public function getUserVacancies()
    {
        return Core_Model_User::isUserAuthenticated() ? $this->_mapper->getUserVacancies($this->_user->userId) : false;
    }

    public function getUserResumes()
    {
        return Core_Model_User::isUserAuthenticated() ? $this->_mapper->getUserResumes($this->_user->userId) : false;
    }

    public function deleteAd($adId, $adType)
    {
        if (!$this->_canMakeChange($adId, $adType)) {
            $this->setError(Core_Messages_Message::getMessage('cantDelete'));

            return false;
        }
        $this->prepareToRequest($adType);
        parent::delete($adId);
        $this->_mapper->setDefaultDbTable();
    }

    protected function _canMakeChange($adId, $adType)
    {
        $adType    = ucfirst($adType);
        $isUsersAd = $this->_mapper->isAdBondToUser($this->_user->userId, $adId, $adType);

        return $isUsersAd;
    }

    public function getJson($adId, $adType = null)
    {
        if (!$this->_canMakeChange($adId, $adType)) {
            $this->setError(Core_Messages_Message::getMessage('cantEdit'));

            return false;
        }
        $this->prepareToRequest($adType);
        parent::getJson($adId);
    }

    public function publish($adId, $adType)
    {
        if (!$this->_canMakeChange($adId, $adType)) {
            $this->setError(Core_Messages_Message::getMessage('cantEdit'));

            return false;
        }
        $this->prepareToRequest($adType);
        $data = ['isPublished' => 1];
        $res  = $this->_mapper->setPublishingParam($adId, $data, $adType);

        if (!$res) {
            $this->setError(Core_Messages_Message::getMessage('cantPublish'));

            return false;
        }

        $this->setMessage(Core_Messages_Message::getMessage(1));
    }

    public function unpublish($adId, $adType)
    {
        if (!$this->_canMakeChange($adId, $adType)) {
            $this->setError(Core_Messages_Message::getMessage('cantEdit'));

            return false;
        }
        $this->prepareToRequest($adType);
        $data = ['isPublished' => 0];
        $res  = $this->_mapper->setPublishingParam($adId, $data, $adType);

        if (!$res) {
            $this->setError(Core_Messages_Message::getMessage('cantUnPublish'));

            return false;
        }

        $this->setMessage(Core_Messages_Message::getMessage(1));
    }

    protected function _fillModel($model)
    {
        $tablePrefix = $model->getName();
        $tags        = $this->_mapper->getTagsByModelId($model->getFirstProperty(), $tablePrefix);
        $model->tags = implode(',', $tags);
    }
}