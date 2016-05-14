<?php

/**
 * Job_Tags_TagForm
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Job_Tags_TagForm extends Core_Form
{
    public function init()
    {
        $this->setAttrib('class', 'form');
        $this->setAttrib('id', 'tag');
        $this->addElement(
            'hidden',
            'tagId',
            [
                'validators' => [
                    'int',
                    ['StringLength', false, [1, 10]]
                ]
            ]
        );

        $this->addElement(
            'text',
            'tagName',
            [
                'label'       => __('tagName'),
                'placeholder' => __('tagName'),
                'required'    => true,
                'validators'  => [
                    ['StringLength', false, [3, 100]],
                ]
            ]
        );

        $this->addElement(
            'checkbox',
            'enable',
            [
                'label'      => __('enable'),
                'required'   => false,
                'checkbox'   => 'checkbox',
                'value'      => 1,
                'validators' => [
                    ['int', ['Between', false, [0, 1]]]
                ]
            ]
        );

        $this->addSubmit('save');
    }
}