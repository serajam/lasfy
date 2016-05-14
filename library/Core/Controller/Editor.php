<?php

/**
 * Administration controller
 *
 * @author     Petryk Fedor
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_Controller_Editor extends Core_Controller_Auth
{
    /**
     * Default service class name for current controller
     *
     * @var String
     */
    protected $_defaultServiceName = 'Core_Service_Editor';

    /**
     * The service layer object
     *
     * @var Core_Service_Editor
     */
    protected $_service;

    protected $_key = 'id';

    public function preDispatch()
    {
        $id             = (int)$this->_getParam($this->_key);
        $this->view->id = $id;
        $this->_paramsParser();
        $this->_editorSetup();
    }

    protected function _paramsParser()
    {
    }

    protected function _editorSetup()
    {
    }

    public function indexAction()
    {
        $this->view->page = $this->_getParam('page', $this->_pagination);
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
    }

    public function editAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && !$this->_request->isPost()) {
            $id = (int)$this->getParam($this->_key);
            if ($id > 0) {
                $this->_service->getJson($id);
            }
        }
        if ($this->_request->isPost()) {
            $this->_service->edit($this->_request->getPost(), $this->view->id);
            $this->_ajaxPostHelper();
        }
    }

    protected function _ajaxPostHelper()
    {
    }

    public function deleteAction()
    {
        $this->_service->delete(
            (int)$this->_getParam($this->_key)
        );
    }

    public function viewAction()
    {
        $this->_ajaxHelper();
    }
}
