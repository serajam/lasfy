<?php

/**
 * Extended partial loopper
 *
 * @author     Petryk Fedor
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Admin_View_Helper_Search
{
    public $view;

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    /**
     * Core_Form
     */
    public function search($form)
    {
        $html = '<div class="adv-search-form">';
        $html .= '<h1>' . $this->view->translation('advanced_search') . '</h1>';
        $html .= $form . '<div class="clear"></div></div>';

        return $html;
    }
}
