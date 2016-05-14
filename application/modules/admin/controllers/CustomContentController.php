<?php

/**
 * Pages administration controller
 *
 * @author     Petryk Fedor
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Admin_CustomContentController extends Core_Controller_Editor
{
    /**
     * Default service class name for current controller
     *
     * @var String
     */
    protected $_defaultServiceName = 'CustomContentService';

    /**
     * The service layer object
     *
     * @var CustomContentService
     */
    protected $_service;

    protected $_pagination = false;

    protected $_htmlContainer = 'content';
}
