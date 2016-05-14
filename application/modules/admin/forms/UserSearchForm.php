<?php

/**
 * Tags Search form
 *
 * @author Fedor Petryk
 *
 */
class UserSearchForm extends Core_Form
{
    public function init()
    {
        $this->setMethod('GET');
        $this->setAttrib('class', 'form');
        $this->setAttrib('id', 'searchForm');

        $this->addElement(
            'text',
            'email',
            [
                'label'       => __('email'),
                'placeholder' => __('tags'),
                'required'    => false,
                'filters'     => ['StringTrim', 'StripTags'],
                'validators'  => [
                    ['StringLength', false, [2, 1000]],
                ],
                'class'       => 'searchTagsSet',
                'decorators'  => [
                    'ViewHelper',
                    'Errors',
                    'FormElements',
                    [
                        ['data' => 'HtmlTag'],
                        ['class' => 'tagsField']
                    ],
                    [
                        ['row' => 'HtmlTag'],
                        ['tag' => 'div', 'class' => 'small-8 columns']
                    ]
                ]
            ]
        );

        $this->addElement(
            'text',
            'name',
            [
                'label'       => __('name'),
                'placeholder' => __('tags'),
                'required'    => false,
                'filters'     => ['StringTrim', 'StripTags'],
                'validators'  => [
                    ['StringLength', false, [2, 1000]],
                ],
                'class'       => 'searchTagsSet',
                'decorators'  => [
                    'ViewHelper',
                    'Errors',
                    'FormElements',
                    [
                        ['data' => 'HtmlTag'],
                        ['class' => 'tagsField']
                    ],
                    [
                        ['row' => 'HtmlTag'],
                        ['tag' => 'div', 'class' => 'small-8 columns']
                    ]
                ]
            ]
        );

        $this->addElement(
            'submit',
            'search',
            [
                'label'      => __('search'),
                'required'   => false,
                'class'      => 'postfix small button',
                'decorators' => [
                    'ViewHelper',
                    'Errors',
                    'FormElements',
                    [
                        ['data' => 'HtmlTag'],
                        ['class' => 'tagsField']
                    ],
                    [
                        ['row' => 'HtmlTag'],
                        ['tag' => 'div', 'class' => 'small-4 columns']
                    ]
                ]
            ]
        );

        $this->getElement('search')->removeDecorator('label');
        $this->setDecorators(
            [
                'FormElements',
                [
                    ['data' => 'HtmlTag'],
                    ['tag' => 'div', 'class' => 'row collapse'],
                ],
                'Form'
            ]
        );
    }
}