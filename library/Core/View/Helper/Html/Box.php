<?php

/**
 *
 * Messenger helper class class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_View_Helper_Html_Box extends Core_View_Helper_View
{
    /**
     * @var Core_Form
     */
    public function box($content, $title = false, $width = '97%')
    {
        $html = '<div class="box ui-corner-all" %s>';
        if (!is_null($width)) {
            $html = sprintf($html, 'style="width:' . $width . '"');
        } else {
            $html = sprintf($html, 'style="position:relative; float:left;"');
        }
        if ($title) {
            $html .= '<h2>' . $this->view->translation($title) . '</h2>';
        }
        $html .= '<hr class="divider">';
        $html .= '<div class="box-content">' . $content . '</div>';
        $html .= '</div>';

        return $html;
    }
}

?>