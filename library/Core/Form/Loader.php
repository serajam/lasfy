<?php

/**
 * Class
 *
 * @author     : Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_Form_Loader
{
    protected $_forms = [];

    protected $_cacheEnabled = true;

    public function __construct()
    {
        $config              = Zend_Registry::get('appConfig');
        $this->_cacheEnabled = $config['cache']['form'];
    }

    /**
     * @param $formName
     *
     * @return Core_Form
     */
    public function getForm($formName)
    {
        if ($form = $this->isExists($formName)) {
            return $form;
        }
        if ($this->_cacheEnabled) {
            return $this->getCached($formName);
        }

        return $this->addForm($formName);
    }

    public function isExists($formName)
    {
        if (array_key_exists($formName, $this->_forms)) {
            return $this->_forms[$formName];
        }

        return false;
    }

    public function getCached($formName)
    {
        $cache = Zend_Registry::get('cache');
        if (($form = $cache->load($formName)) === false) {
            $form = $this->addForm($formName);
            try {
                $cache->save($form, $formName, [], '1800');
            } catch (Exception $e) {
                echo 'error';
            }
        } else {
            $this->_forms[$formName] = $form;
        }

        return $form;
    }

    public function addForm($formName)
    {
        $formObj                 = new $formName;
        $this->_forms[$formName] = $formObj;

        return $this->_forms[$formName];
    }

    public function setForm($form)
    {
        $formName                = get_class($form);
        $this->_forms[$formName] = $form;

        return $this;
    }
}