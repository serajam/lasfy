<?php

/**
 * Foundation Zurb Button Bars helper
 *
 * @author     Petryk Fedor
 * @copyright
 */
class Core_View_Helper_Foundation_ButtonBars extends Core_View_Helper_Buttons_Link
{
    protected $_container
        = '<div class="button-bar">
              <ul class="button-group">%s</ul>
            </div>';

    protected $_row = '<li><a href="%s" id="%s" class="small button" %s>%s</a></li>';

    /**
     * @param $buttons
     *
     * @return bool|string
     */
    public function buttonBars($buttons)
    {
        if (!$buttons) {
            return false;
        }
        $buttonsHtml = '';
        foreach ($buttons as $b) {
            $allowed = $this->isAllowed($b[0]);
            if (!$allowed) {
                continue;
            }
            $buttonsHtml .= vsprintf($this->_row, $b);
        }

        return sprintf($this->_container, $buttonsHtml);
    }
}