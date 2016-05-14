<?php

/**
 *
 * Role form class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class TranslationForm extends Core_Form
{
    public function init()
    {
        $this->setAttrib('class', 'form');
        $this->setAttrib('id', 'translation');

        $conf = Zend_Registry::get('languages');

        $this->addElement(
            'text',
            'code',
            [
                'label'      => Zend_Registry::get('translation')->get('code'),
                'required'   => true,
                //   'id' => 'translation-code',
                'validators' => [
                    [
                        'regex',
                        true,
                        [
                            'pattern'  => '/^[0-9a-zA-Z_\-]+$/i',
                            'messages' => [
                                'regexNotMatch' => Zend_Registry::get('translation')->get(
                                        'allow_letters_numbers_symbols'
                                    ) . ' \'"-,.'
                            ]
                        ]
                    ],
                    ['StringLength', false, [0, 500]]
                ]
            ]
        );

        foreach ($conf['languages'] AS $lang => $val) {
            $this->addElement(
                'textarea',
                'caption_' . $lang,
                [
                    'label'      => $val,
                    'required'   => true,
                    'filters'    => ['StringTrim'],
                    'validators' => [
                        [
                            'regex',
                            true,
                            [
                                'pattern'  => '/^[0-9a-z\x80-\xFF��\s\.\,"\'\-\(\)\$\<\>\/%\?]+$/i',
                                'messages' => [
                                    'regexNotMatch' =>
                                        Zend_Registry::get('translation')->get(
                                            'allow_letters_numbers_symbols'
                                        ) .
                                        ' \'"-,.'
                                ]
                            ]
                        ],
                        ['StringLength', false, [0, 500]]
                    ]
                ]
            );
        }

        $this->addSubmit('save');
    }
}