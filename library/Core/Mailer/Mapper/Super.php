<?php

/**
 * Mapper Super Type
 * Base functionality
 *
 * @author     Alexey Kagarlykskiy
 * @package    Core_Mailer_Mapper
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_Mailer_Mapper_Super extends Core_Mapper_Super
{
    /**
     * Database table object
     *
     * @var Core_DbTable_Base
     */
    protected $_dbTable;

    /**
     * The name of the table class
     *
     * @var string
     */
    protected $_tableName = "Core_DbTable_Base";

    /**
     * Model name class
     *
     * @var String
     */
    protected $_rowClass;

    /**
     * Model name
     *
     * @var Core_Model_Super
     */
    protected $_domainObject;

    /**
     * Collection object
     *
     * @var Core_Collection_Super
     */
    protected $_collectionClass = 'Core_Collection_Super';

    /**
     * Collection object
     *
     * @var Core_Collection_Super
     */
    protected $_domainObjectCollection;
}