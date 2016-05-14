<?php

/**
 *
 * User role form class
 *
 * @author     Fedor Petryk
 *
 */
class UserRole extends Core_Form
{
    public function init()
    {
        $this->setAttrib('class', 'form');
        $this->setAttrib('id', 'user_role');

        $this->addElement(
            'select',
            'roleId',
            [
                'label'      => __('role'),
                'required'   => true,
                'validators' => [
                    'int',
                    ['StringLength', false, [1, 10]]
                ]
            ]
        );
        $this->addSubmit('save');
    }
}