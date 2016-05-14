<?php

/**
 *
 * The modue blocked checker
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_Modules_Blocker extends Zend_Controller_Plugin_Abstract
{
    /**
     *
     * Checks whethere module was blocked by system
     *
     * @param $moduleName
     */
    public static function isBlocked($moduleCode)
    {
        $cache    = Zend_Registry::get('cache');
        $cacheKey = 'SystemModules_' . $moduleCode;
        if (($module = $cache->load($cacheKey)) === false) {
            $db     = Zend_Registry::get('DB');
            $select = $db->select()->from('SystemModules')
                ->where('moduleCode = ?', $moduleCode);
            $module = $db->fetchRow($select);
            try {
                $cache->save($module, $cacheKey, [], '1800');
            } catch (Exception $e) {
                echo 'error';
            }
        }
        if (empty($module) || $module['blocked'] == 1) {
            return true;
        }

        return false;
    }
}