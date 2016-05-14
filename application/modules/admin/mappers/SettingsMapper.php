<?php

/**
 * Created by JetBrains PhpStorm.
 * User: Voronoy
 * Date: 27.08.13
 * Time: 20:31
 * To change this template use File | Settings | File Templates.
 */
class SettingsMapper extends Core_Mapper_Super
{
    protected $_tableName = "SettingsTable";

    protected $_collectionClass = 'SettingsCollection';

    protected $_rowClass = 'Settings';
}
