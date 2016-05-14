<?php

/**
 *
 * Mail db table
 *
 * @author Fedor Petryk
 *
 */
class Core_DbTable_Mail extends Core_DbTable_Base
{
    /** Table name */
    protected $_name = 'SystemMessages';

    protected $_primary = 'mailId';

    public static function getMail($name, $lang = 'en')
    {
        $db = Zend_Registry::get('DB');
        if ($db) {
            $select = $db->select()
                ->from('SystemMessages')
                ->where('mailCode  = ?', $name)
                ->where('lang  = ?', $lang)
                ->query();

            return $select->fetch();
        } else {
            $select = self::getDefaultAdapter()->select()
                ->from('SystemMessages')
                ->where('mailCode  = ?', $name)
                ->where('lang  = ?', $lang)
                ->query();

            return $select->fetch();
        }
    }
}