<?php

/**
 * Mapper Super Type
 * Base functionality
 *
 * @author     Fedor Petryk
 * @package    Core_Mapper
 * @subpackage Services
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_Mapper_Super
{
    /**
     * Database table object
     *
     * @var Core_Model_DbTable_Base
     */
    protected $_dbTable;

    /**
     * The name of the table class
     *
     * @var string
     */
    protected $_tableName = "Core_DbTable_Base";

    /**
     * Model name class
     *
     * @var String
     */
    protected $_rowClass;

    /**
     * Model name
     *
     * @var Core_Model_Super
     */
    protected $_domainObject;

    /**
     * Collection object
     *
     * @var Core_Model_Collection_Super
     */
    protected $_collectionClass = 'Core_Collection_Super';

    /**
     * Collection object
     *
     * @var Core_Model_Collection_Super
     */
    protected $_domainObjectCollection;

    /**
     * Filters applied for queries
     *
     * @var mixed
     */
    protected $_filters;

    /**
     * Alias for joined filters
     *
     * @var mixed
     */
    protected $_filtersAlias;

    /**
     * The constructor initiates table access class
     *
     */
    public function __construct()
    {
        $this->initDependencies();
    }

    /**
     * @throws Exception
     *
     * @author Fedor Petryk
     */
    public function initDependencies()
    {
        if (null != $this->_tableName) {
            $this->setDbTable($this->_tableName);
        }
    }

    /**
     * @param $filters
     *
     * @author Fedor Petryk
     */
    public function addSqlFilters($filters)
    {
        $this->_filters = $filters;
    }

    /**
     * Return row class name
     *
     * @return String
     */
    public function getRowClass()
    {
        return $this->_rowClass;
    }

    /**
     * @param $className
     */
    public function setRowClass($className)
    {
        $this->_rowClass = $className;

        return $this;
    }

    /**
     * Creates table object
     *
     * @var string - table class name
     */
    public function setDefaultDbTable()
    {
        $this->setDbTable($this->_tableName);

        return $this;
    }

    /**
     * Returns domain object
     *
     * @return Core_Model_Super
     */
    public function getDomainObject()
    {
        return $this->_domainObject;
    }

    /**
     * Returns domain object
     *
     * @param Core_Model_Super
     */
    public function setDomainObject($class)
    {
        $this->_domainObject = $class;
    }

    /**
     * Run query
     *
     * @param The query |$queries
     *
     * @return the result of operations|boolean
     */
    public function runQuery($query)
    {
        $db = $this->getDbTable()->getAdapter();
        $db->beginTransaction();

        try {
            $db->query($query);
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();

            return $e->getMessage();
        }

        return true;
    }

    /**
     * Returns table object
     *
     * @return Core_DbTable_Base
     */
    public function getDbTable()
    {
        return $this->_dbTable;
    }

    /**
     * Creates table object
     *
     * @var string - table class name
     */
    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $db      = $dbTable;
            $dbTable = new $db();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception(Core_Messages_Message::getMessage(101, [0 => __CLASS__]));
        }
        $this->_dbTable = $dbTable;

        return $this;
    }

    /**
     * Run queries in array
     *
     * @param The array of queries|$queries
     *
     * @return the result of operations|boolean
     */
    public function runQueries($queries)
    {
        $db = $this->getDbTable()->getAdapter();
        $db->beginTransaction();
        try {
            foreach ($queries as $q) {
                $db->query($q);
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();

            return $e->getMessage();
        }

        return true;
    }

    /**
     * Returns domain object
     *
     * @param Core_Model_Super
     */
    public function setCollectionClass($class)
    {
        $this->_collectionClass = $class;

        return $this;
    }

    /**
     *
     * Save domain object as is
     *
     * @param Core_Model_Super
     *
     * @return Core_Model_Super
     */
    public function objectSave($object)
    {
        $table     = $this->getDbTable();
        $data      = $table->cleanArray($object->toArray());
        $newObject = false;

        $pk  = $table->getPrimary();
        $opk = $object->getFirstPropertyKey();
        if ($object->$opk == null) {
            $newObject = true;
        }

        if ($newObject) {
            try {
                $table->insert($data);
            } catch (Zend_Exception $e) {
                $object->setError("Ошибка: " . $e->getMessage() . "\n");

                return false;
            }
            $ids          = $table->getAdapter()->lastInsertId();
            $opk          = $object->getFirstPropertyKey();
            $object->$opk = $ids;

            return $object;
        } else {
            try {
                $where = [];
                if (is_array($pk)) {
                    foreach ($pk as $key) {
                        $where[$key . ' = ?'] = $object->$key;
                    }
                    $row = $this->fetchByFields($where, false);
                } else {
                    $row   = $this->fetchById($object->$pk);
                    $where = [$pk . ' = ?' => $object->$pk];
                }

                if (!empty($row)) {
                    foreach ($data as $key => $val) {
                        if ($val === null) {
                            $object->$key = $row->$key;
                        }
                    }
                    $table->update($object->toArray(), $where);
                } else {
                    $table->insert($data);
                }

                return $object;
            } catch (Zend_Exception $e) {
                $object->setError("Ошибка: " . $e->getMessage() . "\n");
            }

            return $object;
        }

        return $object;
    }

    /**
     *
     * Fetches row by db key and it's val
     *
     * @param db key | $key
     * @param db key value
     *
     * @return Core_Model_Super
     */
    public function fetchByFields(array $fields, $prepare = true)
    {
        $preparedFields = [];
        if ($prepare) {
            foreach ($fields as $field => $value) {
                $preparedFields[$field . ' = ?'] = $value;
            }
        } else {
            $preparedFields = $fields;
        }

        $result = $this->getDbTable()
            ->fetchRow($preparedFields);
        if (!empty($result)) {
            $entry = new $this->_rowClass;
            $entry->populate($result);

            return $entry;
        } else {
            return false;
        }
    }

    /**
     *
     * Fetches row by id of primary key
     *
     * @param $id
     *
     * @return Core_Model_Super
     */
    public function fetchById($id)
    {
        $sql = $this->createFetchQuery((int)$id);
        $this->_addFetchRules($sql);
        $result = $this->getDbTable()
            ->fetchRow($sql);
        if (!empty($result)) {
            $entry = new $this->_rowClass;
            $entry->populate($result);

            return $entry;
        }

        return false;
    }

    /**
     * @param $id
     *
     * @return Zend_Db_Select
     *
     * @author Fedor Petryk
     */
    public function createFetchQuery($id)
    {
        $pk = $this->getDbTable()->getPrimary();
        if (is_array($pk)) {
            $pk = $pk[1];
        }

        return $this->getDbTable()
            ->select()
            ->where($pk . ' = "' . $id . '"');
    }

    /**
     * @param $sql
     *
     * @author Fedor Petryk
     */
    protected function _addFetchRules($sql)
    {
    }

    /**
     *
     * Save domain object as is
     *
     * @param Core_Model_Super
     *
     * @return Core_Model_Super
     */
    public function saveFields($object, $fields)
    {
        $table = $this->getDbTable();
        $data  = $object->toArray();
        $data  = $table->cleanArray($data);
        $pk    = $table->getPrimary();

        if (is_array($pk)) {
            $pk = $pk[1];
        }

        try {
            $where = $pk . ' = "' . $object->$pk . '"';
            $row   = $this->fetchById($object->$pk);
            if (!empty($row)) {
                foreach ($fields as $key) {
                    $row->$key = $object->$key;
                }
                $table->update($row->toArray(), $where);
            } else {
                $table->insert($object->toArray());
            }

            return $object;
        } catch (Zend_Exception $e) {
            $object->setError("Ошибка: " . $e->getMessage() . "\n");
        }

        return $object;
    }

    /**
     *
     * Counts all rows in a table
     *
     * @param table name | $table_name
     *
     * @return String
     */
    public function countAll($table_name = null)
    {
        if ($table_name === null) {
            $table_name = $this->_dbTable->getName();
        }

        return $this->getDbTable()->countAll($table_name);
    }

    /**
     * @param  $page  int
     *
     * @return mixed | Core_Collection_Super
     */
    public function fetchAll($page = 0)
    {
        if ($page) {
            $table   = $this->getDbTable()->info('name');
            $adapter = new Zend_Paginator_Adapter_DbTableSelect($this->getDbTable()->select()->from($table));
            $adapter->setRowCount(
                $this->getDbTable()->select()
                    ->from(
                        $table,
                        [
                            Zend_Paginator_Adapter_DbSelect::ROW_COUNT_COLUMN => 'count(*)'
                        ]
                    )
            );
            $paginator  = new Zend_Paginator($adapter);
            $config     = Zend_Registry::get('appConfig');
            $collection = new $this->_collectionClass;
            $paginator->setCurrentPageNumber($page);
            $paginator->setItemCountPerPage($config['itemsPerPage']);
            $collection->populate($paginator->getCurrentItems());
            $collection->setPaginator($paginator);

            return $collection;
        }
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = [];
        foreach ($resultSet as $row) {
            $entry = new $this->_rowClass;
            $entry->populate($row);
            $entries[] = $entry;
        }
        $collection = new $this->_collectionClass($entries);

        return $collection;
    }

    /**
     *
     * Fetches all by where statement
     *
     * @param Where string | $where
     *
     * @return Core_Model_Collection_Super
     */
    public function fetchAllWhere($where)
    {
        $resultSet = $this->getDbTable()->fetchAll($where);
        $entries   = [];
        foreach ($resultSet as $row) {
            $entry = new $this->_rowClass;
            $entry->populate($row);
            $entries[] = $entry;
        }
        $collection = new $this->_collectionClass($entries);

        return $collection;
    }

    /**
     *
     * Fetches all ordered
     *
     * @param Order by table field | $name
     * @param ASC   or DESC | $dis
     *
     * @return Core_Model_Collection_Super
     */
    public function fetchAllOrdered($name, $dir)
    {
        $resultSet = $this->getDbTable()->fetchAll(null, $name . ' ' . $dir);
        $entries   = [];
        foreach ($resultSet as $row) {
            $entry = new $this->_rowClass;
            $entry->populate($row);
            $entries[] = $entry;
        }
        $collection = new $this->_collectionClass($entries);

        return $collection;
    }

    /**
     *
     * Fetches all ordered
     *
     * @param Order  by table field | $name
     * @param ASC    or DESC | $dis
     * @param $where | where condition
     *
     * @return Core_Model_Collection_Super
     */
    public function fetchAllOrderedWhere($name, $dir, $where)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $name . ' ' . $dir);
        $entries   = [];
        foreach ($resultSet as $row) {
            $entry = new $this->_rowClass;
            $entry->populate($row);
            $entries[] = $entry;
        }
        $collection = new $this->_collectionClass($entries);

        return $collection;
    }

    /**
     *
     * Fetches all by db key and it's val
     *
     * @param db key | $key
     * @param db key value | $val
     *
     * @return Core_Model_Collection_Super
     */
    public function fetchAllByKey($key, $val)
    {
        $resultSet = $this->getDbTable()->fetchAll($key . ' = "' . $val . '"');

        if (empty($resultSet)) {
            return false;
        }

        $entries = [];
        foreach ($resultSet as $row) {
            $entry = new $this->_rowClass;
            $entry->populate($row);
            $entries[] = $entry;
        }
        $collection = new $this->_collectionClass($entries);

        return $collection;
    }

    /**
     *
     * Fetches row by db key and it's val
     *
     * @param db key | $key
     * @param db key value | $val
     *
     * @return Core_Model_Super
     */
    public function fetchByKey($key, $val)
    {
        $result = $this->getDbTable()
            ->fetchRow($key . ' = "' . $val . '"');
        if (!empty($result)) {
            $entry = new $this->_rowClass;
            $entry->populate($result);

            return $entry;
        } else {
            return false;
        }
    }

    /**
     *    Gets max id as for the versioning
     *
     * @return int
     */
    public function getLastKeyValue($key, $name)
    {
        if (empty($name)) {
            $name = $this->getDbTable()->getName();
        }

        $db     = $this->getDbTable()->getAdapter();
        $select = $db->select()
            ->from($name, 'MAX(' . $key . ') AS max');

        return $db->fetchOne($select);
    }

    /**
     * @param $key
     * @param $name
     * @param $param
     * @param $val
     *
     * @return string
     *
     * @author Fedor Petryk
     */
    public function getLastKeyValueWhere($key, $name, $param, $val)
    {
        if (empty($name)) {
            $name = $this->getDbTable()->getName();
        }

        $db     = $this->getDbTable()->getAdapter();
        $select = $db->select()
            ->from($name, 'MAX(' . $key . ') AS max');
        if (!empty($val) and !empty($param)) {
            $select->where($param . '= ?', $val);
        }

        return $db->fetchOne($select);
    }

    /**
     *
     * The validation method for validator element Unique
     * Checks whethere value exixts in table field or not
     *
     * @param String $table | table witch to checl
     * @param String $field | field for checking
     * @param String $value | value of the field
     *
     * @return boolean
     */
    public function checkUnique($table, $field, $value, $id, $primary)
    {
        $db = $this->getDbTable()->getAdapter();
        if ($table && $field && $value && $primary && $id) {
            $select = $db->select()
                ->from($table)
                ->where($field . ' = "' . $value . '" AND ' . $primary . ' != "' . $id . '"');
        } elseif ($table && $field && $value) {
            $select = $db->select()
                ->from($table)
                ->where($field . ' = "' . $value . '"');
        } else {
            return true;
        }

        $row = $db->fetchRow($select);

        if (empty($row)) {
            return true;
        }

        return false;
    }

    /**
     * @param $table
     * @param $field
     * @param $value
     * @param $id
     * @param $primary
     *
     * @return bool
     *
     * @author Fedor Petryk
     */
    public function checkUniqueField($table, $field, $value, $id, $primary)
    {
        $db     = $this->getDbTable()->getAdapter();
        $select = $db->select()
            ->from($table)
            ->where($field . ' = "' . $value . '" AND ' . $primary . ' = "' . $id . '"');
        $row    = $db->fetchRow($select);

        if (empty($row)) {
            return true;
        }

        return false;
    }

    /**
     * @param $table
     * @param $params
     *
     * @return bool
     *
     * @author Fedor Petryk
     */
    public function checkExistingRow($table, $params)
    {
        $db     = $this->getDbTable()->getAdapter();
        $select = $db->select()->from($table);

        if (empty($params)) {
            return false;
        } else {
            foreach ($params as $key => $val) {
                $select->where($key . ' = ?', $val);
            }
        }
        $row = $db->fetchRow($select);

        if (empty($row)) {
            return true;
        }

        return false;
    }

    /**
     * @param            $table
     * @param            $key
     * @param            $value
     * @param bool|false $where
     *
     * @return mixed
     *
     * @author Fedor Petryk
     */
    public function fetchPairs($table, $key, $value, $where = false)
    {
        $db  = $this->getAdapter();
        $sql = $db->select()->from($table, [$key, $value]);
        if ($where != false) {
            $sql->where($where);
        }
        $pairs = $db->fetchPairs($sql);

        return $pairs;
    }

    /**
     * @param $data
     *
     * @return bool
     *
     * @author Fedor Petryk
     */
    public function addMailer($data)
    {
        $this->getAdapter()->insert('Mailer', $data);

        return true;
    }

    /**
     * @param $code
     *
     * @return bool|Core_Model_EmailTemplate
     *
     * @author Fedor Petryk
     */
    public function getMailTemplate($code)
    {
        $sql = $this->getAdapter()->select()
            ->from('SystemMessages')
            ->where('mailCode = ?', $code);
        $res = $this->getAdapter()->fetchRow($sql);

        if (!empty($res)) {
            $template = new Core_Model_EmailTemplate($res);

            return $template;
        }

        return false;
    }

    /**
     * @param $code
     *
     * @return bool|Core_Model_SystemMessage
     *
     * @author Fedor Petryk
     */
    public function getMailMessage($code)
    {
        $sql = $this->getAdapter()->select()
            ->from('SystemMessages')
            ->where('mailCode = ?', $code);
        $res = $this->getAdapter()->fetchRow($sql);

        if (!empty($res)) {
            $template = new Core_Model_SystemMessage($res);

            return $template;
        }

        return false;
    }

    /**
     * @param $table
     * @param $data
     *
     * @return mixed
     *
     * @author Fedor Petryk
     */
    public function bind($table, $data)
    {
        return $this->getAdapter()->insert($table, $data);
    }

    /**
     * @param $table
     * @param $data
     *
     * @return int
     *
     * @author Fedor Petryk
     */
    public function unBind($table, $data)
    {
        return $this->getAdapter()->delete($table, $data);
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->_tableName;
    }

    /**
     * @param string $tableName
     */
    public function setTableName($tableName)
    {
        $this->_tableName = $tableName;
    }

    /**
     * @param $lang
     *
     * @return array
     *
     * @author Fedor Petryk
     */
    public function getSiteMenu($lang)
    {
        $db  = $this->getAdapter();
        $sql = $db->select()
            ->from(['sm' => 'SiteMenu'])
            ->joinLeft(['hp' => 'Pages'], 'hp.pageId = sm.contentId', ['hp.slug'])
            ->where('sm.active = ?', 1)
            ->where('sm.lang = ?', $lang)
            ->order('sm.position ASC')
            ->order('sm.parentId ASC');

        $menuItems = $db->fetchAll($sql);
        $menuTree  = [];
        $this->buildMenuTree($menuItems, $menuTree);

        return $menuTree;
    }

    /**
     * @param array $menuItems Input linear menu items
     * @param array $menuTree Resulting tree
     *
     * @author Fedor Petryk
     */
    protected function buildMenuTree($menuItems, &$menuTree)
    {
        foreach ($menuItems as $key => $item) {
            if ($item['parentId'] && isset($menuTree[$item['parentId']])) {
                $menuTree[$item['parentId']]['subPage'][$item['menuId']]['page'] = $item;
                unset($menuItems[$key]);
            } elseif (!$item['parentId']) {
                unset($menuItems[$key]);
                $menuTree[$item['menuId']]['page'] = $item;
            }
        }

        if (count($menuItems)) {
            foreach ($menuTree as $key => &$item) {
                if (isset($item['subPage'])) {
                    $this->buildMenuTree($menuItems, $item['subPage']);
                }
            }
        }
    }

    /**
     * @param $field
     *
     * @return int|string
     *
     * @author Fedor Petryk
     */
    public function getMaximumValue($field)
    {
        $table  = $this->getDbTable();
        $db     = $this->getDbTable()->getAdapter();
        $sql    = $db->select()->from($table->getName(), ['MAX(' . $field . ')']);
        $result = $db->fetchOne($sql);

        if ($result) {
            return $result;
        }

        return 0;
    }

    /**
     * @param bool|false $pageSlug
     * @param bool|false $pageId
     * @param            $lang
     *
     * @return bool|Page
     * @throws Exception
     *
     * @author Fedor Petryk
     */
    public function getPage($pageSlug = false, $pageId = false, $lang)
    {
        $db     = $this->getAdapter();
        $select = $db->select()
            ->from(['p' => 'Pages'])
            ->joinLeft(['sm' => 'SiteMenu'], 'sm.contentId = p.pageId', ['type', 'menuId'])
            ->where('sm.lang = ?', $lang);

        if (!$pageSlug && !$pageId && !$lang) {
            return false;
        }

        if (!empty($pageSlug)) {
            $select->where('p.slug = ?', $pageSlug);
        } elseif (!empty($pageId)) {
            $select->where('p.pageId = ?', $pageId);
        }

        $res = $db->fetchRow($select);

        if (!empty($res)) {
            $page = new Page();
            $page->populate($res);

            return $page;
        }

        /*    else
            {
              $select = $db->select()
                ->from(['p' => 'Pages'])
                ->order('p.pageId ASC')
                ->limit(1);
    
              $res = $db->fetchRow($select);
    
              if (!empty($res))
              {
                $page = new Page();
                $page->populate($res);
    
                return $page;
              }
            }*/

        return false;
    }

    /**
     * @param $type
     *
     * @return bool|Core_Collection_CustomContent
     *
     * @author Fedor Petryk
     */
    public function getCustomContent($type)
    {
        $db  = $this->getAdapter();
        $sql = $db->select()->from(['cc' => 'CustomContent'])->order('position ASC');
        if ($type) {
            $sql->where('type = ?', $type);
        }

        $result = $db->fetchAll($sql);
        if (empty($result)) {
            return false;
        }

        return new Core_Collection_CustomContent($result);
    }

    /**
     *
     * Returns current user role
     *
     * @param Core_Model_User $user
     *
     * @return boolean
     */
    public function getUserRole(Core_Model_User $user)
    {
        $db     = $this->getDbTable()->getAdapter();
        $select = $db->select()->from(['ur' => 'UsersRoles'], [])
            ->joinLeft(
                ['r' => 'Roles'],
                'ur.roleId = r.roleId',
                ['r.roleCode', 'r.roleName']
            )
            ->joinLeft(
                ['sm' => 'SystemModules'],
                'sm.id = r.defaultModule',
                ['sm.moduleCode as defaultModule']
            )
            ->joinLeft(
                ['rr' => 'Resources'],
                'rr.resourceId = r.defaultController',
                ['rr.resourceCode as defaultController']
            )
            ->joinLeft(
                ['a' => 'Rights'],
                'a.rightId = r.defaultAction',
                ['a.action as defaultAction']
            )
            ->where('ur.userId = ?', $user->userId);

        $result = $db->fetchRow($select);

        if (empty($result)) {
            return false;
        }

        $user->role          = $result['roleCode'];
        $user->defaultModule = $result['defaultModule'];
        list($mod, $cntrl) = explode(':', $result['defaultController']);
        $user->defaultController = $cntrl;
        $user->defaultAction     = $result['defaultAction'];
        $user->roleName          = $result['roleName'];

        return true;
    }

    /**
     * @param $userId
     *
     * @return bool|User
     */
    public function getUser($userId)
    {
        $db  = $this->getAdapter();
        $sql = $db->select()->from('Users')
            ->where('userId = ?', $userId);

        $result = $db->fetchRow($sql);
        if (empty($result)) {
            return false;
        }

        return new User($result);
    }

    /**
     * @param $userId
     *
     * @return bool|Core_Model_Company
     */
    public function getCompanyByUserId($userId)
    {
        $db  = $this->getAdapter();
        $sql = $db->select()->from('Companies')
            ->where('userId = ?', $userId);

        $result = $db->fetchRow($sql);
        if (empty($result)) {
            return false;
        }

        return new Core_Model_Company($result);
    }

    /**
     * Get Tag Ids with or without name
     *
     * @param           $tagsNamesArray
     * @param bool|true $withName
     *
     * @return array
     */
    public function getExistedTagsByName($tagsNamesArray, $withName = true)
    {
        $fields = ['t.tagId'];
        $withName ? array_push($fields, 't.tagName') : array_push($fields, 't.tagId');

        $db     = $this->getAdapter();
        $sql    = $db->select()
            ->from(['t' => 'Tags'], $fields)
            ->where('t.enable = 1')
            ->where('t.tagName IN ("' . implode('","', $tagsNamesArray) . '")');
        $result = $db->fetchPairs($sql);
        if (!empty($result)) {
            return $result;
        }

        return [];
    }

    /**
     * @param $select
     *
     * @author Fedor Petryk
     */
    protected function _applySqlFilters($select)
    {
        $db = $this->getAdapter();
        if (!empty($this->_filters)) {
            foreach ($this->_filters as $filter => $val) {
                if (!array_key_exists($filter, $this->_filtersAlias)) {
                    continue;
                }

                if (!$val > 0) {
                    continue;
                }

                $select->where(
                    $this->_filtersAlias[$filter] . '.'
                    . $filter . ' = ' . $db->quote($val)
                );
            }
        }
    }

    /**
     * Returns table adapter
     *
     * @return Zend_Db_Table_Abstract
     */
    public function getAdapter()
    {
        return $this->_dbTable->getAdapter();
    }

    /**
     * @param $rows
     *
     * @return mixed
     *
     * @author Fedor Petryk
     */
    protected function _parseCollection($rows)
    {
        $collection = new $this->_collectionClass;
        if (!empty($rows)) {
            $collection->populate($rows);
        }

        return $collection;
    }

    /**
     * @param $row
     *
     * @return bool
     *
     * @author Fedor Petryk
     */
    protected function _createModel($row)
    {
        if (empty($row) || !is_array($row)) {
            return false;
        }
        $model = new $this->_rowClass;
        $model->populate($row);

        return $model;
    }
}