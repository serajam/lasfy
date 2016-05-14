<?php

/**
 *
 * Role row class
 *
 * @author     Fedor Petryk
 *
 *
 * @property int    $pageId
 * @property string $title
 * @property int    $lang
 * @property int    $categoryId
 * @property int    $pageType
 * @property string pageContent
 * @property string shortContent
 * @property string $slug
 * @property string $metaTitle
 * @property string $metaDescription
 * @property string $metaKeywords
 * @property string $createDate
 */
class Pages_Page extends Core_Model_Super
{
    protected $_data
        = [
            'pageId'          => null,
            'type'            => null,
            'title'           => null,
            'lang'            => null,
            'categoryId'      => null,
            'pageType'        => null,
            'pageContent'     => null,
            'shortContent'    => null,
            'slug'            => null,
            'metaKeywords'    => null,
            'metaDescription' => null,
            'metaTitle'       => null,
            'createDate'      => null,
            'image'           => null,
            'shortDesc'       => null,
        ];
}