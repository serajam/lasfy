<?php

/**
 * User form
 * @author: Aleksey Kagarlykskiy
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 *
 */
class UserForm extends Core_Form
{
    public function init()
    {
        $this->setAttrib('class', 'form');
        $this->setAttrib('id', 'userForm');

        $this->addElement(
            'hidden',
            'userId',
            [
                'required'   => true,
                'validators' => ['int']
            ]
        );

        $this->addElement(
            'hidden',
            'process',
            [
                'required'   => true,
                'value'      => 'saveUser',
                'validators' => [
                    ['StringLength', true, [8, 8]],
                ]
            ]
        );

        $this->addElement(
            'text',
            'name',
            [
                'placeholder' => __('userName'),
                'required'    => true,
                'class'       => 'has-tip tip-top',
                'title'       => __('userName'),
                'attribs'     => ['data-tooltip' => 'data-tooltip'],
                'filters'     => ['StringTrim', 'StripTags'],
                'validators'  => [
                    ['StringLength', false, [2, 100]],
                ]
            ]
        );

        $this->addElement(
            'textarea',
            'telephones',
            [
                'placeholder' => __('telephones'),
                'required'    => false,
                'class'       => 'has-tip tip-top',
                'title'       => __('telephones'),
                'attribs'     => ['data-tooltip' => 'data-tooltip'],
                'validators'  => [
                    ['StringLength', false, [1, 100]],
                ]
            ]
        );

        $this->addSubmit('save', 'sub', 'button');

        $this->addDisplayGroup(
            ['name', 'telephones', 'sub'],
            'userGroup',
            ['disableLoadDefaultDecorators' => true]
        );
        $this->getDisplayGroup('userGroup')
            ->addDecorators(
                [
                    'FormElements',
                    'Fieldset',
                    ['HtmlTag', ['tag' => 'div', 'class' => 'columns panel']]
                ]
            );
    }

    protected function _addFilters()
    {
    }
}