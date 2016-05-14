<?php

/**
 * Contact form class
 *
 * @author     Fedor Petryk
 *
 */
class Core_Form_Contact extends Core_Form
{
    public function init()
    {
        $this->setAttrib('class', 'form');
        $this->setAttrib('id', 'request');

        $this->addElement(
            'text',
            'name',
            [
                'placeholder' => __('name'),
                'required'    => true,
                'validators'  => [
                    ['StringLength', false, [3, 100]],
                ]
            ]
        );

        $this->addElement(
            'text',
            'email',
            [
                'placeholder' => __('email'),
                'required'    => true,
                'validators'  => [
                    'EmailAddress',
                    ['StringLength', false, [5, 100]],
                ]
            ]
        );

        $this->addElement(
            'textarea',
            'message',
            [
                'placeholder' => __('message'),
                'required'    => true,
                'validators'  => [
                    ['StringLength', false, [5, 1000]],
                ]
            ]
        );

        $newFilters = [
            new Zend_Filter_StringTrim(),
            new Zend_Filter_StripTags(),
        ];
        $this->email->addFilters($newFilters);
        $this->message->addFilters($newFilters);

        $this->addCaptcha();
        $this->addSubmit('send', 'sub', 'button success');
    }

    protected function _addFilters()
    {
    }
}