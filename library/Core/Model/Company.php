<?php

/**
 *
 * Company row class
 *
 * @author     Aleksey Kagarlykskiy
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_Model_Company extends Core_Model_Super
{
    protected $_data
        = [
            'companyId'        => null,
            'userId'           => null,
            'isActiveByUser'   => null,
            'name'             => null,
            'address'          => null,
            'telephones'       => null,
            'webSite'          => null,
            'description'      => null,
            'shortDescription' => null,
            'logo'             => null,
        ];
}