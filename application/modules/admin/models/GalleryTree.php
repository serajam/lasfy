<?php

/**
 * Class GalleryTree
 */
class GalleryTree
{
    protected $_tree;

    public function __construct($galleries)
    {
        $this->_buildTree($galleries);
    }

    protected function _buildTree($galleries)
    {
        $tree = [];
        foreach ($galleries as $g) {
        }
    }
}