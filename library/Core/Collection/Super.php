<?php

/**
 *
 * The collection of Domain objects
 *
 * @author     Fedor Petryk
 *
 */
class Core_Collection_Super implements Iterator, Countable
{
    /**
     *
     * Total object count
     *
     * @var int
     */
    protected $_count;

    /**
     *
     * Domain object
     *
     * @var Core_Model_Super
     */
    protected $_domainObjectClass = null;

    protected $_resultSet = [];

    /**
     * @var Zend_Paginator
     */
    protected $_paginator;

    public function __construct($results = null)
    {
        if (isset($results['data'])) {
            $this->populate($results['data']);
        } elseif (is_array($results)) {
            $this->populate($results);
        }
    }

    public function populate($results)
    {

        if (empty($results)) {
            return false;
        }
        foreach ($results as $data) {
            if (is_object($data)) {
                $data = $data->toArray();
            }
            $model = new $this->_domainObjectClass($data);
            $model->populate($data);
            $this->add($model);
        }
    }

    public function add($data)
    {
        $model = $data;
        if (!is_object($data)) {
            $model = new $this->_domainObjectClass;
            $model->populate($data);
        }
        if (empty($this->_resultSet)) {
            $this->_resultSet[] = $model;
        } else {
            array_push($this->_resultSet, $model);
        }

        return $model;
    }

    public function getPairsArray($indexField, $valueField)
    {
        $assocArr = [];
        if ($this->count() == 0) {
            return false;
        }
        foreach ($this as $r) {
            $assocArr[$r->$indexField] = $r->$valueField;
        }

        return $assocArr;
    }

    public function count()
    {
        if (null === $this->_count) {
            $this->_count = count($this->_resultSet);
        }

        return $this->_count;
    }

    public function getIds($key = null)
    {
        if ($this->count() == 0) {
            return false;
        }
        $ids = [];
        foreach ($this as $r) {
            if ($key == null) {
                $ids[] = $r->getFirstProperty();
            } else {
                $ids[] = $r->$key;
            }
        }

        return $ids;
    }

    public function addByKey($array)
    {
        $model                                        = new $this->_domainObjectClass($array);
        $this->_resultSet[$model->getFirstProperty()] = $model;
    }

    public function getByKey($key)
    {
        return isset($this->_resultSet[$key]) ? $this->_resultSet[$key] : null;
    }

    public function addCollection($collection)
    {
        if (empty($collection) || $collection->count() < 1) {
            return false;
        }

        foreach ($collection as $user) {
            $model = new $this->_domainObjectClass;
            $model->populate($user->toArray());
            $this->add($model);
        }
    }

    public function isLast($index)
    {
        if ($this->count() == $index) {
            return true;
        }

        return false;
    }

    public function key()
    {
        return key($this->_resultSet);
    }

    public function next()
    {
        return next($this->_resultSet);
    }

    public function valid()
    {
        return (bool)$this->current();
    }

    public function current()
    {
        if ($this->_resultSet == null) {
            return false;
        }

        if ($this->_resultSet instanceof Iterator) {
            $key = $this->_resultSet->key();
        } else {
            $key = key($this->_resultSet);
        }

        if (array_key_exists($key, $this->_resultSet)) {
            $result = $this->_resultSet[$key];
        } else {
            $result = false;
        }

        return $result;
    }

    public function getFirst()
    {
        if (array_key_exists('0', $this->_resultSet)) {
            $result = $this->_resultSet[0];
        } else {
            $result = false;
        }

        return $result;
    }

    public function getLast()
    {
        $count = $this->count() - 1;

        return $this->_resultSet[$count];
    }

    public function getModelKeys()
    {
        if ($this->count() < 1) {
            return false;
        }

        return $this->current()->getDataKeys();
    }

    /**
     * @return \Core_Model_Super
     */
    public function getDomainObjectClass()
    {
        return $this->_domainObjectClass;
    }

    /**
     * @param \Core_Model_Super $domainObjectClass
     */
    public function setDomainObjectClass($domainObjectClass)
    {
        $this->_domainObjectClass = $domainObjectClass;
    }

    /**
     * @param $method set method name
     * @param $value
     *
     * @return bool
     * set models property globally
     */
    public function setProperty($method, $value)
    {
        if ($this->count() < 1) {
            return false;
        }

        foreach ($this as $model) {
            $model->$method($value);
        }
        $this->rewind();
    }

    public function rewind()
    {
        if ($this->_resultSet != null) {
            return reset($this->_resultSet);
        }

        return false;
    }

    public function setDataValues($data)
    {
        if ($this->count() < 1) {
            return false;
        }

        foreach ($this as $model) {
            foreach ($data as $field => $val) {
                $model->$field = $val;
            }
        }
        $this->rewind();
    }

    public function getItemsArray()
    {
        return array_keys($this->_resultSet);
    }

    public function getItems()
    {
        return $this->_resultSet;
    }

    /**
     * @return \Zend_Paginator
     */
    public function getPaginator()
    {
        return $this->_paginator;
    }

    /**
     * @param \Zend_Paginator $paginator
     */
    public function setPaginator($paginator)
    {
        $this->_paginator = $paginator;
    }

    public function getModelByKey($key, $value)
    {
        foreach ($this as $model) {
            if ($model->$key == $value) {
                return $model;
            }
        }

        return false;
    }
}