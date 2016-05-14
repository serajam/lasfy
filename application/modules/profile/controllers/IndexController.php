<?php

/**
 * Profile controller
 *
 * @author      Alexey Kagarlykskiy
 * @copyright   Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Profile_IndexController extends Core_Controller_Auth
{
    protected $_defaultServiceName = 'ProfileService';

    /**
     * @var ProfileService
     */
    protected $_service;

    public function preDispatch()
    {
        $this->_helper->layout->setLayout('profile');
    }

    public function sendMessageAction()
    {
    }

    public function messagesAction()
    {
        if ($id = (int)$this->getParam('vid', 0)) {
            $type = MessagesAccess::VACANCY_TYPE;
        } elseif ($id = (int)$this->getParam('rid', 0)) {
            $type = MessagesAccess::RESUME_TYPE;
        }

        if (!$id) {
            $this->noPage();

            return false;
        }

        $this->view->addId = $id;
        $replierId         = (int)$this->getParam('reid', 0);
        MessagesAccess::setReplierId($replierId);

        if ($this->getRequest()->isPost()) {
            $this->_service->sendMessage($this->getAllParams(), $id, $type, $replierId);
        }
        $this->_helper->viewRenderer('messages-' . $type);
        $this->view->{$type} = $this->_service->getMapper()->{'get' . ucfirst($type)}($id);

        if (empty($this->view->{$type})) {
            $this->noPage();

            return false;
        }

        $isOwner = $this->view->{$type}->userId == Core_Model_User::getInstance()->userId;
        if ($isOwner) {

            $this->view->repliers = $this->_service->getRepliers($id, $type);
        }

        if ($isOwner && $replierId) {
            $this->_service->markMessagesAsViewed($id, $type, $replierId);
        } elseif (!$isOwner) {
            $this->_service->markMessagesAsViewed($id, $type, $replierId);
        }

        $this->view->userMessages = $this->_service->getAddMessages($id, $type, $replierId);
        if (!$this->view->{$type}) {
            $this->noPage();

            return false;
        }
    }

    public function conversationsAction()
    {
        $this->view->conversations = $this->_service->getConversations();
    }

    public function profileAction()
    {
        $this->view->header       = __('yourProfile');
        $this->view->userForm     = $this->_service->getPopulatedUserForm();
        $this->view->companyForm  = $this->_service->getPopulatedCompanyForm();
        $this->view->passwordForm = $this->_service->getFormLoader()->getForm('ChangePasswordForm');

        if ($this->_request->isPost()) {
            $post    = (string)$this->_request->getParams();
            $process = $post['process'];
            $this->_service->{$process}($post);
        }
    }

    public function getLogoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->view->logo = $this->_service->getCompanyLogo();
    }
}
