<?php

/**
 * Extended base class for bootstrap classes
 *
 *
 * @uses       Zend_Application_Module_Bootstrap
 * @package    Default
 * @subpackage Bootstrap
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Default_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected $_moduleName = 'default';

    /**
     *
     * Init module specific configuration
     */
    protected function _initConfiguration()
    {
        $options = $this->getApplication()->getOptions();
        Zend_Registry::set('config', $options);
        defined('APPLICATION_PUB')
        || define('APPLICATION_PUB', BASE_PATH . '/../application' . '/modules/' . $this->_moduleName);
    }

    protected function _initView()
    {
        $view = new Zend_View();
        $view->setEncoding('UTF-8');
        $view->doctype('XHTML1_STRICT');
        $view->addHelperPath('Core/View/Helper/', 'Core_View_Helper');
        $view->addHelperPath('Core/View/Helper/Html', 'Core_View_Helper_Html');
        $view->addHelperPath('Core/View/Helper/Buttons', 'Core_View_Helper_Buttons');
        $view->addHelperPath('Core/View/Helper/Foundation', 'Core_View_Helper_Foundation');
        $view->addHelperPath('Core/View/Helper/Client', 'Core_View_Helper_Client');
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'ViewRenderer'
        );

        $viewRenderer->setView($view);

        return $view;
    }
}