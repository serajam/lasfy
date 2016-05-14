<?php

/**
 *
 * Page row class
 *
 * @author     Aleksey Kagarlykskiy
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Page extends Core_Model_Super
{
    protected $_data
        = [
            'pageId'          => null,
            'slug'            => null,
            'type'            => null,
            'title'           => null,
            'pageContent'     => null,
            'shortContent'    => null,
            'dateCreated'     => null,
            'createDate'      => null,
            'metaKeywords'    => null,
            'metaDescription' => null,
            'metaTitle'       => null,
            'lang'            => null,
            'isDefaultPage'   => 0
        ];
}