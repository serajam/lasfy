<?php

/**
 *
 * The login form for authentication
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 *
 */
class Core_Form_LoginForm extends Core_Form
{
    public $elementDecorators
        = [
            'ViewHelper',
            'Errors',
            [['data' => 'HtmlTag'], ['class' => 'element']],
            ['Label', ['class' => 'desc']],
            [['row' => 'HtmlTag'], ['tag' => 'li']],
        ];

    public function init()
    {
        $this->setAttrib('class', 'login-form');
        $login = new Zend_Form_Element_Text('email');
        $login->setLabel(Zend_Registry::get('translation')->get('login'))
            ->setRequired(true)
            ->setAttrib('id', '')
            ->setAttrib('class', 'field text full')
            ->addValidator('NotEmpty')
            ->addValidator('EmailAddress');
        $login->setDecorators($this->elementDecorators);

        $pass = new Zend_Form_Element_Password('password');
        $pass->setLabel(Zend_Registry::get('translation')->get('passwd'))
            ->setRequired(true)
            ->setAttrib('class', 'field text full')
            ->addValidator('NotEmpty')
            ->addValidator(
                'Regex',
                true,
                [
                    'pattern'  => '/^[0-9a-z\s\.\,"\'\-\(\)\&\@\#\$\%\^\*]+$/i',
                    'messages' => [
                        'regexNotMatch' => Zend_Registry::get('translation')->get('allow_letters_numbers_symbols') .
                            ' \'"-.,&'
                    ]
                ]

            );
        $pass->setDecorators($this->elementDecorators);
        $submit = new Zend_Form_Element_Submit('sub');
        $submit->setLabel(Zend_Registry::get('translation')->get('enter'));

        $this->addElements([$login, $pass, $submit]);
        $this->sub->setDecorators(
            [
                ['ViewHelper'],
                ['Description'],
                ['HtmlTag', ['tag' => 'div', 'class' => 'submit-group']],
            ]
        );
    }

    public function loadDefaultDecorators()
    {
        $this->setDecorators(
            [
                'FormElements',
                [
                    'HtmlTag',
                    ['tag' => 'ul'],
                    ['class' => 'forms']
                ],
                'Form',
            ]
        );
    }
}