<?php

/**
 *
 * MessageForm form class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class MessageForm extends Core_Form
{
    public function init()
    {
        $this->setAttrib('class', 'form')
            ->setAttrib('method', 'POST')
            ->setAttrib('id', 'send_message_form');
        $this->addElement(
            'textarea',
            'message',
            [
                'placeholder' => __('type_your_message'),
                'required'    => true,
                'cols'        => 10,
                'rows'        => 25,
                'validators'  => [
                    ['StringLength', false, [3, 500]],
                ]
            ]
        );
        $this->addSubmit('send');
    }
}