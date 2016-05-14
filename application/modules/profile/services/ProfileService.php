<?php

/**
 * The profile service class
 * Class ProfileService
 *
 * @author     Alexey Kagarlykskiy
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class ProfileService extends Core_Service_Editor
{
    /**
     * Users mapper object
     *
     * @var ProfileMapper
     */
    protected $_mapperName = 'ProfileMapper';

    /**
     * @var ProfileMapper
     */
    protected $_mapper;

    /**
     * @return bool|MessagesCollection
     */
    public function getConversations()
    {
        $userId        = Core_Model_User::getInstance()->userId;
        $conversations = $this->_mapper->getUserConversations($userId);

        return $conversations;
    }

    /**
     * @param $addId
     * @param $addType
     * @param $repliedId
     */
    public function markMessagesAsViewed($addId, $addType, $repliedId)
    {
        $this->_mapper->markMessagesAsViewed($addId, $addType, $repliedId);
    }

    /**
     * @param $addId
     * @param $addType
     *
     * @return bool|UsersCollection
     */
    public function getRepliers($addId, $addType)
    {
        return $this->_mapper->getRepliers($addId, Core_Model_User::getInstance()->userId, $addType);
    }

    /**
     * @param      $addId
     * @param      $addType
     * @param bool $replierId
     *
     * @return bool|MessagesCollection
     */
    public function getAddMessages($addId, $addType, $replierId = false)
    {
        $add = $this->_mapper->{'get' . $addType}($addId);
        if (!$add) {
            return false;
        }

        // I am owner
        if ($add->userId == Core_Model_User::getInstance()->userId) {
            // no replier selected
            if (!$replierId) {
                return false;
            }
            $relations = $this->getUserRelations($replierId, Core_Model_User::getInstance()->userId);
            if (!$relations || $relations->status == 2) {
                return false;
            }
        } // I am viewer
        else {
            $relations = $this->getUserRelations(Core_Model_User::getInstance()->userId, $add->userId);
            if (!$relations || $relations->status == 2) {
                return false;
            }
        }
        $messages = $this->_mapper->getAddMessages($addId, $addType, $relations->friendId, $relations->userId);
        if (!$messages) {
            return false;
        }
        $owner  = $this->_mapper->getUser($relations->friendId);
        $viewer = $this->_mapper->getUser($relations->userId);
        /** @var Message $message */
        foreach ($messages as $message) {
            if ($message->userFrom == $owner->userId) {
                $message->setSenderName($owner->userName);
            } elseif ($message->userFrom == $viewer->userId) {
                $message->setSenderName($viewer->getName());
            }
        }

        return $messages;
    }

    public function getUserRelations($senderId, $receiverId)
    {
        return $this->_mapper->getUsersRelations($senderId, $receiverId);
    }

    /**
     * @param array $data
     * @param       $addId
     * @param       $addType
     * @param bool  $receiverId
     *
     * @return bool
     * @throws Zend_Form_Exception
     */
    public function sendMessage(array $data, $addId, $addType, $receiverId = false)
    {
        $add = $this->_mapper->{'get' . $addType}($addId);
        if (!$add) {
            return false;
        }

        $senderId = Core_Model_User::getInstance()->userId;
        if ($add->userId == Core_Model_User::getInstance()->userId) {
            if (!$receiverId) {
                return false;
            }
        } else {
            $receiverId = $add->userId;
        }
        if (!($relations = $this->getUserRelations($senderId, $receiverId))) {
            $relations = $this->addUserRelations($senderId, $receiverId);
        }
        if ($relations->status == 2) {
            $this->setError(__('user_banned'));
        }

        $form = $this->getFormLoader()->getForm('MessageForm');
        if (!$form->isValid($data)) {
            $this->_processFormError($form);

            return false;
        }

        $message           = new Message($form->getValues());
        $message->userFrom = $relations->userId;
        $message->userTo   = $relations->friendId;
        $message->sendDate = date('Y-m-d H:i:s');
        $message->addId    = $addId;
        $message->addType  = $addType;
        $message->new      = 1;

        $this->_mapper->saveMessage($message);
        $form->reset();

        // send personal message for receiver
        $userReceiver = $this->getMapper()->getUser($receiverId);
        $userSender   = $this->getMapper()->getUser($receiverId);

        $lang   = Zend_Registry::get('language');
        $config = Zend_Registry::get('appConfig');

        $type = ($addType = MessagesAccess::VACANCY_TYPE ? 'vid' : 'rid');

        $params = [
            'fio'  => $userSender->getName(),
            'link' => $config['baseHttpPath'] . $lang . '/profile/index/messages/' . $type . '/' . $addId,
        ];
        $mail   = new Core_Mailer('newMessage', $params, true);
        $mail->addTo($userReceiver->email, $userReceiver->getName());
        $mail->send();
    }

    protected function addUserRelations($friendId, $userId)
    {
        // hir relations
        $relations           = new UserRelation();
        $relations->friendId = $userId;
        $relations->userId   = $friendId;
        $relations->status   = 1;
        $this->_mapper->addUserRelations($relations);

        // my relations
        $relations           = new UserRelation();
        $relations->friendId = $friendId;
        $relations->userId   = $userId;
        $relations->status   = 1;
        $this->_mapper->addUserRelations($relations);

        return $relations;
    }

    /**
     * @return Core_Form
     */
    public function getPopulatedUserForm()
    {
        $userForm = $this->getUserForm();
        $userForm->populate($this->_user->toArray());

        return $userForm;
    }

    /**
     * @return Core_Form
     */
    public function getUserForm()
    {
        if ($this->getFormLoader()->isExists('ProfileUserForm')) {
            return $this->getFormLoader()->getForm('ProfileUserForm');
        }
        $userForm = $this->getFormLoader()->addForm('ProfileUserForm');

        return $userForm;
    }

    /**
     * @return Core_Form
     */
    public function getPopulatedCompanyForm()
    {
        $companyForm = $this->getCompanyForm();
        $company     = $this->_mapper->getUserCompany($this->_user->userId);
        if ($company) {
            $companyForm->populate($company->toArray());
        } else {
            $companyForm->getElement('userId')->setValue($this->_user->userId);
        }

        return $companyForm;
    }

    /**
     * @return Core_Form
     */
    public function getCompanyForm()
    {
        if ($this->getFormLoader()->isExists('CompanyForm')) {
            return $this->getFormLoader()->getForm('CompanyForm');
        }
        $companyForm = $this->getFormLoader()->addForm('CompanyForm');

        return $companyForm;
    }

    /**
     * @return bool|string
     */
    public function getCompanyLogo()
    {
        $companyLogo = $this->_mapper->getCompanyLogo($this->_user->userId);
        if (!$companyLogo) {
            $companyLogo = file_get_contents('images/no-logo.png');
        }

        return $companyLogo;
    }

    /**
     * @param $data
     *
     * @return bool
     * @throws Exception
     * @throws Zend_Form_Exception
     */
    public function saveUser($data)
    {
        $userForm = $this->getUserForm();
        if (!$userForm->isValid($data)) {
            $this->_processFormError($userForm);

            return false;
        }

        if ((int)$data['userId'] !== (int)$this->_user->userId) {
            $this->setError(Core_Messages_Message::getMessage('youAreHacker'));

            return false;
        }

        $this->_user->populate($userForm->getValues());
        $res = $this->save($this->_user);
        $this->_user->createUserStorage();

        if (!$res || $res->getError()) {
            $error = __('cantSaveUserData') . ' - ' . $res->getError();
            $this->setError(Core_Messages_Message::getMessage($error));

            return false;
        }

        $this->setMessage(__('dataSaved'));

        return true;
    }

    /**
     * @param $data
     *
     * @return bool
     * @throws Exception
     * @throws Zend_Form_Exception
     */
    public function saveCompany($data)
    {
        $companyForm = $this->getCompanyForm();

        if (!$companyForm->isValid($data)) {
            $this->_processFormError($companyForm);

            return false;
        }

        if ((int)$data['userId'] !== (int)$this->_user->userId) {
            $this->setError(Core_Messages_Message::getMessage('youAreHacker'));

            return false;
        }

        $company = $this->_mapper->getUserCompany($this->_user->userId);
        if (empty($company)) {
            $company         = new Core_Model_Company();
            $company->userId = $this->_user->userId;
        } else {
            $companyForm->getElement('companyId')->setValue($company->companyId);
        }
        $company->populate($companyForm->getValues());

        $adapter = $companyForm->file->getTransferAdapter();
        $adapter->setOptions(['ignoreNoFile' => true], $adapter->getFileInfo());
        if ($adapter->isReceived()) {
            $imageHandler  = new Core_Image_Manager();
            $company->logo = $imageHandler->processLogo($adapter->getFileInfo());
        }

        $this->_mapper->setDbTable('Core_DbTable_Company');
        $res = $this->save($company);

        if (!$res || $res->getError()) {
            $error = __('cantSaveCompanyData') . ' - ' . $res->getError();
            $this->setError(Core_Messages_Message::getMessage($error));

            return false;
        }

        $this->setMessage(__('dataSaved'));

        return true;
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function changePassword(array $data)
    {
        $form = $this->getFormLoader()->getForm('ChangePasswordForm');
        if (!$form->isValid($data)) {
            $this->_processFormError($form);

            return false;
        }
        $user         = $this->_mapper->getUser(Core_Model_User::getInstance()->userId);
        $hashCostLog2 = 8;
        $hasher       = new   PasswordHash($hashCostLog2, false);

        // checking entered password to be valid against stored hash
        if (!$hasher->CheckPassword($form->getValue('oldpassword'), $user->securityCode)) {
            $this->setError(__('invalid_old_password'));

            return false;
        }

        $user           = new Core_Users_User();
        $user->password = $form->getValue('password');
        $user->setPasswordHash();
        $res = $this->_mapper->setPassword(Core_Model_User::getInstance()->userId, $user->securityCode);
        $this->setMessage(__('dataSaved'));

        return true;
    }
}