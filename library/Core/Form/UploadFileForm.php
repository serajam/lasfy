<?php

/**
 * Loading documents type form class
 *
 * @author     Kagarlykskiy Aleksey
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class COre_Form_UploadFileForm extends Core_Form
{
    public function init()
    {
        $this->setAttrib('class', 'form');
        $this->setAttrib('id', 'document-form');

        $this->addElement(
            'textarea',
            'comment',
            [
                'label'      => Zend_Registry::get('translation')->get('comment'),
                'required'   => false,
                'filters'    => ['StringTrim', 'StripTags'],
                'validators' => [
                    ['StringLength', false, [0, 300]]
                ]
            ]
        );

        $this->addElement(
            'checkbox',
            'isPublic',
            [
                'label'      => Zend_Registry::get('translation')->get('isPublic'),
                'required'   => true,
                'validators' => [
                    ['int', true],
                    ['StringLength', false, [1, 2]]
                ]
            ]
        );

        $this->addElement(
            'textarea',
            'documentDescription',
            [
                'label'      => Zend_Registry::get('translation')->get('documentDescription'),
                'required'   => false,
                'filters'    => ['StringTrim', 'StripTags'],
                'validators' => [
                    ['StringLength', false, [0, 300]]
                ]
            ]
        );

        $this->addElement(
            'file',
            'document',
            [
                'description' => $this->_dbTranslator->get('available_formats')
                    . ' .jpg, .png, .gif, .pdf.<br>' .
                    $this->_dbTranslator->get('available_text_file_formats') .
                    ' .doc, .docx, .txt, .xls, xlsx, rar, zip<br>&nbsp;',
                'label'       => $this->_dbTranslator->get('document'),
                'required'    => true,
                'validators'  => [
                    ['Extension', false, 'doc,xls,jpeg,jpg,png,gif,pdf,rar,zip,xlsx,docx,tif,tiff'],
                    ['Size', false, 16777216],
                    //array('Count', false, $files),
                    [
                        'MimeType',
                        false,
                        [
                            'image',
                            'application/pdf',
                            'image/tiff',
                            'image/tif',
                            'application/x-pdf',
                            'application/acrobat',
                            'application/vnd.ms-office',
                            'applications/vnd.pdf',
                            'text/pdf',
                            'text/x-pdf',
                            'application/msword',
                            'application/vnd.ms-excel',
                            'application/vnd.ms-powerpoint',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/zip',
                            'application/x-rar-compressed',
                            'application/x-zip-compressed',
                            'application/rar',
                            'application/x-rar'
                            ////TODO REMOVE TEMPORARY
                        ]
                    ]
                ],
                'decorators'  => [
                    'File',
                    ['Description', ['escape' => false]],
                    [
                        ['data' => 'HtmlTag'],
                        ['class' => 'document']
                    ],
                    'Errors',
                    ['HtmlTag', ['tag' => 'li']],
                    ['Label', ['class' => 'desc']]
                ]
            ]
        );
    }
}