<?php

/**
 * RSS feeds controller
 *
 * @author      Alexey Kagarlykskiy
 * @copyright   Copyright (c) 2013-2016 Studio 105 (http://105.in.ua)
 */
class Default_RssController extends Core_Controller_Start
{
    protected $_defaultServiceName = 'RssService';

    /**
     * @var RssService
     */
    protected $_service;

    public function init()
    {
        parent::init();
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function vacanciesAction()
    {
        $feed = $this->_service->getVacanciesRssFeed();
        echo $feed->send();
    }

    public function resumesAction()
    {
        $feed = $this->_service->getResumesRssFeed();
        echo $feed->send();
    }

    public function alladsAction()
    {
        $feed = $this->_service->getBothRssFeed();
        echo $feed->send();
    }
}