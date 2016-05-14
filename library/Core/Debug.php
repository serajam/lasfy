<?php

/**
 * Class
 *
 * @author     : Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_Debug
{
    public static function printr($value, $exit = false)
    {
        if (empty($value) || $value == null || $value == '') {
            var_dump($value);
        } else {
            echo '<pre style="font-size: 12px !important;">';
            print_r($value);
            echo '</pre>';
        }

        if ($exit) {
            exit('Debug');
        }
    }
}