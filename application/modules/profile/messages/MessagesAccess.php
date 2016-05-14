<?php

/**
 * Class MessagesAccess
 */
class MessagesAccess
{
    const VACANCY_TYPE = 'vacancy';
    const RESUME_TYPE = 'resume';

    protected static $replierId;

    /**
     * Count all user new messages
     * @return string
     * @throws Zend_Exception
     */
    static public function countNewMessages()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            return false;
        }

        /** @var Zend_Db_Adapter_Abstract $db */
        $db     = Zend_Registry::get('DB');
        $result = $db->fetchAll(self::getBaseQuery());
        $total  = 0;
        foreach ($result as $val) {
            ++$total;
        }

        return $total;
    }

    /**
     * Basic query for getting messages
     * @return Zend_Db_Select
     * @throws Zend_Exception
     */
    protected static function getBaseQuery()
    {
        $userId = Core_Model_User::getInstance()->userId;
        $db     = Zend_Registry::get('DB');
        $sql    = $db->select()
            ->from(['um' => 'UsersMessages'], ['COUNT( DISTINCT um.userFrom)'])
            ->where('um.userTo = ?', $userId)
            ->where('um.new = 1')
            ->group('um.addType')
            ->group('um.addId')
            ->group('um.userFrom');

        return $sql;
    }

    static public function getAddMessagesByUser($id)
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            return false;
        }

        /** @var Zend_Db_Adapter_Abstract $db */
        $db  = Zend_Registry::get('DB');
        $sql = self::getBaseQuery();
        $sql->where('um.addId = ?', $id);
        $sql->reset(Zend_Db_Select::COLUMNS);
        $sql->columns(['um.userFrom', 'COUNT( DISTINCT um.userFrom)']);
        $total = $db->fetchPairs($sql);

        return $total;
    }

    /**
     * Get adds messages by  type
     *
     * @param array $ids
     * @param       $type
     *
     * @return array|bool
     * @throws Zend_Db_Select_Exception
     * @throws Zend_Exception
     */
    static public function getAddNewMessages(array $ids, $type)
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            return false;
        }

        /** @var Zend_Db_Adapter_Abstract $db */
        $db  = Zend_Registry::get('DB');
        $sql = self::getBaseQuery();
        $sql->where('um.addType = ?', $type)
            ->where('um.addId IN (?)', $ids);
        $sql->reset(Zend_Db_Select::COLUMNS);
        $sql->columns(['um.addId', 'um.userFrom']);
        $result = $db->fetchAll($sql);

        $total = [];
        foreach ($result as $r) {
            if (!isset($total[$r['addId']])) {
                $total[$r['addId']] = 1;
            } else {
                ++$total[$r['addId']];
            }
        }

        return $total;
    }

    /**
     * Get all adds messages
     *
     * @param array $ids
     *
     * @return array|bool
     * @throws Zend_Db_Select_Exception
     * @throws Zend_Exception
     */
    static public function getAllAddNewMessages(array $ids)
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            return false;
        }

        /** @var Zend_Db_Adapter_Abstract $db */
        $db  = Zend_Registry::get('DB');
        $sql = self::getBaseQuery();
        $sql->where('um.addId IN (?)', $ids);
        $sql->reset(Zend_Db_Select::COLUMNS);
        $sql->columns(['um.addId', 'um.userFrom']);
        $result = $db->fetchAll($sql);

        $total = [];
        foreach ($result as $r) {
            if (!isset($total[$r['addId']])) {
                $total[$r['addId']] = 1;
            } else {
                ++$total[$r['addId']];
            }
        }

        return $total;
    }

    /**
     * @return mixed
     */
    public static function getReplierId()
    {
        return self::$replierId;
    }

    /**
     * @param mixed $replierId
     */
    public static function setReplierId($replierId)
    {
        self::$replierId = $replierId;
    }
}