<?php

/**
 * Modal dialog cunstructer
 *
 * @author     Petryk Fedor
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_View_Helper_Html_ModalDialog
{
    /**
     *
     * Build dialog div
     *
     * @param String $body | dialog text
     * @param String $id | dialog id
     * @param String $title | dialog title
     * @param String $class | dialog class
     *
     * @return String
     */
    public function modalDialog($body, $id = 'dialog', $title = 'Message', $class = '')
    {
        $dialog = '<div id="' . $id . '" title="' . $title . '" class="modal-dialogue ' . $class . '">
                    <p class="errors"></p><div class="clear"></div><div class="dialog-body">';
        $dialog .= $body;
        $dialog .= '</div><div class="clear"></div></div>';

        return $dialog;
    }
}