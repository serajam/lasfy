<?php

/**
 *
 * Page Service class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_Image_Collection_Galleries extends Core_Collection_Super
{
    protected $_domainObjectClass = 'Core_Image_Gallery';

    public function populate($results)
    {
        if (is_object($results)) {
            //	$results = $results->toArray();
        }

        if (empty($results)) {
            return false;
        }
        foreach ($results as $data) {
            if (is_object($data)) {
                $data = $data->toArray();
            }

            if ($gallery = $this->getModelByKey('galleryId', $data['galleryId'])) {
                $gallery->addImages($data);
            } else {
                $model = new $this->_domainObjectClass;
                $model->populate($data);
                $model->populateImages([$data]);
                $this->add($model);
            }
        }
    }
}