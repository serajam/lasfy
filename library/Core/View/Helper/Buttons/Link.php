<?php

/**
 *
 * Messenger helper class class
 *
 * @author     Fedor Petryk
 * @copyright
 */
class Core_View_Helper_Buttons_Link extends Core_View_Helper_View
{
    protected static $_httpPath = null;

    protected static $_baseHttpPath = null;

    /**
     * Return current module base link path
     *
     * @param int    $link | if needed to return raw path
     * @param String $id | link identifier 'id="a1"'
     * @param Srting $classes | classes list
     * @param bool   $blank | open in new window
     */
    public function link($link, $id = '', $classes = '', $blank = false)
    {
        $allowed = $this->isAllowed($link);
        if (!$allowed) {
            return false;
        }

        if ($blank == true) {
            $blank = ' target=_blank ';
        }
    }

    public function isAllowed($link)
    {
        return Core_Acl_Access::isAllowed($link);
    }
}