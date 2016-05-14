<?php

/**
 *
 * Messenger helper class class
 *
 * @author     Fedor Petryk
 * @copyright
 */
class Core_View_Helper_Buttons_ButtonsList
{
    /**
     *
     * Return current module base link path
     *
     * @param int $link | if needed to return raw path
     */
    public function buttonsList($buttonList)
    {
        if (empty($buttonList)) {
            return false;
        }
        $list = '<ul class="button-group">';
        foreach ($buttonList as $button) {
            $list .= '<li>' . $button . '</li>';
        }
        $list .= '</ul>';

        return $list;
    }
}