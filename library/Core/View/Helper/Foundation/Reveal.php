<?php

/**
 * FoundationZurb Reveal Modal Window helper
 * Class Core_View_Helper_Foundation_Reveal
 *
 * @author Fedor Petryk
 */
class Core_View_Helper_Foundation_Reveal
{
    const REVEAL_WINDOW_TEMPLATE = '<div data-reveal id="%s" class="reveal-modal %s"><div class="dialog-body"><p>%s</p></div>';
    const REVEAL_BUTTON_TEMPLATE = '<p><a href="#" class="button small %s">%s</a></p>';
    const REVEAL_CLOSE_MODAL_TEMPLATE = '<a class="close-reveal-modal">&#215;</a></div>';

    public function reveal($body, $id = 'dialog', $class = '', $button = false)
    {
        $dialog = sprintf(self::REVEAL_WINDOW_TEMPLATE, $id, $class, $body);
        $button ? $dialog .= sprintf(self::REVEAL_BUTTON_TEMPLATE, $button, __($button)) : null;
        $dialog .= self::REVEAL_CLOSE_MODAL_TEMPLATE;

        return $dialog;
    }
}