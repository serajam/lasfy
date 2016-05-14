<?php

/**
 *
 * User form class
 *
 * @author     Aleksey Kagarlykskiy
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class PageForm extends Core_Form
{
    public function init()
    {
        $this->setAttrib('class', 'form');
        $this->setAttrib('id', 'page');
        $this->addElement(
            'hidden',
            'pageId',
            [
                'validators' => [
                    'int',
                    ['StringLength', false, [1, 10]]
                ]
            ]
        );

        $this->addElement(
            'text',
            'title',
            [
                'label'       => __('title'),
                'placeholder' => __('page_title'),
                'required'    => true,
                'validators'  => [
                    ['StringLength', false, [3, 100]],
                ]
            ]
        );
        $this->addElement(
            'textarea',
            'pageContent',
            [
                'label'    => __('content'),
                'required' => true,
                'class'    => 'tinymce',
                'id'       => 'pageContent',
                'style'    => 'visibility:hidden;'
            ]
        );

        $this->addElement(
            'textarea',
            'shortContent',
            [
                'label'      => __('shortContent'),
                'required'   => false,
                'id'         => 'shortContent',
                'validators' => [
                    ['StringLength', false, [0, 1000]],
                ]
            ]
        );

        $this->addElement(
            'text',
            'slug',
            [
                'label'       => __('slug'),
                'placeholder' => __('enter_short_url'),
                'value'       => false,
                'required'    => false,
                'validators'  => [
                    ['StringLength', false, [3, 45]],
                ]
            ]
        );

        $this->addElement(
            'select',
            'type',
            [
                'label'      => __('type'),
                'required'   => true,
                'class'      => 'small',
                'validators' => [
                    ['Between', false, [1, 4]]
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
                    ['Between', false, [1, 3]]
                ]
            ]
        );

        $this->addElement(
            'text',
            'metaTitle',
            [
                'label'       => __('metaTitle'),
                'placeholder' => __('metaTitle'),
                'validators'  => [
                    ['StringLength', false, [5, 255]],
                ]
            ]
        );

        $this->addElement(
            'textarea',
            'metaDescription',
            [
                'label'       => __('metaDescription'),
                'placeholder' => __('metaDescription'),
                'validators'  => [
                    ['StringLength', false, [5, 255]],
                ]
            ]
        );

        $this->addElement(
            'textarea',
            'metaKeywords',
            [
                'label'       => __('keywords'),
                'placeholder' => __('keywords'),
                'validators'  => [
                    ['StringLength', false, [5, 255]],
                ]
            ]
        );

        $this->addSubmit('save');
    }

    protected function _addFilters()
    {
        $newFilters = [
            new Zend_Filter_StringTrim(),
        ];
        if (count($this->getElements())) {
            foreach ($this->getElements() as $element) {
                if (!$element instanceof Zend_Form_Element_Multi) {
                    $filters = [];
                    foreach ($newFilters as $newFilter) {
                        if (!in_array($newFilter, $element->getFilters())) {
                            $filters[] = $newFilter;
                        }
                    }
                    $element->addFilters($filters);
                }
            }
        }
    }
}