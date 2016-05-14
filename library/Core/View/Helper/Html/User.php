<?php

/**
 * user menu builder
 * build header menu according to permited usrs rights
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_View_Helper_Html_User extends Core_View_Helper_View
{
    /**
     * build header
     */
    public function user()
    {
        $html = '<div class="user">';
        $host = $this->view->domainLink(1);
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $html .= '<div class="actions">';
            $html .= '<div><a href="' . $host . 'login/">'
                . $this->view->translation('login') . '</a></div>';
            $html .= '<div><a href="' . $host . 'registration/">'
                . $this->view->translation('register') . '</a></div>';
            $html .= '</div>';
        } else {
            $user = Core_Model_User::getInstance();
            $link = $host . 'logout/';

            $html .= '<div class="user-info">';
            $html .= '<div>' . $user->name . '</div>';
            $html .= '</div>';

            $html .= '<div class="actions">';
            $html .= '<div><a href="' . $host . 'profile/informing/inform-history/">'
                . __('new_messages') . ' (' . $user->messages . ') </a></div>';
            $html .= '<div><a href="' . $host . 'profile/settings/index">'
                . $this->view->translation('profile') . '</a></div>';
            $html .= '<div><a href="' . $link . '">'
                . $this->view->translation('logout') . '</a></div>';

            $html .= '</div>';
        }
        $html .= '<div class="clear"></div></div>';

        return $html;
    }
}

?>