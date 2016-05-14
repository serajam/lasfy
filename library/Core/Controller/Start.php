<?php

/**
 * Application start controller
 *
 * @author      Fedor Petryk
 * @copyright   Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_Controller_Start extends Core_Controller_Super
{
    protected function setPageMetaData($slug = null)
    {

        $lang = Zend_Registry::get('language');
        $page = $this->_service->getMapper()->getPage($slug ? $slug : $this->getRequest()->getActionName(), false, $lang);

        if (empty($page)) {
            return false;
        }

        $this->view->title       = $page->metaTitle;
        $this->view->keywords    = $page->metaKeywords;
        $this->view->description = $page->metaDescription;
        $this->view->page        = $page;

        return false;
    }
}