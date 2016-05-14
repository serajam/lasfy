<?php

class Default_PageController extends Core_Controller_Start
{
    protected $_defaultServiceName = 'DefaultService';

    /**
     * @var DefaultService
     */
    protected $_service;

    public function indexAction()
    {
        $url  = $this->_request->getParam('url');
        $lang = Zend_Registry::get('language');
        $page = $this->_service->getMapper()->getPage($url, false, $lang);

        if (empty($page)) {
            $this->noPage();

            return false;
        }

        $this->view->title       = $page->metaTitle;
        $this->view->keywords    = $page->metaKeywords;
        $this->view->description = $page->metaDescription;
        $this->view->page        = $page;
    }

    public function blogAction()
    {
        $this->view->header = __('blog');
        $this->view->pages  = $this->_service->getBlogPages();

        $this->setPageMetaData();
    }

    public function newsAction()
    {
        $this->view->header = __('news');
        $this->view->pages  = $this->_service->getNewsPages();

        $this->setPageMetaData();
    }

    public function citiesAction()
    {
        $lang               = Zend_Registry::get('language');
        $this->view->cities = $this->_service
            ->getMapper()
            ->getTagsByTypeWithText(Job_Tags_TagsMapper::TAG_TYPE_CITY, $lang);
    }

    public function jobsAction()
    {
        $lang             = Zend_Registry::get('language');
        $this->view->jobs = $this->_service
            ->getMapper()
            ->getTagsByTypeWithText(Job_Tags_TagsMapper::TAG_TYPE_JOB, $lang);
    }

    public function sitemapAction()
    {
        $lang                  = Zend_Registry::get('language');
        $this->view->menuItems = $this->_service->getSiteMenu();
        $this->view->blogPages = $this->_service->getBlogPages();
    }
}