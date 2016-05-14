<?php

/**
 * Extended Form with new decorators and translated errors to russion lang
 *
 * @author     Aleksey Kagarlykskiy
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_Translation
{
    protected $_translation = [];

    /**
     * Db gateway
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db = null;

    /**
     * Initializes DB from registry
     */
    public function init($db = null, $lang = 'ru')
    {
        $cache     = Zend_Registry::get('cache');
        $this->_db = empty($db) ? Zend_Registry::get('DB') : $db;
        $cacheKey  = 'trans_' . $lang;

        if (!($result = $cache->load($cacheKey))) {
            $select             = $this->_db->select()
                ->from('Translation', ['code', 'caption'])
                ->where('lang = ?', $lang);
            $result             = $this->_db->fetchPairs($select);
            $this->_translation = $result;
            try {
                $t = $cache->save($result, $cacheKey, [], '3600');
            } catch (Exception $e) {
                error_log('Could not save data in memcache; Key: ' . $cacheKey);
                error_log('Reason: ' . $e);
            }
        } else {
            $this->_translation = $result;
        }
    }

    public function translatePairs($array)
    {
        $translated = [];
        foreach ($array as $key => $value) {
            $translated[$key] = $this->get($value);
        }

        return $translated;
    }

    public function get($code)
    {
        if (!is_int($code) && !is_string($code)) {
            return $code;
        }
        if (isset($this->_translation[$code])) {
            return $this->_translation[$code];
        }

        return $code;
    }

    public function getFromDb($code, $lang)
    {
        $select = $this->_db->select()
            ->from('translation', ['code', 'caption'])
            ->where('lang = ?', $lang)
            ->where('code = ?', $code);

        $result = $this->_db->fetchPairs($select);

        if (empty($result)) {
            return $code;
        }

        return $result[$code];
    }

    /**
     * @return array
     */
    public function getTranslation()
    {
        return $this->_translation;
    }
}