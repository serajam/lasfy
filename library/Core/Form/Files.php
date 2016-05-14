<?php

class Core_Form_Files extends Core_Form
{
    public function init()
    {
        $this->setAttrib('class', 'commonform');
        $this->setAttrib('enctype', 'multipart/form-data');

        $this->addElement(
            'textarea',
            'comment',
            [
                'label'       => Zend_Registry::get('translation')->get('your_comment'),
                'description' =>
                    Zend_Registry::get('translation')->get('allow_letters_numbers_symbols')
                    . ' \'"-.,&',
                'validators'  => [
                    [
                        'regex',
                        true,
                        [
                            'pattern'  => '/^[0-9a-z\x80-\xFFіІ\s\.\,"\'\-\(\)\&\/\\\%\:\?]+$/i',
                            'messages' => [
                                'regexNotMatch' => Zend_Registry::get('translation')->get(
                                        'allow_letters_numbers_symbols'
                                    ) .
                                    " \/'\"-.,:%&()?"
                            ]
                        ]
                    ],
                    ['StringLength', false, [0, 2500]],
                ],

            ]
        );

        $this->addElement(
            'file',
            'files',
            [
                'label'       => Zend_Registry::get('translation')->get('additional_files'),
                'description' => '<b>' . Zend_Registry::get('translation')->get('allow_5_files') . '</b><br>
			    ' . Zend_Registry::get('translation')->get('img_formats') . ': .jpg, .png, .gif, .pdf.,<br>
			    ' . Zend_Registry::get('translation')->get('doc_formats') . ': .xlsx, .xls, .docx, .doc.<br>
			    ' . Zend_Registry::get('translation')->get('arch_formats') . ': .rar, .zip<br>',
                'required'    => false,
                'validators'  => [
                    ['Extension', false, 'doc,xls,jpeg,jpg,png,gif,pdf,rar,zip,xlsx,docx'],
                    ['Size', false, 5777216],
                    ['Count', false, 5],
                    [
                        'MimeType',
                        false,
                        [
                            'image/jpeg',
                            'image/pjpeg',
                            'image/png',
                            'image/gif',
                            'application/pdf',
                            'application/vnd.ms-office',
                            'application/msword',
                            'application/vnd.ms-excel',
                            'application/x-pdf',
                            'application/acrobat',
                            'applications/vnd.pdf',
                            'text/pdf',
                            'text/x-pdf',
                            'application/zip',
                            'application/x-rar-compressed',
                            'application/x-rar',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
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
        $this->files->setMultiFile(2);
    }
}

?>