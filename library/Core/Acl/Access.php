<?php

/**
 * Checking access via ACL to specified url
 *
 * @author     Petryk Fedor
 * @uses       Zend_Acl
 * @package    Core_Acl
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_Acl_Access
{
    /**
     * @param string $link
     *
     * @return bool
     */
    public static function isAllowed($link)
    {
        /** @var Zend_Acl $acl */
        $acl = Zend_Registry::get('aclObject');
        if (!Zend_Uri::check($link)) {
            return true;
        }

        $request    = new Zend_Controller_Request_Http($link);
        $controller = Zend_Controller_Front::getInstance();
        /** @var Zend_Controller_Router_Rewrite $router */
        $router = $controller->getRouter();
        $router->route($request);

        $user         = Core_Model_User::getInstance();
        $resourceName = '';
        $resourceName .= $request->getModuleName() . ':';
        $resourceName .= $request->getControllerName();

        if (!$acl->has($resourceName)) {
            return false;
        }

        if (!$acl->hasRole($user->role)) {
            return false;
        }

        if (!$acl->isAllowed($user->role, $resourceName, $request->getActionName())) {
            return false;
        }

        return true;
    }
}