<?php

/**
 *
 * Menu form class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class ChildGalleryForm extends Core_Form
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

        $this->addElement(
            'hidden',
            'parentId',
            [
                'required'   => false,
                'validators' => ['int']
            ]
        );

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

        $this->addSubmit('save');
    }
}