<?php

/**
 * Type formatter
 *
 * @author     Petryk Fedor
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_View_Helper_Type
{
    /**
     *
     * Returns formmated date
     *
     * @param String  $date
     * @param boolean $time | with or without time string
     *
     * @return String
     */
    public function type($type, $typeCode, $int = true)
    {

        if ($int) {
            $type = (int)$type;
        }
        $types = Core_Model_Settings::types($typeCode);
        if (array_key_exists($type, $types)) {
            $trans = Zend_Registry::get('translation');

            return $trans->get($types[$type]);
        }
    }
}