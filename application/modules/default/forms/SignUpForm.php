<?php

/**
 * Registration form
 *
 * @author     : Aleksey Kagarlykskiy
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 *
 */
class SignUpForm extends Core_Form
{
    public function init()
    {
        $this->setAttrib('class', 'form');
        $this->setAttrib('id', 'signUpForm');

        $this->addElement(
            'text',
            'userName',
            [
                'placeholder' => __('userName'),
                'required'    => true,
                'filters'     => ['StringTrim', 'StripTags'],
                'validators'  => [
                    ['StringLength', false, [3, 100]],
                ]
            ]
        );

        $this->addElement(
            'text',
            'login',
            [
                'placeholder' => __('email'),
                'required'    => true,
                'filters'     => ['StringTrim', 'StripTags'],
                'validators'  => [
                    'EmailAddress',
                    ['UniqueEmail', false, ['Users', 'email']],
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

        $this->addCaptcha();

        $this->addElement(
            'checkbox',
            'agreement',
            [
                'label'          => __('agreement_preview') . ' <a href="agreement.html" target=_blank>' . __(
                        'more_details'
                    ) . '</a>',
                'required'       => true,
                'checked'        => 'checked',
                'uncheckedValue' => null,
                'class'          => 'labelInline',
                'validators'     => [
                    'int',
                ],
                'decorators'     => [
                    ['ViewHelper'],
                    [
                        'Label',
                        [
                            'placement' => 'APPEND',
                            'escape'    => false
                        ]
                    ],
                    ['Errors'],
                    ['FormElements'],
                    [
                        ['data' => 'HtmlTag'],
                        ['class' => 'element']
                    ],
                    [
                        ['row' => 'HtmlTag'],
                        ['tag' => 'li']
                    ]
                ]
            ]
        );

        $this->addSubmit('signUp', 'sub', 'button signUp');

        $this->addDisplayGroup(
            ['login', 'password', 'repeatPassword', 'agreement', 'captchaCode', 'sub'],
            'signUpGroup',
            ['disableLoadDefaultDecorators' => true]
        );
        $this->getDisplayGroup('signUpGroup')
            ->addDecorators(
                [
                    'FormElements',
                    'Fieldset',
                ]
            );
    }

    protected function _addFilters()
    {
    }
}