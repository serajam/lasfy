<?php

/**
 *
 * Filter of pages form class
 *
 * @author     Aleksey Kagarlykskiy
 * @copyright  Copyright (c) 2013-forever Studio 105 (http://105.in.ua)
 */
class PagesFilterForm extends Core_Form
{
    public function init()
    {
        $this->setAttrib('class', 'form custom');
        $this->setAttrib('id', 'filter-page');
        $this->setAttrib('method', 'get');

        $this->addElement(
            'select',
            'pageType',
            [
                'label'      => __('pageType'),
                'required'   => true,
                'class'      => 'small',
                'validators' => [
                    ['Between', false, [0, 3]],
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
                    ['Between', false, [0, 3]],
                ]
            ]
        );

        $this->addSubmit('filter');
    }
}