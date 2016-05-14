<?php

/**
 *
 * Messenger helper class class
 *
 * @author     Fedor Petryk
 * @copyright
 */
class Core_View_Helper_Buttons_ButtonLink extends Core_View_Helper_Buttons_Link
{
    protected $_buttonsTypes
        = [
            1 => 'tiny',
            2 => 'small',
        ];

    public function buttonLink($link, $name, $id = '', $classes = '', $blank = false, $rel = '', $type = false)
    {
        $allowed = $this->isAllowed($link);
        if (!$allowed) {
            return false;
        }

        if ($blank == true) {
            $blank = ' target=_blank ';
        }

        if ($type) {
            $classes .= ' ' . $this->_buttonsTypes[$type];
        }

        if (!empty($link)) {
            $link = ' href="' . $link . '"';
        }
        $template = '<a ' . (!empty($rel) ? 'rel="' . $rel . '"' : ' ') . $id . $blank
            . $link . ' class="button '
            . $classes . '">
			' . $name . '</a>';

        return $template;
    }
}

?>