<?php

/**
 * Get user's interface translation
 *
 * @author     Aleksey Kagatlykskiy
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_View_Helper_Translation extends Core_View_Helper_View
{
    public function translation($code)
    {
        return $this->view->escape(
            Zend_Registry::isRegistered('translation') ? Zend_Registry::get('translation')->get($code) : $code
        );
    }
}