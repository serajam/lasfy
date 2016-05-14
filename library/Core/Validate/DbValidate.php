<?php

/**
 *
 * Custom unique field validation against non current
 *
 * @author Fedor Petry
 *
 */
class Core_Validate_DbValidate extends Zend_Validate_Abstract
{
    /**
     * @var Core_DbTable_Validation
     */
    protected $_dbTable;

    protected $_dbName;

    public function init($table)
    {
        $this->_dbTable = new Core_DbTable_Validation();
        $this->_dbTable->setTable($table);
    }

    public function isValid($val)
    {
    }
}
