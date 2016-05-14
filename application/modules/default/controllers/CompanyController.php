<?php

/**
 * Companies controller
 *
 * @author      Fedor Petryk
 * @copyright   Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Default_CompanyController extends Core_Controller_Start
{
    protected $_defaultServiceName = 'CompanyService';

    /**
     * @var CompanyService
     */
    protected $_service;

    protected $_pagination = true;

    public function indexAction()
    {
        $this->_helper->layout->setLayout('main');
        if ($this->_request->isPost()) {
            $post                  = $this->_request->getParams();
            $page                  = $this->_getParam('page', $this->_pagination);
            $companies             = $this->_service->getCompaniesByName($post['searchRequest'], $page);
            $this->view->companies = $companies;
            $form                  = $this->_service->getSearchCompanyForm();
            $form->getElement('searchRequest')->setValue($post['searchRequest']);
            $this->view->searchForm    = $form;
            $_SESSION['searchRequest'] = $post['searchRequest'];
        } else {
            $page = $this->_getParam('page', false);
            if (!$page) {
                unset($_SESSION['searchRequest']);
                $page = true;
            }
            $form = $this->_service->getSearchCompanyForm();
            if (isset($_SESSION['searchRequest'])) {
                $searchRequest = $_SESSION['searchRequest'];
                $companies     = $this->_service->getCompaniesByName($searchRequest, $page);
                $form->getElement('searchRequest')->setValue($searchRequest);
            } else {
                $companies             = $this->_service->getCompaniesList($page);
                $serializedCompanies   = serialize($companies);
                $_SESSION['companies'] = $serializedCompanies;
            }
            $this->view->companies  = $companies;
            $this->view->searchForm = $form;
        }

        $service = new DefaultService();
        $lang    = Zend_Registry::get('language');
        $page    = $service->getMapper()->getPage('companies', false, $lang);

        $this->view->title       = $page->metaTitle;
        $this->view->keywords    = $page->metaKeywords;
        $this->view->description = $page->metaDescription;
        $this->view->page        = $page;
    }

    public function viewAction()
    {
        $companyId = $this->getParam('id');
        if (isset($_SESSION['companies'])) {
            $companies = unserialize($_SESSION['companies']);
            $company   = $companies->getModelByKey('companyId', $companyId);
        } else {
            $company = $this->_service->getCompanyById($companyId);
        }
        $this->view->company = $company;
    }
}