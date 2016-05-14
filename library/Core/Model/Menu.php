<?php

/**
 * Menu domain class
 *
 * @author     Fedor Petryk
 * @package    Core_Model
 *
 * @property int    $menuId
 * @property int    $parentId
 * @property string $name
 * @property string $type
 * @property string $link
 * @property string $active
 * @property string $contentId
 * @property int    $position
 * @property int    $lang
 * @property string $contentTitle
 * @property string $routeCode
 */
class Core_Model_Menu extends Core_Model_Super
{
    /**
     * @see Core_Model_Super
     */
    protected $_data
        = [
            'menuId'       => null,
            'parentId'     => 0,
            'name'         => null,
            'type'         => null,
            'link'         => null,
            'active'       => null,
            'contentId'    => null,
            'position'     => null,
            'lang'         => null,
            'contentTitle' => null,
            'availability' => null,
            'routeCode'    => null,
        ];
}