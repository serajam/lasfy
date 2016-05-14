<?php

/**
 *
 * Menu form class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class GalleryForm extends Core_Form
{
    public function init()
    {
        $this->setAttrib('class', 'form custom');
        $this->setAttrib('id', 'gallery-form');

        $this->addElement(
            'hidden',
            'galleryId',
            [
                'validators' => ['int']
            ]
        );

        /*   $this->addElement(
               'select',
               'parentId',
               array(
                    'required'   => false,
                    'label'      => __('root_gallery'),
                    'validators' => array('int')
               )
           );*/

        $this->addElement(
            'text',
            'galleryName',
            [
                'placeholder' => __('galleryName'),
                'required'    => true,
                'validators'  => [
                    ['StringLength', false, [4, 150]],
                ]
            ]
        );

        $this->addElement(
            'select',
            'thumbnailSize',
            [
                'filters'  => ['StringTrim', 'StripTags'],
                'required' => true,
                'label'    => __('thumbnailSize'),
            ]
        );

        $this->addElement(
            'select',
            'fullSize',
            [
                'filters'  => ['StringTrim', 'StripTags'],
                'required' => true,
                'label'    => __('fullSize'),
            ]
        );

        $this->addSubmit('save');
    }
}