<?php

/**
 * Management controller for manage the CVs and Jobs of user
 *
 * @author      Alexey Kagarlykskiy
 * @copyright   Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Profile_ManagementController extends Core_Controller_Editor
{
    protected $_defaultServiceName = 'ManagementService';

    /**
     * @var ManagementService
     */
    protected $_service;

    protected $_typeAd = null;

    public function preDispatch()
    {
        $this->_helper->layout->setLayout('profile');
        $this->_typeAd = $this->_getParam('ad');
        parent::preDispatch();

        if (!empty($this->_typeAd)) {
            $this->_service->verifyTypeOfAd($this->_typeAd);
        }
    }

    public function cvsJobsAction()
    {
        $this->view->vacancies = $this->_service->getUserVacancies();
        $this->view->resumes   = $this->_service->getUserResumes();
    }

    public function deleteAction()
    {
        $this->_service->deleteAd(
            $this->view->id, $this->_typeAd
        );
    }

    public function editAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && !$this->_request->isPost()) {
            $id = (int)$this->getParam($this->_key);
            if ($id > 0) {
                $this->_service->getJson($id, $this->_typeAd);
            }
        } elseif ($this->getRequest()->isXmlHttpRequest() && $this->_request->isPost()) {
            $this->_service->editAd($this->_request->getPost(), $this->view->id, $this->_typeAd);
            $this->_ajaxPostHelper();
        } else {
            $this->noPage();
        }
    }

    public function publishAction()
    {
        $this->_service->publish($this->view->id, $this->_typeAd);
    }

    public function unpublishAction()
    {
        $this->_service->unpublish($this->view->id, $this->_typeAd);
    }
}
