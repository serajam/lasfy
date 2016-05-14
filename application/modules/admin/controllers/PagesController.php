<?php

/**
 * Pages administration controller
 *
 * @author     Petryk Fedor
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Admin_PagesController extends Core_Controller_Editor
{
    /**
     * Default service class name for current controller
     *
     * @var String
     */
    protected $_defaultServiceName = 'PagesService';

    /**
     * The service layer object
     *
     * @var PagesService
     */
    protected $_service;

    protected $_pagination = true;

    protected $_htmlContainer = 'pages-list';

    public function indexAction()
    {
        $page             = $this->_getParam('page', $this->_pagination);
        $data['lang']     = $this->_getParam('lang');
        $data['pageType'] = $this->_getParam('pageType');

        $this->view->collection = $this->_service->getPagesCollection($data, $page);
        $this->view->params     = $data;
    }

    public function editAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && !$this->_request->isPost()) {
            $id = (int)$this->getParam($this->_key);
            if ($id > 0) {
                $this->_service->getPageJson($id, $this->getParam('slug'), $this->getParam('lang'));
            }
        }
        if ($this->_request->isPost()) {
            $this->_service->edit($this->_request->getPost(), $this->view->id);
            $this->_ajaxPostHelper();
        }
    }

    public function editPageAction()
    {
    }

    public function deleteAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && !$this->_request->isPost()) {
            $this->_service->deletePage($this->_request->getParams(), (int)$this->getParam($this->_key));
            $this->_ajaxPostHelper();
        }
    }

    public function listCategoriesAction()
    {
    }

    public function editCategoryAction()
    {
        parent::editAction();
    }

    public function deleteCategoryAction()
    {
        parent::deleteAction();
    }

    public function editMenuAction()
    {
        $id = (int)$this->getParam($this->_key);
        if ($this->getRequest()->isXmlHttpRequest() && !$this->_request->isPost()) {
            if ($id > 0) {
                $this->_service->getMenuJson($id);
            }
        }

        if ($this->_request->isPost()) {
            $post = $this->_request->getPost();
            $this->_service->editMenu($post, $id);
        }
    }

    public function deleteMenuAction()
    {
        $this->_service->deleteMenu((int)$this->_getParam($this->_key));
    }

    public function listMenuAction()
    {
    }
}
