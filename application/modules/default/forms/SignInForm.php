<?php

/**
 * SignIn form
 *
 * @author     : Aleksey Kagarlykskiy
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 *
 */
class SignInForm extends Core_Form
{
    public function init()
    {
        $this->setAttrib('class', 'form');
        $this->setAttrib('id', 'signInForm');

        $this->addElement(
            'text',
            'login',
            [
                'placeholder' => __('email'),
                'required'    => true,
                'filters'     => ['StringTrim', 'StripTags'],
                'validators'  => [
                    'EmailAddress',
                    ['StringLength', false, [6, 100]],
                ]
            ]
        );

        $this->addElement(
            'password',
            'password',
            [
                'placeholder' => __('password'),
                'required'    => true,
                'filters'     => ['StringTrim', 'StripTags'],
                'validators'  => [
                    ['StringLength', false, [8, 15]],
                ]
            ]
        );

        $lang = Zend_Registry::get('language');
        $this->addElement(
            'note',
            'forgotPassword',
            [
                'value'      => __('forgotPassword'),
                'decorators' => [
                    ['ViewHelper'],
                    [
                        'HtmlTag',
                        [
                            'tag'  => 'a',
                            'href' => '/' . $lang . '/forgot-password'
                        ]
                    ],

                ]
            ]
        );

        $this->addSubmit('signIn', 'sub', 'button success signIn');

        $this->addDisplayGroup(
            ['login', 'password', 'sub', 'forgotPassword'],
            'signInGroup',
            ['disableLoadDefaultDecorators' => true]
        );
        $this->getDisplayGroup('signInGroup')
            ->addDecorators(
                [
                    'FormElements',
                    'Fieldset'
                ]
            );
    }
}