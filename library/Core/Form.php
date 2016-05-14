<?php

/**
 * Extended Form with new decorators and translated errors to russion lang
 *
 * @author      Fedor Petryk
 * @copyright   Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_Form extends Zend_Form
{
    // form cache individually
    /**
     * The form element decorators FOR LIST
     *
     * @var array
     */
    public $elementDecoratorsTable
        = [
            'ViewHelper',
            'Errors',
            'FormElements',
            [['data' => 'HtmlTag'], ['tag' => 'td']],
            ['Label', ['tag' => 'td']],
            [['row' => 'HtmlTag'], ['tag' => 'tr']]
        ];

    /**
     * The form and elements decorators FOR LIST
     *
     * @var array
     */
    public $formDecoratorsTable
        = [
            'FormElements',
            [
                ['data' => 'HtmlTag'],
                ['tag' => 'table'],
                ['class' => 'zend_form']
            ],
            'Form'
        ];

    /**
     * The form element decorators for list style type
     *
     * @var array
     */
    public $elementDecorators
        = [
            'ViewHelper',
            'Errors',
            'FormElements',
            [
                ['data' => 'HtmlTag'],
                ['class' => 'element']
            ],
            [
                'Label',
                ['class' => 'desc']
            ],
            [
                'Description',
                ['class' => 'desc']
            ],
            [
                ['row' => 'HtmlTag'],
                ['tag' => 'li']
            ]
        ];

    public $elementDecoratorsHidden
        = [
            'ViewHelper',
            'Errors',
            'FormElements',
            /*   array(
                   array('data' => 'HtmlTag'),
                   array('class' => 'element small-8 columns')
               ),*/
            [
                'Label',
                ['class' => 'desc']
            ],
            [
                'Description',
                ['class' => 'desc']
            ],
            /*   array(
                   'FieldHelper',
                   array('class' => 'has-tip small-2 columns', 'data-tooltip' => '')
               ),*/
            [
                ['row' => 'HtmlTag'],
                ['tag' => 'li', 'class' => 'row collapse  hidden']
            ]
        ];

    /**
     * The form and elements decorators
     *
     * @var array
     */
    public $formDecorators
        = [
            'FormElements',
            [
                ['data' => 'HtmlTag'],
                ['tag' => 'ul'],
                ['class' => 'zend_form']
            ],
            'Form'
        ];

    protected $_caching = false;

    /**
     * @var Core_Translation
     */
    protected $_dbTranslator;

    /**
     *
     * The constructor
     * Translates errors
     * Sets new decorators
     * Initializes form
     *
     * @param array $options
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof Zend_Config) {
            $this->setConfig($options);
        }

        $this->_dbTranslator = Zend_Registry::get('translation');

        $this->setTranslator(Zend_Registry::get('Zend_Translate'));
        $this->addElementPrefixPath('Core_Validate', 'Core/Validate', 'validate');
        $this->addElementPrefixPath('Core_Filter', 'Core/Filter', 'filter');
        $this->addPrefixPath('Core_Form_Element', 'Core/Form/Element', 'element');
        $this->addPrefixPath('Core_Form_Decorator', 'Core/Form/Decorator', 'decorator');
        $this->setupDecorators();
        $this->init();
        $this->_addFilters();
    }

    public function setupDecorators()
    {
        $this->setElementDecorators($this->elementDecorators);
        $this->setDecorators($this->formDecorators);
        $this->loadDefaultDecorators();
    }

    protected function _addFilters()
    {
        $newFilters = [
            new Zend_Filter_StringTrim(),
            new Zend_Filter_StripTags(),
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

    public function populate(array $data)
    {
        /*        if (is_object($data)) {
                    return $this->setDefaults($data->toArray());
                }*/
        return $this->setDefaults($data);
    }

    public function addSubmit($value = 'accept', $name = 'sub', $class = 'small button')
    {
        $element = new Zend_Form_Element_Submit($name);
        $element->setRequired(false)
            ->setValue($value)
            ->setAttribs(
                [
                    'type'  => 'submit',
                    'order' => 100,
                    'id'    => $value,
                    'class' => $class
                ]
            );
        $this->addElement($element);
        $this->$name->setLabel($this->_dbTranslator->get($value));
        $this->$name->setDecorators(
            [
                ['ViewHelper'],
                ['HtmlTag', ['tag' => 'div', 'class' => 'submit']],
            ]
        );
    }

    public function addDocument($id, $files = 1)
    {
        $this->addElement(
            'file',
            $id,
            [
                'description' => $this->_dbTranslator->get('available_formats')
                    . ' .jpg, .png, .gif, .pdf.<br>' .
                    $this->_dbTranslator->get('available_text_file_formats') .
                    ' .doc, .docx, .txt, .xls, xlsx, rar, zip<br>&nbsp;',
                'label'       => $this->_dbTranslator->get('document'),
                'required'    => false,
                'validators'  => [
                    ['Extension', false, 'doc,xls,jpeg,jpg,png,gif,pdf,rar,zip,xlsx,docx,tif,tiff'],
                    ['Size', false, 16777216],
                    ['Count', false, $files],
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
                        ['class' => 'file-element']
                    ],
                    'Errors',
                    ['HtmlTag', ['tag' => 'li']],
                    ['Label', ['class' => 'desc']]
                ]
            ]
        );
        $this->$id->setMultiFile($files);
    }

    public function setAction($action)
    {
        $isAllowed = Core_Acl_Access::isAllowed($action);
        if ($isAllowed) {
            parent::setAction($action);
        } else {
            $this->removeElement('sub');
        }
    }

    /**
     * Add captcha to current form
     *
     * @throws Zend_Exception
     * @throws Zend_Form_Exception
     */
    protected function addCaptcha()
    {

        $conf                  = Zend_Registry::get('appConfig');
        Zend_Captcha_Word::$VN = Zend_Captcha_Word::$CN = range(0, 9);

        $this->addElement(
            new Zend_Form_Element_Captcha(
                'captchaCode',
                [
                    'ignore'      => true,
                    'placeholder' => __('enter_code'),
                    'captcha'     => [
                        'captcha'    => 'Image',
                        'class'      => 'code',
                        'font'       => APPLICATION_PUB . '/../../fonts/Vera.ttf',
                        'fsize'      => '6', // размер шрифта
                        'height'     => '60', // высота изображения
                        'width'      => '130', // ширина изображения
                        'imgDir'     => BASE_PATH . '/images/captcha',
                        'imgUrl'     => $conf['baseHttpPath'] . 'images/captcha',
                        'wordLen'    => 4, // количество символов
                        'expiration' => 500, // время жизни капчи
                    ],
                    // Captcha использует свой собственный декоратор, поэтому, для корректного ее отображения
                    // декоратор должен быть задан примерно следующим образом:

                    'decorators' => [
                        ['Errors'],
                        [
                            'HtmlTag',
                            ['tag' => 'div', 'class' => 'captcha']
                        ],
                        ['Label', ['tag' => 'div']],
                        [
                            'FormElements',
                            ['tag' => 'div', 'class' => 'captcha']
                        ],
                    ]
                ]
            )
        );
    }
}