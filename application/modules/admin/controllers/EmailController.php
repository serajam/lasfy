<?php

/**
 * Email controller
 *
 * @author     Petryk Fedir
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Admin_EmailController extends Core_Controller_Editor
{
    /**
     * Default service class name for current controller
     *
     * @var String
     */
    protected $_defaultServiceName = 'EmailService';

    protected $_pagination = true;

    /**
     * @var EmailService
     */
    protected $_service;
}
