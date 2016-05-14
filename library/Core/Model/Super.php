<?php

/**
 * Base class for Domain object
 * @author     Fedor Petryk
 * @package    Core_Model
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
abstract class Core_Model_Super
{
    /**
     * Object data (Database fields) as field_name => val (null)
     *
     * @var array
     */
    protected $_data = [];

    /**
     *
     * Error value if occured
     *
     * @var String
     */
    protected $_error = [];

    /**
     * Countructs domain object
     *
     * @param array $data | of fields or object for object creation | $data
     */
    public function __construct($data = null)
    {
        $this->extender();
        if (is_array($data) || is_object($data)) {
            $this->populate($data);
        }
        $this->_init();
    }

    /**
     *  perfom properties extensios
     */
    public function extender()
    {
        if (get_class($this) != 'Core_Model_Super') {
            $refclass  = new ReflectionClass($this);
            $refparent = $refclass->getParentClass();
            $defProps  = $refparent->getDefaultProperties();

            $data        = ($defProps['_data']);
            $this->_data = array_merge($data, $this->_data);
        }
    }

    /**
     * @param $data
     *
     * @return bool
     * @throws Exception
     */
    public function populate($data)
    {
        if ($data instanceof Zend_Db_Table_Row) {
            $data = $data->toArray();
        } else {
            if (is_object($data)) {
                $data = (array)$data;
            }
        }
        if (!is_array($data)) {
            throw new Exception('Initial data must be an array or object');
        }

        foreach ($data as $key => $value) {
            $this->$key = $value;
        }

        return true;
    }

    /**
     * Runs after object population
     *
     * @return void
     */
    protected function _init()
    {
    }

    /**
     *
     */
    public function clear()
    {
        foreach ($this->_data as $key => $d) {
            $this->_data[$key] = null;
        }
    }

    /**
     * The magic get function
     * Checks whether $name exists in $_data and returns value
     *
     * @param String $name | $name
     *
     * @return String
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->_data)) {
            return $this->_data[$name];
        }

        return null;
    }

    /**
     * The magic set function
     * Checks whether $name exists in $_data
     *
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->_data)) {
            $this->_data[$name] = $value;
        }
    }

    /**
     * The magic isset function
     *
     * @param $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    /**
     * The magic unset function
     *
     * @param param name String | $name
     */
    public function __unset($name)
    {
        if (isset($this->$name)) {
            $this->_data[$name] = null;
        }
    }

    /**
     * Returns error
     * @return String
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * Sets error
     *
     * @param String | $error
     */
    public function setError($error)
    {
        $this->_error = $error;
    }

    /**
     * Returns array representation of object dada
     * @return array
     */
    public function toArray()
    {
        if (!empty($this->_data)) {
            return $this->_data;
        }
    }

    /**
     *
     * returns keys of objectr
     *
     * @return array
     */
    public function getDataKeys()
    {
        return array_keys($this->_data);
    }

    /**
     * @return mixed
     */
    public function getFirstProperty()
    {
        $data  = array_reverse($this->_data);
        $data  = array_keys($data);
        $first = array_pop($data);

        return $this->_data[$first];
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function getProperty($key)
    {
        $data = $this->_data;

        return $data[$key];
    }

    /**
     * @return mixed
     */
    public function getFirstPropertyKey()
    {
        $reversed = array_reverse($this->_data);
        $keys     = array_keys($reversed);
        $first    = array_pop($keys);

        return $first;
    }

    /**
     * Prepare model data before saving
     *
     * @return void
     */
    public function prepare()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return get_class($this);
    }
}