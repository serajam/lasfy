<?php

/**
 * Created by PhpStorm.
 * User: so
 * Date: 2/27/14
 * Time: 7:30 PM
 */
class CustomContentMapper extends Core_Mapper_Super
{
    protected $_tableName = "CustomContentTable";

    protected $_rowClass = 'Core_Model_CustomContent';

    protected $_collectionClass = 'Core_Collection_CustomContent';
}