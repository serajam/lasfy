<?php

/**
 * Created by JetBrains PhpStorm.
 * User: Voronoy
 * Date: 28.08.13
 * Time: 22:30
 * To change this template use File | Settings | File Templates.
 */
class Core_Settings_Settings
{
    const DEFAULT_LANG = 'default_lang';

    protected static $_settings = [];

    public static function getDefaultLanguage()
    {
        $settings        = self::getGroupSettings('site_settings');
        $defaultLangName = $settings[Core_Settings_Settings::DEFAULT_LANG];
        $types           = Zend_Registry::get('types');

        return $types['language'][$defaultLangName];
    }

    public static function getGroupSettings($group)
    {
        if (array_key_exists($group, self::$_settings)) {
            return self::$_settings[$group];
        }
        $db     = Zend_Registry::get('DB');
        $select = $db->select()
            ->from('SystemSettings')
            ->where('paramGroup = ?', $group);
        $result = $db->fetchAll($select);
        if ($result) {
            $settings = [];
            foreach ($result as $res) {
                $settings[$res['paramName']] = $res['paramValue'];
            }
            self::$_settings[$group] = $settings;

            return $settings;
        }

        return false;
    }
}