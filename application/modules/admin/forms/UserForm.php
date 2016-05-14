<?php

/**
 *
 * User form class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class UserForm extends Core_Form
{
    public function init()
    {
        $this->setAttrib('class', 'form');
        $this->setAttrib('id', 'user');

        $this->addElement(
            'hidden',
            'userId',
            [
                'required'   => true,
                'validators' => ['int']
            ]
        );

        $this->addElement(
            'text',
            'login',
            [
                'label'      => __('login'),
                'required'   => true,
                'validators' => [
                    ['StringLength', false, [5, 55]],
                    ['Unique', false, ['Users', 'login', 'userId', $this]],
                ]
            ]
        );

        $this->addElement(
            'text',
            'userName',
            [
                'label'      => __('name'),
                'required'   => true,
                'validators' => [
                    ['StringLength', false, [3, 55]],
                ]
            ]
        );

        $this->addElement(
            'text',
            'email',
            [
                'label'      => __('email'),
                'required'   => true,
                'validators' => [
                    'EmailAddress',
                    ['Unique', false, ['Users', 'email', 'userId', $this]],
                    ['StringLength', false, [5, 100]],
                ]
            ]
        );
        $this->addSubmit('save');
    }
}