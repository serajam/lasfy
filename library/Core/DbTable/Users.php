<?php

/**
 *
 * Users DbTable Class
 *
 * @author user
 *
 */
class Core_DbTable_Users extends Core_DbTable_Base
{
    /** Table name */
    protected $_name = 'Users';

    protected $_primary = 'userId';

    public function deleteById($id)
    {
        parent::delete($this->getAdapter()->quoteInto('userId = ?', $id));
    }
}