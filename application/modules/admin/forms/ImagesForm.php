<?php

/**
 *
 * Images form class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class ImagesForm extends Core_Form
{
    public function init()
    {
        $this->setAttrib('class', 'form custom');
        $this->setAttrib('id', 'gallery-form');

        $this->addElement(
            'text',
            'image',
            [
                'required'    => false,
                'placeholder' => __('give_a_word'),
                'validators'  => ['int']
            ]
        );

        $this->addSubmit('save');
    }
}