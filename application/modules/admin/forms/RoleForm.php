<?php

/**
 *
 * Role form class
 *
 * @author     Fedor Petryk
 *
 */
class RoleForm extends Core_Form
{
    public function init()
    {
        $this->setAttrib('class', 'form');
        $this->setAttrib('id', 'role-add');

        $this->addElement(
            'hidden',
            'defaultModule',
            [
                'required'   => true,
                'validators' => [
                    'int',
                    ['StringLength', false, [1, 10]]
                ]
            ]
        );

        $this->addElement(
            'hidden',
            'defaultController',
            [
                'required'   => true,
                'validators' => [
                    'int',
                    ['StringLength', false, [1, 10]]
                ]
            ]
        );

        $this->addElement(
            'hidden',
            'defaultAction',
            [
                'required'   => true,
                'validators' => [
                    'int',
                    ['StringLength', false, [1, 10]]
                ]
            ]
        );

        $this->addElement(
            'hidden',
            'roleId',
            [
                'validators' => [
                    'int',
                    ['StringLength', false, [1, 10]]
                ]
            ]
        );

        $this->addElement(
            'text',
            'roleName',
            [
                'label'      => __('title'),
                'required'   => true,
                'validators' => [
                    ['Unique', false, ['Roles', 'roleName', 'roleId', $this]],
                    ['StringLength', false, [1, 165]],
                ]
            ]
        );

        $this->addElement(
            'text',
            'roleCode',
            [
                'label'       => __('code'),
                'placeholder' => __('code_for_usage'),
                'required'    => true,
                'validators'  => [
                    ['Unique', false, ['Roles', 'roleCode', 'roleId', $this]],
                    ['StringLength', false, [1, 165]],
                ]
            ]
        );

        $this->addElement(
            'select',
            'active',
            [
                'label'        => __('just_activated'),
                'multioptions' => [
                    0 => __('no'),
                    1 => __('yes')
                ],
                'filters'      => ['StringTrim', 'StripTags'],
                'validators'   => [
                    'int',
                    ['StringLength', false, [0, 1]]
                ]
            ]
        );
        $this->addSubmit('save');
        $this->addElement(
            'note',
            'rights',
            [
                'filters'    => [],
                'validators' => []
            ]
        );

        $this->addDisplayGroup(
            ['roleName', 'roleCode', 'active', 'sub'],
            'roleData',
            ['disableLoadDefaultDecorators' => true]
        );
        $this->addDisplayGroup(['rights'], 'roleRights', ['disableLoadDefaultDecorators' => true]);
        $this->getDisplayGroup('roleData')->addDecorators(
            [
                'FormElements',
                'Fieldset',
                ['HtmlTag', ['tag' => 'div', 'class' => 'columns large-4 columns panel']]
            ]
        );
        $this->getDisplayGroup('roleRights')->addDecorators(
            [
                'FormElements',
                'Fieldset',
                ['HtmlTag', ['tag' => 'div', 'class' => 'columns large-8 columns']]
            ]
        );
    }

    protected function _addFilters()
    {
    }
}