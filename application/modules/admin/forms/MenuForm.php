<?php

/**
 *
 * Menu form class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class MenuForm extends Core_Form
{
    public function init()
    {
        $this->setAttrib('class', 'form custom');
        $this->setAttrib('id', 'page');

        $this->addElement(
            'hidden',
            'menuId',
            [
                'validators' => ['int']
            ]
        );

        $this->addElement(
            'select',
            'parentId',
            [
                'label'      => __('subItemOf'),
                'class'      => 'small',
                'required'   => false,
                'validators' => ['int']
            ]
        );

        $this->addElement(
            'text',
            'name',
            [
                'placeholder' => __('menu_name'),
                'required'    => true,
                'validators'  => [
                    ['StringLength', false, [4, 45]]
                ]
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
                    ['Between', false, [0, 3]]
                ]
            ]
        );

        $this->addElement(
            'select',
            'type',
            [
                'label'      => __('type'),
                'required'   => true,
                'validators' => [
                    'int',
                    ['Between', false, [1, 5]]
                ]
            ]
        );

        $this->addElement(
            'select',
            'contentId',
            [
                'label'            => __('bind_to_content'),
                'required'         => false,
                'data-placeholder' => __('make_choose'),
                'validators'       => [
                    ['int'],
                    ['Unique', false, ['SiteMenu', 'contentId', 'menuId', $this]],
                ],

            ]
        );

        $this->addElement(
            'multiselect',
            'pageId',
            [
                'label'            => __('add_articles'),
                'required'         => false,
                'data-placeholder' => __('make_choose'),
                'class'            => 'large-4',
                'validators'       => [['int']]
            ]
        );

        //   $this->contentId->setDecorators($this->elementDecoratorsHidden);
        //   $this->pageId->setDecorators($this->elementDecoratorsHidden);
        $this->addElement(
            'text',
            'link',
            [
                'placeholder' => __('external_link'),
                'class'       => 'hidden',
                'required'    => false,
                'validators'  => [
                    ['StringLength', false, [1, 200]]
                ]
            ]
        );

        $this->link->setDecorators($this->elementDecoratorsHidden);
        $this->addElement(
            'text',
            'position',
            [
                'label'      => __('position'),
                'validators' => [['int']]
            ]
        );

        $this->addElement(
            'select',
            'availability',
            [
                'label'      => __('availability'),
                'required'   => true,
                'validators' => [
                    'int',
                    ['Between', false, [1, 3]]
                ]
            ]
        );

        $this->addElement(
            'checkbox',
            'active',
            [
                'label'      => __('active'),
                'required'   => false,
                'value'      => 1,
                'validators' => [
                    ['int', ['Between', false, [0, 1]]]
                ]
            ]
        );

        $this->addSubmit('save');
    }
}