<?php

/**
 * Recovery password form class
 *
 * @author     Kagarlykskiy Aleksey
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class RecoveryPasswordForm extends Core_Form
{
    public function init()
    {
        $this->setAttrib('class', 'form');
        $this->setAttrib('id', 'recoveryPassword');

        $this->addElement(
            'text',
            'email',
            [
                'label'      => __('e-mail_login'),
                'required'   => true,
                'validators' => [
                    'EmailAddress',
                    ['StringLength', false, [5, 100]],
                ]
            ]
        );

        $this->addCaptcha();
        $this->addSubmit('send');
    }

    public function _addFilters()
    {
    }
}