<?php

/**
 * Search form for search process by tags.
 *
 * @author Alexius Kagarlykskiy
 *
 */
class SearchForm extends Core_Form
{
    public function init()
    {
        $this->setAttrib('class', 'form');
        $this->setAttrib('id', 'searchForm');

        $this->addElement(
            'text',
            'searchTags',
            [
                'label'       => __('tagged'),
                'placeholder' => __('tags'),
                'required'    => true,
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
                        ['class' => 'tagsField'],
                    ],
                    [
                        ['row' => 'HtmlTag'],
                        ['tag' => 'div', 'class' => 'small-10 columns'],
                    ],
                ],
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
                        ['class' => 'tagsField'],
                    ],
                    [
                        ['row' => 'HtmlTag'],
                        ['tag' => 'div', 'class' => 'small-2 columns'],
                    ],
                ],
            ]
        );

        $this->getElement('search')->removeDecorator('label');

        $this->addElement(
            new Zend_Form_Element_Note(
                [
                    'name'       => 'forgotten',
                    'value'      => __('additional_search_parameters'),
                    'decorators' => [
                        ['ViewHelper'],
                        [
                            'HtmlTag',
                            [
                                'tag'   => 'a',
                                'id'    => 'show_more_parameters',
                                'class' => 'row',
                                'href'  => $this->getView()->url(['remind']),
                            ],
                        ],
                    ],
                ]
            ),
            'forgotten'
        );

        $this->addElement(
            'checkbox',
            'strictCompliance',
            [
                'label'          => __('strictCompliance'),
                'required'       => false,
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
                            'escape'    => false,
                        ],
                    ],
                    ['Errors'],
                    [
                        ['data' => 'HtmlTag'],
                        ['class' => 'visible-for-medium-up formElements'],
                    ],
                    [
                        ['row' => 'HtmlTag'],
                        ['tag' => 'div', 'class' => 'additional-params-hide  row left'],
                    ],
                ],
            ]
        );

        $this->addElement(
            'MultiCheckbox',
            'separateSearch',
            [
                'multiOptions' => [
                    'vacancy'  => __('onlyVacancy'),
                    'resume'   => __('onlyResume'),
                    'partners' => __('onlyPartnerVacancies'),
                ],
                'value'        => ['partners', 'vacancy', 'resume'],
                'required'     => false,
                'separator'    => '',
                'attribs'      => ['label_placement' => 'APPEND',],
                'label_class'  => 'inline-display',
                'class'        => 'labelInline',
                'escape'       => false,
                'decorators'   => [
                    ['ViewHelper'],
                    ['Errors'],
                    [
                        ['data' => 'HtmlTag'],
                        ['class' => 'columns formElements'],
                    ],
                    [
                        ['row' => 'HtmlTag'],
                        ['tag' => 'div', 'class' => 'additional-params-hide row left visible-for-medium-up'],
                    ],
                ],
            ]
        );

        $this->addElement(
            'MultiCheckbox',
            'separateSearchSelect',
            [
                'multiOptions' => [
                    'vacancy'  => __('onlyVacancy'),
                    'resume'   => __('onlyResume'),
                    'partners' => __('onlyPartnerVacancies'),
                ],
                'value'        => ['partners', 'vacancy', 'resume'],
                'separator'    => '',
                'required'     => false,
                'decorators'   => [
                    ['ViewHelper'],
                    ['Errors'],
                    [
                        ['data' => 'HtmlTag'],
                        ['class' => 'columns'],
                    ],
                    [
                        ['row' => 'HtmlTag'],
                        ['tag' => 'div', 'class' => 'additional-params-hide row visible-for-small-down'],
                    ],
                ],
            ]
        );

        $this->setDecorators(
            [
                'FormElements',
                [
                    ['data' => 'HtmlTag'],
                    ['tag' => 'div', 'class' => 'row collapse'],
                ],
                'Form',
            ]
        );
    }
}