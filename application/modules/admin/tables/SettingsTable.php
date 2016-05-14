<?php

/**
 * Created by JetBrains PhpStorm.
 * User: Voronoy
 * Date: 27.08.13
 * Time: 20:42
 * To change this template use File | Settings | File Templates.
 */
class SettingsTable extends Core_DbTable_Base
{
    protected $_name = 'SystemSettings';

    protected $_primary = 'paramId';
}