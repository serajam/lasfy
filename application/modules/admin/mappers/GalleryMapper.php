<?php

/**
 * Created by JetBrains PhpStorm.
 * User: Voronoy
 * Date: 29.08.13
 * Time: 20:48
 * To change this template use File | Settings | File Templates.
 */
class GalleryMapper extends Core_Mapper_Super
{
    protected $_tableName = "ImagesTable";

    protected $_rowClass = 'Images';

    public function addGalleryImages($galleryId, $images)
    {
        $db     = $this->getAdapter();
        $maxPos = (int)$db->fetchOne(
            $db->select()->from('GalleriesImages', ['MAX(position)'])->where('galleryId = ?', $galleryId)
        );
        $db->beginTransaction();
        try {
            foreach ($images as $id => $type) {
                $db->insert(
                    'GalleriesImages',
                    [
                        'position'  => ++$maxPos,
                        'galleryId' => $galleryId,
                        'imageId'   => $id
                    ]
                );
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();

            return $e->getMessage();
        }

        return true;
    }

    function getGallery($id)
    {
        $db  = $this->getAdapter();
        $sql = $db->select()
            ->from(['g' => 'Galleries'])
            ->order('parentId ASC')
            ->where('galleryId = ?', $id);
        $row = $db->fetchRow($sql);

        if (!empty($row)) {
            $model = new Gallery($row);

            return $model;
        }

        return false;
    }

    public function getRootGalleryPairs()
    {
        $db        = $this->getAdapter();
        $sql       = $db->select()->from('Galleries', ['galleryId', 'galleryName'])->order('parentId ASC')
            ->where('parentId IS NULL');
        $galleries = $db->fetchPairs($sql);
        if (empty($galleries)) {
            return [];
        }

        return $galleries;
    }

    public function getGalleries($parentId)
    {
        $db        = $this->getAdapter();
        $sql       = $db->select()->from('Galleries', ['galleryId', 'galleryName'])
            ->order('parentId ASC')
            ->where('parentId <> ?', $parentId);
        $galleries = $db->fetchPairs($sql);
        if (empty($galleries)) {
            return [];
        }

        return $galleries;
    }

    public function insertImages($collection, $data)
    {
        $lastId = $this->getMaximumValue('imageId');
        $groups = $collection->getSameImages();
        $db     = $this->getAdapter();
        $db->beginTransaction();
        foreach ($groups as $key => $group) {
            $lastId++;
            foreach ($collection as $image) {
                if ($image->name == $key) {
                    $info = [
                        'imageId'    => $lastId,
                        'imageTitle' => $data['imageTitle'],
                        'imageDesc'  => $data['imageDesc'],
                    ];
                    $info += $image->toArray();
                    $db->insert('Images', $info);
                }
            }
        }
        try {
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();

            return $e->getMessage();
        }

        return true;
    }

    public function deleteImage($imageId)
    {
        $db = $this->getAdapter();

        return $db->delete('Images', ['imageId = ?' => $imageId]);
    }

    public function deleteGalleryImage($imageId, $galleryId)
    {
        $db = $this->getAdapter();

        return $db->delete('GalleriesImages', ['imageId = ?' => $imageId, 'galleryId = ?' => $galleryId]);
    }

    public function getImages($imageId = false, $word = false)
    {
        $db  = $this->getAdapter();
        $sql = $db->select()->from('Images');
        $sql->order('created DESC');
        $sql->order('compression ASC');
        if ($imageId > 0) {
            $sql->where('imageId = ?', $imageId);
        }
        if ($word) {
            $sql->where('imageTitle LIKE ("%' . $word . '%")');
            $sql->orWhere('imageDesc LIKE ("%' . $word . '%")');
        }
        $result = $db->fetchAll($sql);
        if (!$result) {
            return false;
        }
        $container = new Core_Image_Collection_Containers();

        return $container->populate($result);
    }

    /**
     * @param      $word
     * @param bool $galleryId
     *
     * @return bool|Core_Image_Collection_Containers
     */
    public function filterImages($word, $galleryId = false)
    {
        $db  = $this->getAdapter();
        $sql = $db->select()->from(['i' => 'Images']);
        $sql->order('i.created DESC');
        $sql->order('compression ASC');
        if ($word) {
            $sql->where('i.imageTitle LIKE ("%' . $word . '%")');
        }

        if ($galleryId > 0) {
            $gallerySizes = $db->fetchRow(
                $db->select()->from('Galleries', ['fullSize'])->where(
                    'galleryId = ?',
                    $galleryId
                )
            );
            $sizes        = $gallerySizes + [Core_Image_Manager::DEFAULT_SIZE];
            $sql->joinLeft(['gi' => 'GalleriesImages'], 'i.imageId = gi.imageId', []);
            $sql->where('gi.imageId IS NULL');
            $sql->where('i.imageSizeType IN (?)', $sizes);
        }

        $result = $db->fetchAll($sql);
        if (!$result) {
            return false;
        }

        $imgTypes = [];
        foreach ($result as $r) {
            if (!array_key_exists($r['imageId'], $imgTypes)) {
                $imgTypes[$r['imageId']] = 1;
            } else {
                $imgTypes[$r['imageId']]++;
            }
        }

        foreach ($imgTypes as $imgId => $it) {
            if ($it < 2) {
                foreach ($result as $key => $r) {
                    if ($r['imageId'] == $imgId) {
                        unset($result[$key]);
                    }
                }
            }
        }

        $container = new Core_Image_Collection_Containers();

        return $container->populate($result);
    }

    /**
     * @param bool $galleryId
     *
     * @return bool|Core_Image_Collection_Containers
     */
    public function getGalleryImages($galleryId)
    {
        $db           = $this->getAdapter();
        $sql          = $db->select()->from(['gi' => 'GalleriesImages'])
            ->joinLeft(['i' => 'Images'], 'gi.imageId = i.imageId')
            ->where('gi.galleryId = ?', $galleryId);
        $gallerySizes = $db->fetchRow(
            $db->select()->from('Galleries', ['fullSize'])->where(
                'galleryId = ?',
                $galleryId
            )
        );
        $sizes        = $gallerySizes + [Core_Image_Manager::DEFAULT_SIZE];
        $sql->where('i.imageSizeType IN (?)', $sizes);
        $result = $db->fetchAll($sql);
        if (!$result) {
            return false;
        }
        $container = new Core_Image_Collection_Containers();

        return $container->populate($result);
    }

    /**
     * @param int   $galleryId
     * @param array $data
     *
     * @return bool
     */
    public function sortGalleryImages($galleryId, $data)
    {
        $db = $this->getAdapter();
        $db->beginTransaction();
        foreach ($data as $position => $imageId) {
            $where = [
                'galleryId = ?' => $galleryId,
                'imageId = ?'   => $imageId
            ];

            $db->update('GalleriesImages', ['position' => $position + 1], $where);
        }
        try {
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();

            return $e->getMessage();
        }

        return true;
    }
}