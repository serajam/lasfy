<?php

/**
 *
 * Messenger helper class class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_View_Helper_Client_PageTitle extends Core_View_Helper_View
{
    /**
     * Return current page title
     */
    public function pageTitle($append = false, $titleOther = false)
    {
        $title = $this->_getTitle();
        if ($append != false) {
            $title .= $append;
        }
        $this->view->headTitle($title);

        return '<h1 class="headerText">' . ($titleOther ? $titleOther : $title) . '</h1>';
    }

    protected function _getTitle()
    {
        $front    = Zend_Controller_Front::getInstance();
        $curRes   = $front->getRequest()->getControllerName();
        $curModel = $front->getRequest()->getModuleName();
        $curAct   = $front->getRequest()->getActionName();
        $curSys   = $curModel . ':' . $curRes;
        $acl      = Zend_Registry::get('acl');

        foreach ($acl as $a) {
            if ($a['resourceCode'] == $curSys
                && $a['action'] == $curAct
            ) {
                return $this->view->translation($a['rightName']);
            }
        }
    }
}