<?php

/**
 * Date formatter
 *
 * @author     Petryk Fedor
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_View_Helper_DateFormatter
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
    public function dateFormatter($date, $time = false)
    {
        $validator = new Zend_Validate_Date();
        if ($time == true) {
            $validator->setFormat('yyyy-MM-dd H:m:s');
        }

        if (!$validator->isValid($date)) {
            return $date;
        }
        if (empty($date) || null == $date || $date == '0000-00-00 00:00:00'
            || $date == '0000-00-00'
        ) {
            return false;
        }

        $format = $time ? "d.m.Y H:i:s" : "d.m.Y";

        return date($format, strtotime($date));
    }
}