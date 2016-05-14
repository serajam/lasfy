<?php

/**
 *
 * Role row class
 *
 * @author     Fedor Petryk
 *
 * @property int    $categoryId
 * @property int    $lang
 * @property string $categoryName
 */
class Pages_Category extends Core_Model_Super
{
    protected $_data
        = [
            'categoryId'   => null,
            'lang'         => null,
            'categoryName' => null,
        ];
}