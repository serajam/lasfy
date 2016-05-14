<?php

/**
 * User administration controller
 *
 * @author     Petryk Fedor
 *
 */
class Admin_IndexController extends Core_Controller_Start
{
    /**
     * Default service class name for current controller
     *
     * @var String
     */
    protected $_defaultServiceName = 'AdminService';

    /**
     * The service layer object
     *
     * @var AdminService
     */
    protected $_service;

    /**
     * View current Roles
     * see view script
     */
    public function indexAction()
    {
    }

    /**
     * View current Roles
     * see view script
     */
    public function rolesAction()
    {
    }

    /**
     * Create new role
     */
    public function addAction()
    {
        $acl             = $this->_service->getFullAcl();
        $this->view->acl = $acl;

        if ($this->_request->isPost()) {
            $this->view->post = $post = $this->_request->getPost();
            $add              = $this->_service->saveRole($post);

            if ($add) {
                $this->_redirect('admin/index/edit/id/' . $add->roleId);
            }
        }
    }

    /**
     * Edit existence Role
     */
    public function editAction()
    {
        $id = (int)$this->_request->getParam('id');
        if ($id == 0) {
            $this->_redirect('admin');
        }
        $acl             = $this->_service->getFullAcl();
        $this->view->acl = $acl;
        if ($this->_request->isPost()) {
            $this->view->post = $post = $this->_request->getPost();
            $this->_service->saveRole($post);
        }
        $this->_service->getRole($id);
    }
}