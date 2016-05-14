<?php

/**
 * Created by JetBrains PhpStorm.
 * User: Voronoy
 * Date: 28.08.13
 * Time: 19:39
 * To change this template use File | Settings | File Templates.
 */
class SettingsForm extends Core_Form
{
    public function init()
    {
        $this->setAttrib('class', 'form');
        $this->setAttrib('id', 'settings-form');

        $this->addElement(
            'hidden',
            'paramId'
        );

        $this->addElement(
            'textarea',
            'paramDesc',
            [
                'placeholder' => __('param_desc'),
                'required'    => true,
                'validators'  => [
                    ['StringLength', false, [1, 200]],
                ]
            ]
        );

        $this->addElement(
            'text',
            'paramName',
            [
                'placeholder' => __('param_name'),
                'required'    => true,
                'validators'  => [
                    ['StringLength', false, [3, 20]],
                ]
            ]
        );

        $this->addElement(
            'textarea',
            'paramValue',
            [
                'placeholder' => __('param_value'),
                'required'    => true,
                'class'       => 'tinymce',
                'id'          => 'paramValue',
                'style'       => 'visibility:hidden;',
                'validators'  => [
                    ['StringLength', false, [1, 1000]],
                ]
            ]
        );

        $this->addElement(
            'select',
            'paramGroup',
            [
                'label'      => __('param_group'),
                'required'   => true,
                'validators' => [
                    ['StringLength', false, [1, 100]],
                ]
            ]
        );

        $this->addSubmit('save');
    }

    protected function _addFilters()
    {
    }
}