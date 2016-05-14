<?php

/**
 * Created by JetBrains PhpStorm.
 * User: Voronoy
 * Date: 29.08.13
 * Time: 20:42
 * To change this template use File | Settings | File Templates.
 */
class ImageUploadForm extends Core_Form
{
    public function init()
    {
        $this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);
        $this->setAttrib('class', 'form');
        $this->setAttrib('id', 'upload-form');

        $this->addElement(
            'text',
            'imageTitle',
            [
                'label'    => __('imageTitle'),
                'required' => true,
            ]
        );

        $this->addElement(
            'textarea',
            'imageDesc',
            [
                'label'    => __('imageDesc'),
                'required' => false,
            ]
        );

        /*  $this->addElement(
              'select',
              'category',
              array(
                   'label'    => __('category'),
                   'required' => true,
              )
          );*/

        $this->addElement(
            'multiCheckbox',
            'compression',
            [
                'filters'   => ['StringTrim', 'StripTags'],
                'required'  => false,
                'checked'   => '',
                'label'     => __('compression'),
                'separator' => false,
            ]
        );
        $image = new Zend_Form_Element_File(
            'file',
            'files',
            [
                'label'       => __('additional_files'),
                'description' => __('img_formats') . ': .jpg, .png, .gif<br>',
                'required'    => true,
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
        $image->setMultiFile(1);

        $this->addElement($image);
        $this->addSubmit(__('save'));
    }
}