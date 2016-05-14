<?php

/**
 *
 * Page row class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 *
 * @property int    $companyId
 * @property int    userId
 * @property String $isActiveByUser
 * @property String $name
 * @property String $address
 * @property String $webSite
 * @property String $description
 * @property String $logo
 */
class Company extends Core_Model_Super
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