<?php

/**
 *
 * Page Service class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 *
 * @property Core_Image_Collection_Images $images
 */
class Core_Image_Container extends Core_Model_Super
{
    protected $_data
        = [
            'imageId'    => null,
            'imageTitle' => null,
            'imageDesc'  => null,
            'images'     => null,
        ];
}