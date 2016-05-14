<?php

/**
 * User administration controller
 *
 * @author     Petryk Fedor
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Admin_UsersController extends Core_Controller_Start
{
    /**
     * Default service class name for current controller
     *
     * @var String
     */
    protected $_defaultServiceName = 'UsersService';

    /**
     * The service layer object
     *
     * @var UsersService
     */
    protected $_service;

    /**
     * View current Roles
     * see view script
     */
    public function listUsersAction()
    {
        $userId = (int)$this->_request->getParam('userId');
        if ($this->_request->isPost()) {
            $post = $this->_request->getPost();
            $this->_service->editUserData($post, $userId);
        } elseif ($userId != null && $this->getRequest()->isXmlHttpRequest()) {
            $user = $this->_service->getUser($userId);
            $this->_service->setJsonData($user);
        }

        $page              = $this->_getParam('page', 1);
        $this->view->users = $this->_service->getUsers($page, Core_Users_User::SIMPLE_USER, $userId);
    }

    /**
     * Edit existence Role
     */
    public function changeUserRoleAction()
    {
        $id = (int)$this->_request->getParam('userId');
        $this->_service->changeUserRole($this->_request->getPost() + ['userId' => $id]);
    }

    /**
     * Edit Users
     */
    public function editUserAction()
    {
        $userId = (int)$this->_request->getParam('userId');
        if ($this->_request->isPost()) {
            $post = $this->_request->getPost();
            $this->_service->editUserData($post, $userId);
        } else {
            $userId = $this->_request->getParam('userId');
            if ($userId != null) {
                $user = $this->_service->getUser($userId);
                $this->_service->setJsonData($user);
            }
        }
    }

    public function listManagersAction()
    {
        $userId = (int)$this->_request->getParam('userId');
        if ($this->_request->isPost()) {
            $post = $this->_request->getPost();
            $this->_service->editUserData($post, $userId);
        } else {
            $userId = $this->_request->getParam('userId');
            if ($userId != null) {
                $user = $this->_service->getUser($userId);
                $this->_service->setJsonData($user);
            }
        }

        $page              = $this->_getParam('page', 1);
        $this->view->users = $this->_service->getUsers($page, Core_Users_User::MANAGER_USER);
    }

    /**
     * Add user
     */
    public function addUserAction()
    {
        if ($this->_request->isPost()) {
            $post = $this->_request->getPost();
            $this->_service->addUser($post);
        }
    }

    /**
     * Edit user's password
     */
    public function editPasswordAction()
    {
        if ($this->_request->isPost()) {
            $post   = $this->_request->getPost();
            $userId = $this->_request->getParam('userId');
            $this->_service->changePassword($post, $userId);
        }
    }

    public function profileDetailsAction()
    {
        $userId                     = $this->_request->getParam('userId');
        $profileDetails             = $this->_service->getUserDetails($userId);
        $this->view->profileDetails = $profileDetails;
    }

    public function banAction()
    {
        $userId   = $this->_request->getParam('userId');
        $isBanned = $this->_request->getParam('ban');
        $this->_service->isBanned($userId, $isBanned);
    }

    public function activeAction()
    {
        $userId      = $this->_request->getParam('userId');
        $isActivated = $this->_request->getParam('active');
        $this->_service->isActivated($userId, $isActivated);
    }

    public function deleteUserAction()
    {
        $userId = (int)$this->_request->getParam('userId');
        $this->_service->delete($userId);
    }

    public function userDetailsAction()
    {
        $userId           = (int)$this->_request->getParam('userId');
        $this->view->user = $this->_service->getUser($userId);
    }
}
