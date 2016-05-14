<?php

/**
 *
 * NewUser form class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class CreateUserForm extends Core_Form
{
    public function init()
    {
        $this->setAttrib('class', 'form');
        $this->setAttrib('id', 'new_user');

        $this->addElement(
            'text',
            'login',
            [
                'label'      => __('login'),
                'required'   => true,
                'validators' => [
                    ['StringLength', false, [3, 55]],
                    ['Unique', false, ['Users', 'login']],
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
            'select',
            'roleId',
            [
                'label'      => __('role'),
                'required'   => true,
                'validators' => [
                    'int',
                    ['StringLength', false, [1, 10]]
                ]
            ]
        );

        $this->addElement(
            'password',
            'password',
            [
                'label'      => __('password'),
                'required'   => true,
                'validators' => [
                    ['StringLength', false, [4, 16]]
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
                    ['Unique', false, ['Users', 'email']],
                    ['StringLength', false, [5, 100]],
                ]
            ]
        );
        $this->addSubmit('save');
    }
}