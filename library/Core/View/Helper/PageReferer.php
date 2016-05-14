<?php

/**
 *
 * Domain link class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_View_Helper_PageReferer extends Core_View_Helper_View
{
    /**
     *
     * Return current module base link path
     *
     * @param int $base | if needed to return raw path
     */
    public function pageReferer($addon = null)
    {
        if (isset($_SERVER['HTTP_REFERER'])) {
            $link    = $_SERVER['HTTP_REFERER'];
            $request = new Zend_Controller_Request_Http($link);
            Zend_Controller_Front::getInstance()->getRouter()->route($request);
            $config     = Zend_Registry::get('appConfig');
            $module     = $request->getModuleName();
            $controller = $request->getControllerName();
            $action     = $request->getActionName();
            $pageLink   = $config['baseHttpPath'] . $module . '/'
                . $controller . '/' . $action;
            $pageLink .= $addon;
            $params = $request->getParams();
            if (!empty($params)) {
                unset($params['module']);
                unset($params['controller']);
                unset($params['action']);
                $pageLink .= '?';
                foreach ($params as $param => $value) {
                    $pageLink .= $param . '=' . $value . '&';
                }
            }

            return $pageLink;
        } else {
            return false;
        }
    }
}

?>
