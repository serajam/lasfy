<?php

/**
 *
 * Front Service class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_Service_Front extends Core_Service_Super
{
    /**
     * @var Core_Mapper_Front
     */
    protected $_mapper;

    public function getGallery($galleryId = false)
    {
        $galleryName = null;
        // if no id - get default
        if (!$galleryId) {
            $conf        = Core_Settings_Settings::getGroupSettings('main_page');
            $galleryName = $conf['main_page_gallery'];
        }

        return $this->_mapper->getGallery($galleryId, $galleryName);
    }
}