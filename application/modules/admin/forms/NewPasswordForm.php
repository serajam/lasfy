<?php

/**
 *
 * User form class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class NewPasswordForm extends Core_Form
{
    public function init()
    {
        $this->setAttrib('class', 'form');
        $this->setAttrib('id', 'edit_password');

        $this->addElement(
            'text',
            'password',
            [
                'label'      => __('password'),
                'required'   => true,
                'validators' => [
                    ['StringLength', false, [4, 16]]
                ]
            ]
        );
        $this->addSubmit('save');
    }
}