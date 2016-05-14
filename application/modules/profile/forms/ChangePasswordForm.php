<?php

/**
 * Password form
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class ChangePasswordForm extends Core_Form
{
    public function init()
    {
        $this->setAttrib('class', 'form');
        $this->setAttrib('id', 'password_change');
        //$this->setAttrib('action', $this->getView()->domainLink(1, true) . 'profile/index/change-password');

        $this->addElement(
            'hidden',
            'process',
            [
                'required'   => true,
                'value'      => 'changePassword',
                'filters'    => ['StringTrim', 'StripTags'],
                'validators' => [
                    ['StringLength', true, [14, 14]],
                ]
            ]
        );

        $this->addElement(
            'password',
            'oldpassword',
            [
                'label'      => __('oldpassword'),
                'required'   => true,
                'validators' => [
                    ['StringLength', false, [8, 16]],
                ]
            ]
        );

        $this->addElement(
            'password',
            'password',
            [
                'label'      => __('passwd'),
                'required'   => true,
                'validators' => [
                    ['StringLength', false, [8, 16]]
                ]
            ]
        );

        $this->addElement(
            'password',
            'password_repeat',
            [
                'label'      => __('repeat_passwd'),
                'required'   => true,
                'validators' => [
                    ['IdenticalFields', false, ['password']],
                    ['StringLength', false, [8, 16]]
                ]
            ]
        );

        $this->addSubmit('change');

        $this->addDisplayGroup(
            ['oldpassword', 'password', 'password_repeat', 'sub'],
            'changePassword',
            ['disableLoadDefaultDecorators' => true]
        );
        $this->getDisplayGroup('changePassword')
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
