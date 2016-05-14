<?php

/**
 * User form
 * @author: Aleksey Kagarlykskiy
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 *
 */
class CompanyForm extends Core_Form
{
    public function init()
    {
        $this->setAttrib('class', 'form');
        $this->setAttrib('id', 'companyForm');

        $this->addElement(
            'hidden',
            'userId',
            [
                'required'   => true,
                'validators' => ['int']
            ]
        );

        $this->addElement(
            'hidden',
            'companyId',
            [
                'required'   => false,
                'validators' => ['int']
            ]
        );

        $this->addElement(
            'hidden',
            'process',
            [
                'required'   => true,
                'value'      => 'saveCompany',
                'validators' => [
                    ['StringLength', true, [11, 11]],
                ]
            ]
        );

        $conf        = Zend_Registry::get('appConfig');
        $defaultLang = Zend_Registry::get('language');
        $this->addElement(
            'img',
            'companyLogo',
            [
                'src'      => $conf['baseHttpPath'] . $defaultLang . '/profile/index/get-logo',
                'width'    => 150,
                'height'   => 150,
                'required' => false,
            ]
        );

        $file = new Zend_Form_Element_File(
            'file',
            'files',
            [
                'label'       => __('companyLogo'),
                'description' => __('img_formats') . ': .jpg, .png, .gif<br>',
                'required'    => false,
                'maxlength'   => 5,
                'validators'  => [
                    ['Extension', false, 'jpeg,jpg,png,gif'],
                    ['Size', false, 5777216],
                    ['Count', false, ['min' => 1, 'max' => 1]],
                    [
                        'MimeType',
                        false,
                        [
                            'image/jpeg',
                            'image/pjpeg',
                            'image/png',
                            'image/gif',
                        ]
                    ]
                ],
                'decorators'  => [
                    'File',
                    ['Description', ['escape' => false]],
                    [
                        ['data' => 'HtmlTag'],
                        ['class' => 'files']
                    ],
                    'Errors',
                    ['HtmlTag', ['tag' => 'li']],

                ]
            ]
        );
        $file->setMultiFile(1);

        $this->addElement($file);

        $this->addElement(
            'checkbox',
            'isActiveByUser',
            [
                'label'          => __('active'),
                'checked'        => 'checked',
                'uncheckedValue' => null,
                'class'          => 'labelInline',
                'validators'     => [
                    'int',
                ],
                'decorators'     => [
                    ['ViewHelper'],
                    [
                        'Label',
                        [
                            'placement' => 'APPEND',
                            'escape'    => false
                        ]
                    ],
                    ['Errors'],
                    ['FormElements'],
                    [
                        ['data' => 'HtmlTag'],
                        ['class' => 'element']
                    ],
                    [
                        ['row' => 'HtmlTag'],
                        ['tag' => 'li']
                    ]
                ]
            ]
        );

        $this->addElement(
            'text',
            'name',
            [
                'placeholder' => __('companyName'),
                'required'    => true,
                'class'       => 'has-tip tip-top',
                'title'       => __('companyName'),
                'attribs'     => ['data-tooltip' => 'data-tooltip'],
                'filters'     => ['StringTrim', 'StripTags'],
                'validators'  => [
                    ['StringLength', false, [2, 100]],
                ]
            ]
        );

        $this->addElement(
            'textarea',
            'address',
            [
                'placeholder' => __('address'),
                'required'    => false,
                'class'       => 'has-tip tip-top',
                'title'       => __('address'),
                'attribs'     => ['data-tooltip' => 'data-tooltip'],
                'filters'     => ['StringTrim', 'StripTags'],
                'validators'  => [
                    ['StringLength', false, [1, 100]],
                ]
            ]
        );

        $this->addElement(
            'textarea',
            'telephones',
            [
                'placeholder' => __('telephones'),
                'required'    => false,
                'class'       => 'has-tip tip-top',
                'title'       => __('telephones'),
                'attribs'     => ['data-tooltip' => 'data-tooltip'],
                'filters'     => ['StringTrim', 'StripTags'],
                'validators'  => [
                    ['StringLength', false, [1, 100]],
                ]
            ]
        );

        $this->addElement(
            'text',
            'webSite',
            [
                'placeholder' => __('website'),
                'required'    => false,
                'class'       => 'has-tip tip-top',
                'title'       => __('webSite'),
                'attribs'     => ['data-tooltip' => 'data-tooltip'],
                'filters'     => ['StringTrim', 'StripTags'],
                'validators'  => [
                    ['StringLength', false, [1, 100]],
                    ['IsUrl', false]
                ]
            ]
        );

        $this->addElement(
            'textarea',
            'shortDescription',
            [
                'placeholder' => __('shortCompanyDescription'),
                'required'    => true,
                'class'       => 'has-tip tip-top',
                'title'       => __('shortCompanyDescription'),
                'filters'     => ['StringTrim', 'StripTags'],
                'attribs'     => ['data-tooltip' => 'data-tooltip'],
                'validators'  => [
                    ['StringLength', false, [1, 5000]],
                ]
            ]
        );

        $this->addElement(
            'textarea',
            'description',
            [
                'label'       => __('fullCompanyDescription'),
                'placeholder' => __('companyDescription'),
                'required'    => false,
                'class'       => 'has-tip tip-top',
                'title'       => __('companyDescription'),
                'attribs'     => ['data-tooltip' => 'data-tooltip'],
                'validators'  => [
                    ['StringLength', false, [1, 5000]],
                ]
            ]
        );

        $this->addSubmit('save', 'sub', 'button');

        $this->addDisplayGroup(
            ['userId', 'companyId', 'process', 'companyLogo', 'file', 'isActiveByUser', 'name', 'address', 'telephones', 'webSite',
             'shortDescription', 'description', 'sub'],
            'companyGroup',
            ['disableLoadDefaultDecorators' => true]
        );

        $this->getDisplayGroup('companyGroup')
            ->addDecorators(
                [
                    'FormElements',
                    'Fieldset',
                    ['HtmlTag', ['tag' => 'div', 'class' => 'columns panel']]
                ]
            );
    }

    protected function _addFilters()
    {
    }
}