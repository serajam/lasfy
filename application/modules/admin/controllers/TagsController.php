<?php

/**
 * Tags administration controller
 * Admin_TagsController
 * @author     Petryk Fedor
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Admin_TagsController extends Core_Controller_Editor
{
    /**
     * Default service class name for current controller
     * @var String
     */
    protected $_defaultServiceName = 'Job_Tags_TagsService';

    /**
     * The service layer object
     * @var Job_Tags_TagsService
     */
    protected $_service;

    protected $_pagination = true;

    protected $_htmlContainer = 'tags-list';

    public function indexAction()
    {
        $filters             = $this->getAllParams();
        $this->view->filters = $filters;
        isset($filters['page']) ? $this->view->page = $filters['page'] : $this->view->page = 1;
        $this->view->collection = $this->_service->getTagsCollection($filters);
    }
}
