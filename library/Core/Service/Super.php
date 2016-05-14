<?php

/**
 *  Super Service Class
 *
 * @author     Fedor Petryk
 * @package    Core_Service
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_Service_Super
{
    /**
     * Validator - initialized form object
     *
     * @var Core_Form
     */
    protected $_validator = null;

    /**
     * initialized Mapper object
     *
     * @var Core_Mapper_Super
     */
    protected $_mapper = null;

    /**
     * Mapper class name
     *
     * @var string
     */
    protected $_mapperName = 'Core_Mapper_Super';

    /**
     * Validator (Form) class name
     *
     * @var string
     */
    protected $_validatorName;

    /**
     * Error, generated in action processing by service class
     * or by mapper class
     *
     * @var String
     */
    protected $_error;

    /**
     * Error code, generated in action processing by service class
     * or by mapper class
     *
     * @var String
     */
    protected $_errorCode;

    /**
     * @var Core_Mailer_Service_Super
     */
    protected $_mailer;

    /**
     * Message, generated in action processing by service class
     *
     * @var String
     */
    protected $_message;

    /**
     * Message, generated in processing form by service class
     *
     * @var array
     */
    protected $_formMessages;

    /**
     * @var Core_Form_Loader
     */
    protected $_formLoader;

    protected $_formLoaderName = 'Core_Form_Loader';

    /**
     * Encoded array of data to be returned
     *
     * @var String
     */
    protected $_jsonData = null;

    /**
     * @var Core_Model_Client
     */
    protected $_client;

    /**
     * @var Core_Model_User
     */
    protected $_user;

    /**
     * Constructor inializes validator and mapper instances
     *
     * @var array
     */
    public function __construct()
    {
        $this->initDependencies();
        $this->_init();
        $this->_user = Core_Model_User::getInstance();
    }

    public function initDependencies()
    {
        if (null != $this->_validatorName) {
            $class = $this->_validatorName;
            $this->setValidator(new $class);
        }

        if (null != $this->_mapperName) {
            $class = $this->_mapperName;
            $this->setMapper(new $class);
        }

        if (null != $this->_formLoaderName) {
            $class = $this->_formLoaderName;
            $this->setFormLoader(new $class);
        }
    }

    protected function _init()
    {
    }

    public function getJsonData()
    {
        if ($this->_jsonData != null) {
            return $this->_jsonData;
        }

        return false;
    }

    /**
     *
     * Set JSON data
     */
    public function setJsonData($data)
    {
        if ($data != null) {
            $this->_jsonData = $data;
        }
    }

    /**
     *
     * Gets validator
     */
    public function getValidator($id = false)
    {
        if (isset($this->_validator)) {
            if ($id != false) {
                if (!($id instanceof Core_Model_Super)) {
                    $model = $this->_mapper->fetchById($id);
                } else {
                    $model = $id;
                }
                if (!empty($model)) {
                    $this->_fillModel($model);
                    $this->_validator->populate($model->toArray());
                }
            }
            $this->_fillForm();

            return $this->_validator;
        }
    }

    /**
     * @param $name
     *
     * @return $this
     */
    public function setValidator($name)
    {
        $this->_validator = new $name;

        return $this;
    }

    protected function _fillModel($model)
    {
    }

    /**
     *
     * Fill validator with neccecary info data
     *
     * @return void
     *
     */
    protected function _fillForm()
    {
    }

    public function setValidatorForm($validator)
    {
        $this->_validator = $validator;
    }

    /**
     *
     * Returns error
     *
     * @return String
     */
    public function getError()
    {
        return $this->_errorCode;
    }

    /**
     *
     * Sets error
     *
     * @param String | $error
     */
    public function setError($error)
    {
        $this->_errorCode = $error;
        $error            = Core_Messages_Message::getMessage($error);
        $this->_error     = $error;
    }

    /**
     *
     * Returns error
     *
     * @return String
     */
    public function isError()
    {
        if (!empty($this->_error)) {
            return true;
        }

        return false;
    }

    /**
     *
     * Returns message
     *
     * @return String
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     *
     * Sets message
     *
     * @param String | $error
     */
    public function setMessage($message)
    {
        if (empty($message)) {
            return false;
        }
        $this->_message = $message;
    }

    /**
     *
     * Returns form messages
     *
     * @return String
     */
    public function getFormMessages()
    {
        return $this->_formMessages;
    }

    /**
     *
     * Sets form messages
     *
     * @param String | $error
     */
    public function setFormMessages($message)
    {
        $this->_formMessages = $message;
    }

    public function save($data)
    {
        if (is_array($data)) {
            $mname = $this->_mapper->getRowClass();
            $model = new $mname;
            $model->populate($data);
        } elseif (is_object($data)) {
            $model = $data;
        }
        $model = $this->_mapper->objectSave($model);

        return $model;
    }

    public function saveFields($model, array $fields)
    {

        $model = $this->_mapper->saveFields($model, $fields);

        return $model;
    }

    public function sendNotification($template, $params, $mailTo)
    {
        $mail = new Core_Mailer($template, $params, 1);
        $mail->addTo($mailTo);
        $mail->send();
    }

    public function _processFormError($form = null)
    {
        $this->setError(Core_Messages_Message::getMessage('wrong_data'));
        if ($form != null) {
            $this->setFormMessages($form->getMessages());
            $this->setErrorClass($form);
        } else {
            $this->setFormMessages($this->_validator->getMessages());
            $this->setErrorClass($this->_validator);
        }
    }

    public function setErrorClass(Core_Form $form)
    {
        foreach ($form->getElements() as $element) {
            if ($element->hasErrors()) {
                $class = $element->getAttrib('class');
                $element->setAttrib('class', 'errorLight ' . $class);
            }
        }
    }

    /**
     * @return string
     */
    public function getValidatorName()
    {
        return $this->_validatorName;
    }

    /**
     * @param string $validatorName
     */
    public function setValidatorName($validatorName = null)
    {
        $this->_validatorName = $validatorName;
    }

    /**
     * @return string
     */
    public function getMapperName()
    {
        return $this->_mapperName;
    }

    /**
     * @param string $mapperName
     */
    public function setMapperName($mapperName = null)
    {
        $this->_mapperName = $mapperName;
    }

    /**
     * @return \Core_Form_Loader
     */
    public function getFormLoader()
    {
        if ($this->_formLoader == null) {
            $this->_formLoader = new $this->_formLoaderName;
        }

        return $this->_formLoader;
    }

    /**
     * @param \Core_Form_Loader $formLoader
     */
    public function setFormLoader($formLoader)
    {
        $this->_formLoader = $formLoader;
    }

    public function getFormLoaderName()
    {
        return $this->_formLoaderName;
    }

    public function setFormLoaderName($formLoaderName)
    {
        $this->_formLoaderName = $formLoaderName;
    }

    public function logData($table, $params, $data, $merge = false)
    {
        $unique = $this->getMapper()->checkExistingRow($table, $params);
        if ($merge && $unique) {
            $data = array_merge($params, $data);
        }
        if ($unique) {
            Core_Log_Logger::logIt($table, $data);
        }
    }

    /**
     *
     * returns mapper
     *
     * @return Core_Mapper_Super
     */
    public function getMapper()
    {
        return $this->_mapper;
    }

    /**
     *
     * Sets new mapper
     *
     * @param $mapper
     */
    public function setMapper($mapper)
    {
        $this->_mapper = $mapper;

        return $this;
    }

    public function getSiteMenu($lang = 2)
    {
        $menu = $this->_mapper->getSiteMenu($lang);

        return $menu;
    }

    public function getConfigParam($paramName = '')
    {
        $conf = Zend_Registry::get('appConfig');

        return $conf[$paramName];
    }

    public function isUser()
    {
        if (empty(Core_Model_User::getInstance()->userId)) {
            return false;
        }

        return true;
    }

    public function getCompanyByUserId($userId)
    {
        return $this->_mapper->getCompanyByUserId($userId);
    }
}