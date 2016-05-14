<?php

/**
 *
 * Domain link class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_View_Helper_PageLink extends Core_View_Helper_View
{
    /**
     *
     * Return current module base link path
     *
     * @param int $base | if needed to return raw path
     */
    public function pageLink()
    {
        $config          = Zend_Registry::get('appConfig');
        $configModule    = Zend_Registry::get('config');
        $frontController = Zend_Controller_Front::getInstance();
        $request         = $frontController->getRequest();
        $controller      = $request->getControllerName();
        $action          = $request->getActionName();

        $pageLink = $config['baseHttpPath'] . $configModule['httpPath']
            . $controller . '/' . $action;

        return $pageLink;
    }
}

?>