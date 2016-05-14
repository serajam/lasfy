<?php

/**
 * Search company form
 *
 * @author     : Aleksey Kagarlykskiy
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 *
 */
class SearchCompanyForm extends Core_Form
{
    public function init()
    {
        $this->setAttrib('class', 'form');
        $this->setAttrib('id', 'searchCompanyForm');

        $this->addElement(
            'text',
            'searchRequest',
            [
                'placeholder' => __('searchRequest'),
                'required'    => true,
                'filters'     => ['StringTrim', 'StripTags'],
                'validators'  => [
                    ['StringLength', false, [1, 100]],
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

    protected function _addFilters()
    {
    }
}