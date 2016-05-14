<?php

/**
 *
 * Front Mapper class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_Mapper_Front extends Core_Mapper_Super
{
    public function getGallery($galleryId = false, $galleryName = false, $all = false)
    {
        if (!$galleryId && !$galleryName) {
            return false;
        }
        $db  = $this->getAdapter();
        $sql = $db->select()
            ->from(['g' => 'Galleries'])
            ->joinInner(['gi' => 'GalleriesImages'], 'gi.galleryId = g.galleryId')
            ->joinLeft(
                ['i' => 'Images'],
                'i.imageId = gi.imageId AND (i.imageSizeType = g.thumbnailSize OR i.imageSizeType = g.fullSize)'
            );
        $galleryId ? $sql->where('g.galleryId = ?', $galleryId) : $sql->where('g.galleryName = ?', $galleryName);
        $rows = $db->fetchAll($sql);

        if (empty($rows)) {
            return false;
        }

        $gallery = new Core_Model_Gallery($rows[0]);

        return $gallery->populateImages($rows);
    }
}