<?php

/**
 * Class Core_Model_Gallery
 *
 * @property int                              $galleryId
 * @property int                              $parentId
 * @property string                           $thumbnailSize
 * @property string                           $galleryName
 * @property string                           $fullSize
 * @property Core_Image_Collection_Containers $imagesContainers
 */
class Core_Image_Gallery extends Core_Model_Super
{
    protected $_data
        = [
            'galleryId'        => null,
            'parentId'         => null,
            'galleryName'      => null,
            'thumbnailSize'    => null,
            'fullSize'         => null,
            'imagesContainers' => [],
        ];

    public function addImages($images)
    {
        if ($container = $this->imagesContainers->getModelByKey('imageId', $images['imageId'])) {
            $container->images->populate([$images]);
        } else {
            $this->populateImages([$images]);
        }
    }

    /**
     * @param $images
     *
     * @return $this
     */
    public function populateImages($images)
    {
        $container              = new Core_Image_Collection_Containers();
        $this->imagesContainers = $container->populate($images);

        return $this;
    }

    /**
     * @return Core_Image_Collection_Containers
     */
    public function getContainers()
    {
        return $this->imagesContainers;
    }
}