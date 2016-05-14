<?php

/**
 * Vacancy form
 *
 * @author     : Aleksey Kagarlykskiy
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 *
 */
class Job_Ads_VacancyForm extends Core_Form
{
    protected static $useCaptcha = true;

    public function init()
    {
        $this->setAttrib('class', 'form');
        $this->setAttrib('id', 'vacancyForm');

        $this->addElement(
            'hidden',
            'vacancyId',
            [
                'validators' => ['int']
            ]
        );

        $this->addElement(
            'hidden',
            'process',
            [
                'required'   => true,
                'value'      => 'vacancy',
                'validators' => [
                    ['StringLength', true, [7, 7]],
                ]
            ]
        );

        $this->addElement(
            'text',
            'seat',
            [
                'label'       => __('vacancyTitle'),
                'placeholder' => __('vacancyTitle'),
                'required'    => true,
                'class'       => '',
                'title'       => __('vacancyTitle'),
                'filters'     => ['StringTrim', 'StripTags'],
                'validators'  => [
                    ['StringLength', false, [3, 1000]],
                ]
            ]
        );

        $this->addElement(
            'textarea',
            'vacancyDescription',
            [
                'label'       => __('vacancyDescription'),
                'placeholder' => __('vacancyDescription'),
                'required'    => true,
                'title'       => __('vacancyDescription'),
                'filters'     => ['StringTrim'],
                'validators'  => [
                    ['StringLength', false, [3, 2000]],
                ]
            ]
        );

        $this->addElement(
            'textarea',
            'requirements',
            [
                'label'       => __('vacancyRequirements'),
                'placeholder' => __('vacancyRequirements'),
                'required'    => true,
                'title'       => __('vacancyRequirements'),
                'filters'     => ['StringTrim'],
                'validators'  => [
                    ['StringLength', false, [5, 2000]],
                ]
            ]
        );

        $this->addElement(
            'textarea',
            'offer',
            [
                'label'       => __('vacancyProposition'),
                'placeholder' => __('vacancyProposition'),
                'required'    => true,
                'title'       => __('vacancyProposition'),
                'filters'     => ['StringTrim'],
                'validators'  => [
                    ['StringLength', false, [5, 2000]],
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
                'vacancyId',
                'process',
                'seat',
                'vacancyDescription',
                'requirements',
                'offer',
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
                'placeholder' => __('tags'),
                'label'       => __('tags'),
                'required'    => true,
                'class'       => 'has-tip tip-top',
                'title'       => __('tags'),
                'attribs'     => ['data-tooltip' => 'data-tooltip'],
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