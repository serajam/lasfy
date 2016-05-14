<?php

/**
 *
 * Messenger helper class class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_View_Helper_PageInfo extends Core_View_Helper_Html_PageTitle
{
    /**
     * Return current page title
     */
    public function pageInfo($info)
    {
        $title = parent::_getTitle();
        if (empty($info)) {
            return $title;
        }
        $this->view->headTitle($title);

        return '<div class="b-name">
            <h2>' . $title . '</h2>
            <span>' . $info . '</span>
          </div>';
    }
}

?>