<?php

/**
 *
 * Messenger helper class class
 *
 * @author     Fedor Petryk
 * @copyright
 */
class Core_View_Helper_Buttons_IconsList
{
    public function iconsList($buttonList)
    {
        if (empty($buttonList)) {
            return false;
        }
        $list = '<div class="button-bar"><ul class="button-group icons-list">';
        foreach ($buttonList as $button) {
            $list .= $button;
        }
        $list .= '</ul></div>';

        return $list;
    }
}