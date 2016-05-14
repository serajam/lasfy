<?php

/**
 * Appending head links to layouts class
 *
 * @author     Fedor Petryk
 * @copyright
 */
class Core_View_Helper_Html_LeftMenu extends Core_View_Helper_View
{
    public function leftMenu()
    {
        return $this->_buildMenu();
    }

    /**
     * build header menu
     */
    protected function _buildMenu()
    {
        $html = '<ul class="left">';
        $acl  = Zend_Registry::get('acl');
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

        $menuitemsId = 0;
        foreach ($normalizedAcl as $module => $Resources) {
            if (!array_key_exists('show', $Resources) || $Resources['show'] == 0) {
                continue;
            }
            $menuItem = '<li class="divider"></li>';
            $menuitemsId++;

            if (!empty($Resources['Resources'])) {

                $active = '';
                if ($currentModule == $module || $Resources['moduleId'] == $parent_id) {
                    $active = 'active';
                }

                if (count($Resources['Resources']) > 0) {
                    $isShowable = false;
                    $menuItem
                        .= '<li class="has-dropdown">
                        <a class="' . $active . '" href="#">'
                        . __($Resources['moduleName']) . '</a>';

                    $menuItem .= '<ul class="dropdown">';
                    foreach ($Resources['Resources'] as $controller => $action) {
                        foreach ($action as $val) {
                            if ((int)$val['menu'] == 1) {
                                $link = $this->view->domainLink(1)
                                    . $module . '/' . $controller . '/' . $val['action'];
                                $menuItem .= '<li><a href = "' . $link . '">'
                                    . __($val['name']) .
                                    '</a></li>';
                                $isShowable = true;
                            }
                        }
                    }
                    $menuItem .= '</ul>';
                    $menuItem .= '</li>';

                    if ($isShowable) {
                        $html .= $menuItem;
                    }
                }
            }
        }

        $html .= '</ul>';
        $html .= '</div>';

        return $html;
    }
}