<?php

/**
 *
 * Page Service class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_Image_Collection_Containers extends Core_Collection_Super
{
    protected $_domainObjectClass = 'Core_Image_Container';

    /**
     * @return array
     */
    public function toArray()
    {
        $data = [];
        foreach ($this as $container) {
            $images                    = $container->images;
            $data[$container->imageId] = [
                'imageTitle'   => $container->imageTitle,
                'imageDesc'    => $container->imageDesc,
                'images'       => [
                    'small' => $images->current()->imageName,
                    'full'  => $images->next()->imageName
                ],
                'compressions' => $images->getCompressions()
            ];
        }

        return $data;
    }

    /**
     * @param $results
     *
     * @return Core_Image_Collection_Containers|bool
     */
    public function populate($results)
    {
        if (empty($results) && count($results)) {
            return false;
        }
        foreach ($results as $data) {
            if (is_object($data)) {
                $data = $data->toArray();
            }

            if (!array_key_exists('imageId', $data)) {
                continue;
            }

            $container = $this->getContainer($data['imageId']);
            if (!$container) {
                $model             = new $this->_domainObjectClass;
                $model->imageId    = $data['imageId'];
                $model->imageTitle = $data['imageTitle'];
                $model->imageDesc  = $data['imageDesc'];
                $model->images     = new Core_Image_Collection_Images([$data]);
                $this->add($model);
                continue;
            }
            $container->images->add($data);
        }

        return $this;
    }

    /**
     * @param int $imageId
     *
     * @return Core_Image_Container
     */
    public function getContainer($imageId)
    {
        foreach ($this as $cont) {
            if ($cont->imageId == $imageId) {
                return $cont;
            }
        }

        return false;
    }
}