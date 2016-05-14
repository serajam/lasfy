<?php

/**
 *
 * Page form class
 *
 * @author     Aleksey Kagarlykskiy
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class EmailTemplateForm extends Core_Form
{
    public function init()
    {
        $this->setAttrib('class', 'form custom');
        $this->setAttrib('id', 'page');

        $this->addElement(
            'hidden',
            'mailId',
            [
                'validators' => [
                    'int',
                    ['StringLength', false, [1, 10]]
                ]
            ]
        );
        $this->addElement(
            'text',
            'mailCode',
            [
                'placeholder' => __('email_code'),
                'required'    => true,
                'validators'  => [
                    ['StringLength', false, [3, 45]],
                ]
            ]
        );
        $this->addElement(
            'text',
            'mailSubject',
            [
                'placeholder' => __('email_subject'),
                'required'    => true,
                'validators'  => [
                    ['StringLength', false, [3, 100]],
                ]
            ]
        );

        $this->addElement(
            'textarea',
            'mailBody',
            [
                'label'    => __('mailBody'),
                'required' => true,
                'class'    => 'tinymce',
                'id'       => 'mailBody',
                'style'    => 'visibility:hidden;'
            ]
        );

        $this->addElement(
            'select',
            'lang',
            [
                'label'      => __('lang'),
                'required'   => true,
                'class'      => 'small',
                'validators' => [
                    ['StringLength', false, [1, 2]],
                ]
            ]
        );

        $this->addSubmit('save');
    }

    protected function _addFilters()
    {
    }
}