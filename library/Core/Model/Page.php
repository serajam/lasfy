<?php

/**
 *
 * Page row class
 *
 * @author     Aleksey Kagarlykskiy
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_Model_Page extends Core_Model_Super
{
    protected $_data
        = [
            'pageId'          => null,
            'slug'            => null,
            'title'           => null,
            'pageContent'     => null,
            'shortContent'    => null,
            'dateChanged'     => null,
            'dateCreated'     => null,
            'metaKeywords'    => null,
            'metaDescription' => null,
            'metaTitle'       => null,
            'isDefaultPage'   => null,
            'image'           => null,
            'shortDesc'       => null,
            'type'            => null,
            'menuId'          => null,
        ];
}