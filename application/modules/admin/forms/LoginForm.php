<?php

/**
 *
 * The login form for authentication
 *
 * @author Fedor Petryk
 *
 */
class LoginForm extends Core_Form
{
    public $elementDecorators = [
        'ViewHelper',
        'Errors',
        [['data' => 'HtmlTag'], ['class' => 'element']],
        ['Label', ['class' => 'desc']],
        [['row' => 'HtmlTag'], ['tag' => 'li']],
    ];

    public function init()
    {
        $this->setAttrib('class', 'forms');
        $login = new Zend_Form_Element_Text('login');
        $login->setLabel(__('login'))
            ->setRequired(true)
            ->setAttrib('id', '')
            ->setAttrib('class', 'field text full')
            ->addValidator('NotEmpty');
        $login->setDecorators($this->elementDecorators);

        $pass = new Zend_Form_Element_Password('password');
        $pass->setLabel(__('passwd'))
            ->setRequired(true)
            ->setAttrib('class', 'field text full')
            ->addValidator('NotEmpty');
        $pass->setDecorators($this->elementDecorators);
        $submit = new Zend_Form_Element_Submit('sub', ['class' => 'button']);
        $submit->setLabel(__('enter'));

        $this->addElements([$login, $pass, $submit]);
        $this->sub->setDecorators(
            [
                ['ViewHelper'],
                ['Description'],
                ['HtmlTag', ['tag' => 'div', 'class' => 'submit-group']],
            ]
        );
    }
}