<?php

/**
 *
 * Base static errors class
 *
 * @author  Fedor Petryk
 * @package Core_Model
 *
 */
class Core_Messages_Message
{
    /**
     *
     * Gets an error string and replace params if needed
     *
     * @param the code of error int|$code
     * @param the replacment values array|$params
     *
     * @return String
     */
    static public function getMessage($code, $params = null)
    {
        if (empty($params)) {
            return Zend_Registry::isRegistered('translation') ? Zend_Registry::get('translation')->get($code) : $code;
        }
        $error = Zend_Registry::isRegistered('translation') ? Zend_Registry::get('translation')->get($code) : $code;
        foreach ($params as $key => $p) {
            $error = str_replace('$' . $key, $p, $error);
        }

        return $error;
    }

    /**
     *
     * Gets a system error
     *
     * @param the code of error int|$code
     *
     * @return String
     */
    static public function getServerError($code)
    {
        return Zend_Registry::isRegistered('translation') ? Zend_Registry::get('translation')->get($code) : $code;
    }
}
