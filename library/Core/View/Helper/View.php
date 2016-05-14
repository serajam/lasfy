<?php

/**
 *
 * Base view initializer class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_View_Helper_View
{
    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }
}