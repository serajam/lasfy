<?php

/**
 *
 * Settings getter singleton class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_Model_Settings
{
    /**
     * The instance of class
     *
     * @var Core_Model_User
     */
    protected static $_instance = null;

    protected $_moduleDi = [];

    /**
     * Singleton cant have constructor
     */
    public function __construct()
    {
    }

    /**
     * Returns an inctance of this class
     *
     * @return Core_Model_User
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public static function settings($param)
    {
        $config = Zend_Registry::get('appConfig');

        return $config[$param];
    }

    public static function types($param)
    {
        $config = Zend_Registry::get('types');

        return $config[$param];
    }

    public static function type($type, $index)
    {
        $config = Zend_Registry::get('types');
        if (array_key_exists($type, $config)) {
            if (array_key_exists($index, $config[$type])) {
                return $config[$type][$index];
            }
        }

        return $index;
    }

    /**
     * Singleton cant be cloned
     */
    public function __clone()
    {
    }

    public function getModuleDi()
    {
        return $this->_moduleDi;
    }

    public function setModuleDi($moduleDi)
    {
        $this->_moduleDi = $moduleDi;
    }

    public function getModuleDiSet($strategy)
    {
        return $this->_moduleDi[$strategy];
    }
}