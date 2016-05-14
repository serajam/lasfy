<?php

/**
 * Extended base class for bootstrap classes
 *
 *
 * @uses       Zend_Application_Module_Bootstrap
 * @package    Profile
 * @subpackage Bootstrap
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Profile_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected $_moduleName = 'profile';

    /**
     *
     * Init module specific configuration
     */
    protected function _initConfiguration()
    {
        $front = Zend_Controller_Front::getInstance();
        $auth  = new Core_Controller_Plugin_Authentication();

        $defaultLang = (Zend_Registry::isRegistered('language')
            ? Zend_Registry::get('language')
            : Zend_Registry::get(
                'appConfig'
            )['lang']);

        $auth->setBaseUrl($defaultLang . '/sign-in/?redirect=');
        $front->registerPlugin($auth);
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