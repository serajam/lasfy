<?php

class CustomContentForm extends Core_Form
{
    public function init()
    {
        $this->setAttrib('class', 'form');
        $this->setAttrib('id', 'custom_content');

        $this->addElement(
            'hidden',
            'contentId',
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
                    ['StringLength', false, [3, 255]],
                ]
            ]
        );
        $this->addElement(
            'textarea',
            'text',
            [
                'label'      => __('content'),
                'required'   => false,
                'class'      => 'tinymce',
                'id'         => 'text',
                'style'      => 'visibility:hidden;',
                'validators' => [
                    ['StringLength', false, [3, 2500]],
                ]
            ]
        );

        $this->addElement(
            'text',
            'link',
            [
                'label'       => __('link'),
                'placeholder' => __('link'),
                'value'       => false,
                'required'    => false,
                'validators'  => [
                    ['StringLength', false, [3, 255]]
                ]
            ]
        );

        $this->addElement(
            'text',
            'image',
            [
                'label'      => __('image'),
                'required'   => false,
                'validators' => [
                    ['StringLength', false, [0, 255]]
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
                    ['int'],
                ]
            ]
        );
        $this->addElement(
            'text',
            'position',
            [
                'label'      => __('position'),
                'class'      => 'small',
                'required'   => false,
                'validators' => [
                    ['StringLength', false, [0, 3], 'int'],
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