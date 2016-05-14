<?php

/**
 * Resume form
 *
 * @author     : Aleksey Kagarlykskiy
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 *
 */
class Job_Ads_Model_ResumeForm extends Core_Form
{
    protected static $useCaptcha = true;

    public function init()
    {
        $this->setAttrib('class', 'form');
        $this->setAttrib('id', 'resumeForm');

        $this->addElement(
            'hidden',
            'resumeId',
            [
                'validators' => ['int']
            ]
        );

        $this->addElement(
            'hidden',
            'process',
            [
                'required'   => true,
                'value'      => 'resume',
                'validators' => [
                    ['StringLength', true, [6, 6]],
                ]
            ]
        );

        $this->addElement(
            'text',
            'seat',
            [
                'label'       => __('seat'),
                'placeholder' => __('seat'),
                'required'    => true,
                'title'       => __('seat'),
                'filters'     => ['StringTrim', 'StripTags'],
                'validators'  => [
                    ['StringLength', false, [3, 2000]],
                ]
            ]
        );

        $this->addElement(
            'textarea',
            'experience',
            [
                'label'       => __('myExperience'),
                'placeholder' => __('myExperience'),
                'required'    => true,
                'title'       => __('myExperience'),
                'filters'     => ['StringTrim'],
                'validators'  => [
                    ['StringLength', false, [3, 5000]],
                ]
            ]
        );

        $this->addElement(
            'textarea',
            'expectations',
            [
                'label'       => __('myExpectation'),
                'placeholder' => __('myExpectation'),
                'required'    => true,
                'title'       => __('myExpectation'),
                'filters'     => ['StringTrim'],
                'validators'  => [
                    ['StringLength', false, [3, 5000]],
                ]
            ]
        );

        $this->addElement(
            'textarea',
            'goals',
            [
                'label'       => __('myGoals'),
                'placeholder' => __('myGoals'),
                'required'    => true,
                'title'       => __('myGoals'),
                'filters'     => ['StringTrim'],
                'validators'  => [
                    ['StringLength', false, [3, 2000]],
                ]
            ]
        );

        $this->addTagsElement();

        if (static::$useCaptcha) {
            $this->addCaptcha();
        }

        $this->addSubmit('save', 'sub', 'button');

        $this->addDisplayGroup(
            [
                'resumeId',
                'process',
                'seat',
                'experience',
                'expectations',
                'goals',
                'tags',
                'captchaCode',
                'sub'
            ],
            'adsGroup',
            ['disableLoadDefaultDecorators' => true]
        );
        $this->getDisplayGroup('adsGroup')
            ->addDecorators(
                [
                    'FormElements',
                    'Fieldset',
                    ['HtmlTag', ['tag' => 'div', 'class' => 'columns panel']]
                ]
            );
    }

    protected function addTagsElement()
    {
        $this->addElement(
            'textarea',
            'tags',
            [
                'label'       => __('tags'),
                'placeholder' => __('tags'),
                'required'    => true,
                'filters'     => ['StringTrim', 'StripTags'],
                'validators'  => [
                    ['StringLength', false, [2, 1000]],
                ],
                'class'       => 'tagsSet'
            ]
        );
    }

    protected function _addFilters()
    {
    }
}