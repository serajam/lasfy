<?php

/**
 * Created by JetBrains PhpStorm.
 * User: Voronoy
 * Date: 09.09.13
 * Time: 21:43
 * To change this template use File | Settings | File Templates.
 */
class Core_Image_Collection_Images extends Core_Collection_Super
{
    protected $_domainObjectClass = 'Core_Image_Image';

    public function getImageBySize($size)
    {
        foreach ($this as $i) {
            if ($i->imageSizeType == $size) {
                return $i;
            }
        }
    }

    public function getSameImages()
    {
        $names = [];
        foreach ($this as $image) {
            $names[$image->name] = $image;
        }

        return $names;
    }

    public function getCompressions($skipDefault = true)
    {
        $this->rewind();
        $compressions = [];
        foreach ($this as $image) {
            if ($skipDefault == true && $image->imageSizeType == Core_Image_Manager::DEFAULT_SIZE) {
                continue;
            }
            $compressions[$image->imageSizeType] = $image->compression;
        }

        return $compressions;
    }

    public function sort()
    {
        $collection   = new self;
        $arraySameIds = [];
        foreach ($this as $image) {
            $arraySameIds[$image->imageId][] = $image;
        }
        foreach ($arraySameIds as $arr) {
            //$collection->add(array_pop($arr));
            $collection->add(array_shift($arr));
        }

        return $collection;
    }
}