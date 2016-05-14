<?php

/**
 * Default base class for bootstraping module
 *
 * @author     Fedor Petryk
 * @uses       Zend_Application_Bootstrap_Bootstrap
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Admin_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected $_moduleName = 'admin';

    /**
     *
     * Init module specific configuration
     */
    protected function _initConfiguration()
    {
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Core_Controller_Plugin_Navigation());
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
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'ViewRenderer'
        );
        $viewRenderer->setView($view);

        return $view;
    }
}