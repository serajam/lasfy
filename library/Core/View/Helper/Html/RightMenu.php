<?php

/**
 * Appending head links to layouts class
 *
 * @author     Fedor Petryk
 * @copyright
 */
class Core_View_Helper_Html_RightMenu extends Core_View_Helper_View
{
    public function rightMenu()
    {
        $html = '<ul class="right"><li class="divider"></li>';
        $host = $this->view->domainLink(1);
        $html
            .= '<li><a href="' . $host . '">' . __('website') . '</a></li><li class="divider"></li>';

        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $html .= '<li class="has-form"><a class="button" href="' . $host . 'login/">'
                . __('sign_in') . '</a></li><li class="divider"></li>';
        } else {
            $html
                .= '<li><a href="' . $host . 'logout/' . '">' . __('sign_out') . '</a></li>';
        }
        $html .= '</ul>';

        return $html;
    }
}