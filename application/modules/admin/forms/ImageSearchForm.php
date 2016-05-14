<?php

/**
 *
 * Images form class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class ImageSearchForm extends Core_Form
{
    public function init()
    {
        $this->setAttrib('class', 'form custom');
        $this->setAttrib('id', 'gallery-form');

        $this->addElement(
            'text',
            'name',
            [
                'required'    => false,
                'placeholder' => __('image_name_or_empty_for_all'),
                'validators'  => ['string']
            ]
        );

        $this->addSubmit('show');
    }
}