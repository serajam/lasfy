<?php

/**
 *
 * Role row class
 *
 * @author     Fedor Petryk
 *
 *
 * @property int    $contentId
 * @property string $$title
 * @property int    $text
 * @property int    $link
 * @property int    $image
 * @property string $type
 * @property string $position
 */
class Core_Model_CustomContent extends Core_Model_Super
{
    protected $_data
        = [
            'contentId' => null,
            'title'     => null,
            'text'      => null,
            'link'      => null,
            'image'     => null,
            'type'      => null,
            'position'  => null,
        ];
}