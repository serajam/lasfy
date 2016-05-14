<?php

/**
 * Created by JetBrains PhpStorm.
 * User: serajam
 * Date: 6/13/13
 * Time: 9:18 PM
 * To change this template use File | Settings | File Templates.
 */
class Core_ApplicationLoad
{
    protected static $_startTime;

    public static function start()
    {
        self::$_startTime = microtime(true);;
    }

    public static function stop()
    {
        $finish     = microtime(true);
        $total_time = ($finish - self::$_startTime) / 1000;
        $db         = Zend_Registry::get('DB');
        $profiler   = $db->getProfiler();
        $profile    = '<hr>';
        $totalTime  = 0;

        if ($profiler->getQueryProfiles()) {
            foreach ($profiler->getQueryProfiles() as $query) {

                $profile .= $query->getQuery() . "\n"
                    . 'Time: ' . $query->getElapsedSecs() . '<hr><br>';
                $totalTime += $query->getElapsedSecs();
            }
        }

        echo '<br> Total page time in secs: ' . $total_time * 1000;
        echo '<br> Total db time secs: ' . $totalTime;
        echo '<br> Queries profile: ' . $profile;
    }
}