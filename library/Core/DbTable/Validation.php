<?php

/**
 * Validation DbTable Class
 * @author Fedor Petryk
 */
class Core_DbTable_Validation extends Core_DbTable_Base
{
  public function valueExists($table, $valKey, $val)
  {
    $sql = $this->getAdapter()->select()
        ->from($table, [$valKey])
        ->where($valKey . ' = "' . $val . '"');

    return $this->getAdapter()->fetchOne($sql);
  }

  public function checkIdByField($table, $valKey, $val, $returnField)
  {
    $sql = $this->getAdapter()->select()
        ->from($table, [$returnField])
        ->where($valKey . ' = "' . $val . '"');

    return $this->getAdapter()->fetchOne($sql);
  }

  public function validateOldPassword($userId, $password)
  {
    $sql = $this->getAdapter()->select()
        ->from('Users', ['userId'])
        ->where('userId = ?', $userId)
        ->where('securityCode = ' . $this->getAdapter()->quote($password));

    return $this->getAdapter()->fetchOne($sql);
  }

  public function setTable($table)
  {
    $this->_name = $table;
  }
}
