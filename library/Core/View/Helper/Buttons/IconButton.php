<?php

/**
 *
 * Messenger helper class class
 *
 * @author     Fedor Petryk
 * @copyright
 */
class Core_View_Helper_Buttons_IconButton extends Core_View_Helper_Buttons_Link
{
    protected static $_httpPath = null;

    protected static $_baseHttpPath = null;

    public function iconButton($link, $icon, $tip = '', $id = '', $classes = '', $rel = '', $text = '')
    {
        $ahref   = '<a %s %s %s %s><span %s data-tooltip %s>%s</span>%s</a>';
        $allowed = $this->isAllowed($link);

        if (!$allowed) {
            return false;
        }

        $tipClass = '';
        if (!empty($tip)) {
            $tipClass = 'class="has-tip"';
        }

        $id       = $id ? ('id="' . $id . '"') : '';
        $tip      = $tip ? ('title="' . $tip . '"') : '';
        $link     = $link ? ('href="' . $link . '"') : '';
        $classes  = $classes ? ('class="' . trim($classes) . '"') : '';
        $rel      = $rel ? ('rel="' . $rel . '"') : '';
        $icon     = '<i class="fi-' . $icon . '"></i>';
        $template = '<li>' . sprintf($ahref, $id, $link, $classes, $rel, $tip, $tipClass, $icon, $text) . '</li>';

        return $template;
    }
}