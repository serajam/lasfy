<?php

/**
 *
 * Appending head links to layouts class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_View_Helper_Html_ModalMessages extends Core_View_Helper_View
{
    public function modalMessages()
    {
        $error = $this->view->reveal(
            '<div class="message"></div>',
            'error-message-window',
            'small'
        );

        $notice = $this->view->reveal(
            '<div class="message"></div>',
            'success-message-window',
            'small'
        );

        return $error . $notice;
    }
}
