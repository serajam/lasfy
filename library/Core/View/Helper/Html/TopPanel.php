<?php

/**
 * Appending head links to layouts class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_View_Helper_Html_TopPanel extends Core_View_Helper_View
{
    public function topPanel($slugs = null)
    {
        $conf = Zend_Registry::get('appConfig');

        if (isset($conf['logo']) && !empty($conf['logo'])) {
            $img = '<img src="' . $this->view->domainLink(1) . $conf['logo'] . '" alt="" />';
        } else {
            $img = '<img src="' . $this->view->domainLink(1) . 'images/logist_pro.png" alt="" />';
        }

        $session      = '';
        $instructions = '';
        $user         = Core_Model_User::getInstance();
        if ($user->timeout > 0) {
            $session = '<div class="session-info">' .
                '<div class="block">' . __('session_ends_in') . '</div>' .
                '<div id="session_timer"></div>' .
                '<div class="renew">' .
                '<a href="#" id="session_renew_button">' . __('renew_session') . '</a>' .
                '</div>' .
                '</div>';
        }

        return '<div class="b-header">' .
        '<div class="b-logo">' .
        '<a href="' . $this->view->domainLink(1) . '">' . $img . '</a>' .
        '</div>' .
        $this->buildMenu($slugs) . ' ' . $this->view->user() . $session .
        '<div class="clear"></div>' .
        '</div>';
    }

    /**
     * build header menu
     */
    protected function buildMenu($slugs)
    {
        $html = '<div class="b-menu">';
        $html .= '<ul class="sf-menu">';

        $acl = Zend_Registry::get('acl');
        if (empty($acl)) {
            return false;
        }

        $normalizedAcl = (Core_Controller_Plugin_AclNormalizer::normalize($acl));
        if (empty($normalizedAcl)) {
            return false;
        }

        $frontController = Zend_Controller_Front::getInstance();
        $request         = $frontController->getRequest();
        $currentModule   = $request->getModuleName();
        $parent_id       = null;
        if (array_key_exists($currentModule, $normalizedAcl)) {
            if (array_key_exists('parentId', $normalizedAcl[$currentModule])) {
                $parent_id = $normalizedAcl[$currentModule]['parentId'];
            }
        }

        $menuItemsId = 0;
        foreach ($normalizedAcl as $module => $Resources) {
            if (!array_key_exists('show', $Resources) || $Resources['show'] == 0) {
                continue;
            }

            $menuItemsId++;

            if (!empty($Resources['Resources'])) {

                $active = '';
                if ($currentModule == $module || $Resources['id'] == $parent_id) {
                    $active = 'active';
                }

                if (count($Resources['Resources']) > 0) {
                    $isShowable = false;
                    $menuItem
                                = '<li>
                        <a class="top ' . $active . '" href="#"><span>'
                        . __($Resources['moduleName']) . '</span></a>';

                    $menuItem .= '<div><ul>';
                    foreach ($Resources['Resources'] as $controller => $action) {
                        foreach ($action as $val) {
                            if ($val['menu'] == 1) {
                                $link = $this->view->domainLink(1)
                                    . $module . '/' . $controller . '/' . $val['action'];
                                $menuItem .= '<li><a href = "' . $link . '">'
                                    . __($val['name']) .
                                    '</a></li>';
                                $isShowable = true;
                            }
                        }
                    }
                    $menuItem .= '</ul></div>';
                    $menuItem .= '</li>';

                    if ($isShowable) {
                        $html .= $menuItem;
                    }
                }
            }
        }

        if (!empty($slugs) && count($slugs) > 0) {
            foreach ($slugs as $slug) {
                $menuItem
                    = '<li><a class="top" href="/page/' . $slug['slug'] . '"><span>'
                    . __($slug['slug']) . '</span></a>';
                $menuItem .= '</li>';
                $html .= $menuItem;
            }
        }

        $html .= '</ul>';
        $html .= '</div>';

        return $html;
    }
}