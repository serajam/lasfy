<?php

/**
 *
 * Role Service class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class TranslationService extends Core_Service_Super
{
    /**
     *
     * Role mapper class
     *
     * @var TranslationMapper
     */
    protected $_mapper = null;

    /**
     *
     * Current service domain object
     *
     * @var Object
     */
    protected $_rowObject = null;

    /**
     *
     * Role mapper class
     *
     * @var RolesMapper
     */
    protected $_mapperName = 'TranslationMapper';

    /**
     *
     * THe validator - role form class name
     *
     * @var String
     */
    protected $_validatorName = 'TranslationForm';

    /**
     *
     * THe validator - role form class object
     *
     * @var RoleForm
     */
    protected $_validator;

    public function add($data)
    {
        if (!$this->_validator->isValid($data)) {
            $this->setError(Core_Messages_Message::getMessage(300));
            $this->setFormMessages($this->_validator->getMessages());

            return false;
        }
        $conf   = Zend_Registry::get('languages');
        $values = $this->_validator->getValues();

        $isAdded = $this->_mapper->getTranslationRow($values['code']);
        if ($isAdded) {
            $this->setError(Core_Messages_Message::getMessage('this_code_already_exists'));

            return false;
        }

        $res = $this->_mapper->add($values, $conf['languages']);
        if ($res) {
            $this->_clearCache();
            $this->setMessage(Core_Messages_Message::getMessage(1));

            return true;
        }

        return false;
    }

    protected function _clearCache()
    {
        $cache  = Zend_Registry::get('cache');
        $config = Zend_Registry::get('languages');
        $langs  = $config['languages'];

        foreach ($langs as $key => $lang) {
            $cacheKey = 'trans_' . $key;
            $cache->remove($cacheKey);
        }
    }

    public function save($data)
    {
        if (empty($data['trans'])) {
            return false;
        }

        $res = $this->_mapper->saveTranslation($data['trans']);
        if (!$res) {
            $this->setError($res);

            return false;
        }
        $this->_clearCache();
        $this->setMessage(Core_Messages_Message::getMessage(1));

        return true;
    }

    /**
     * @param  $data | array of filter params (module, resourse, controller
     *
     * @return bool
     */
    public function translations($data)
    {
        $pattern = '/[a-z\_]*/i';
        $valid   = new Zend_Validate_Regex($pattern);

        if (!$valid->isValid($data['search'])) {
            return false;
        }

        $trans = $this->_mapper->getTranslation(
            $data['search']
        );

        if ($trans->count() > 0) {
            $this->_jsonData = $trans->generateInputs();
        }
    }
}