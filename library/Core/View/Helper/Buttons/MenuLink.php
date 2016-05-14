<?php

/**
 *
 * Messenger helper class class
 *
 * @author     Fedor Petryk
 * @copyright
 */
class Core_View_Helper_Buttons_MenuLink extends Core_View_Helper_Buttons_Link
{
    public function menuLink($link, $name)
    {
        $allowed = $this->isAllowed($link);
        if (!$allowed) {
            return false;
        }

        if (!empty($link)) {
            $link = ' href="' . $link . '"';
        }
        $template = '<li><a' . $link . '">' . $name . '</a></li>';

        return $template;
    }
}