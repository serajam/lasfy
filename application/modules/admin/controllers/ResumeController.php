<?php

/**
 * Resume administration controller
 * @author     Petryk Fedor
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Admin_ResumeController extends Core_Controller_Editor
{
    /**
     * Default service class name for current controller
     * @var String
     */
    protected $_defaultServiceName = 'ResumeService';

    /**
     * The service layer object
     * @var ResumeService
     */
    protected $_service;

    protected $_pagination = true;

    protected $_htmlContainer = 'resume-list';

    public function indexAction()
    {
        $this->view->collection = $this->_service->getCollection();
    }

    public function changeStateAction()
    {
    }
}
