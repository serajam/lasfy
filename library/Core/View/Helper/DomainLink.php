<?php

/**
 *
 * Domain link class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_View_Helper_DomainLink extends Core_View_Helper_View
{
    protected static $_httpPath = null;

    protected static $_baseHttpPath = null;

    /**
     * @param null       $base
     * @param bool|false $isLangPrefix
     *
     * @return null|string
     * @throws Zend_Exception
     *
     * @author Fedor Petryk
     */
    public function domainLink($base = null, $isLangPrefix = false)
    {
        $config      = Zend_Registry::get('appConfig');
        $defaultLang = Zend_Registry::get('language');
        if ($base == null) {
            $configModule = Zend_Registry::get('config');
            if (self::$_httpPath === null) {
                self::$_httpPath = $config['baseHttpPath'];

                if ($isLangPrefix) {
                    self::$_httpPath .= $defaultLang . '/';
                }

                if (isset($configModule['httpPath'])) {
                    self::$_httpPath .= $configModule['httpPath'];
                }
            }

            return self::$_httpPath;
        } else {
            if (self::$_baseHttpPath === null) {
                self::$_baseHttpPath = $config['baseHttpPath'];
            }

            if ($isLangPrefix) {
                return self::$_baseHttpPath . $defaultLang . '/';
            }

            return self::$_baseHttpPath;
        }
    }
}