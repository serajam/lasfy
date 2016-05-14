<?php

/**
 * Tags Search form
 *
 * @author Fedor Petryk
 *
 */
class TagSearchForm extends Core_Form
{
    public function init()
    {
        $this->setMethod('GET');
        $this->setAttrib('class', 'form');
        $this->setAttrib('id', 'searchForm');

        $this->addElement(
            'text',
            'searchTags',
            [
                'label'       => __('tagged'),
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