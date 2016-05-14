<?php

/**
 *
 * The base table class
 *
 * @author Fedor Petryk
 *
 */
class Core_DbTable_Base extends Zend_Db_Table_Abstract
{
    protected $_name;

    protected $_primary;

    protected $_itemsPerPage = 50;

    protected $_page = 1;

    /**
     * Saves model to db table
     * 1. Map model properties of model to table columns by method "get" of "is"
     * 2. Insert
     *
     * @param $model
     *
     * @return mixed
     * @throws Exception
     * @throws Zend_Db_Table_Exception
     *
     * @author Fedor Petryk
     */
    public function saveModel($model)
    {
        $modelArray = $this->mapModelPropertiesToTableColumns($model);

        return $this->insert($modelArray);
    }

    /**
     * It is not safe to rely on table metadata to map model fields
     * Map model properties of model to table columns by method "get" of "is"
     *
     * @param $model
     *
     * @return array
     * @throws Exception
     * @throws Zend_Db_Table_Exception
     *
     * @author Fedor Petryk
     */
    protected function mapModelPropertiesToTableColumns($model)
    {
        $info = $this->info();
        if (empty($info['cols'])) {
            throw new Exception(__METHOD__ . '. Could not get table columns.');
        }
        $modelArray = [];
        foreach ($info['cols'] as $column) {
            $methodGet = 'get' . ucfirst($column);
            $methodIs  = 'is' . ucfirst($column);
            if (method_exists($model, $methodGet)) {
                $modelArray[$column] = $model->{$methodGet}();
            } elseif (method_exists($model, $methodIs)) {
                $modelArray[$column] = $model->{$methodIs}();
            }
        }

        return $modelArray;
    }

    public function insert(array $data)
    {
        return parent::insert($this->cleanArray($data));
    }

    public function cleanArray($arr)
    {
        return array_intersect_key($arr, array_combine(parent::info('cols'), parent::info('cols')));
    }

    public function setupPagination($sql)
    {
        $sql->limitPage($this->_page, $this->_itemsPerPage);
    }

    public function getPrimary()
    {
        return $this->_primary;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function fetchOneField($field, $id)
    {
        $select = $this->select()
            ->from($this->_name, [$field])
            ->where($this->_primary[1] . ' = ' . $id);

        return parent::getAdapter()->fetchOne($select);
    }

    public function update(array $data, $where)
    {
        return parent::update($this->cleanArray($data), $where);
    }

    public function insertObject($data)
    {
        return parent::insert($this->cleanArray($data->toArray()));
    }

    public function deleteAllByKey($key, $value)
    {
        parent::delete($this->getAdapter()->quoteInto($key . ' = ?', $value));
    }

    public function countAll($table_name = null)
    {
        if (null === $table_name) {
            $table_name = $this->_name;
        }

        $select = $this->select()->from($table_name, ['COUNT(*) as total_rows']);

        return $this->getAdapter()->fetchOne($select);
    }

    public function deleteById($id)
    {
        try {
            $pk = $this->_primary;
            if (is_array($this->_primary)) {
                $pk = $this->_primary[1];
            }
            parent::delete([$pk . ' = ?' => $id]);
        } catch (Exception $e) {

            return $e->getCode() . ' ' . PHP_EOL . $e->getMessage();
        }

        return false;
    }
}
