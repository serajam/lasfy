<?php

/**
 *
 * Email mapper class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class EmailMapper extends Core_Mapper_Super
{
    protected $_tableName = "Core_DbTable_Mail";

    /**
     * Model name class
     *
     * @var String
     */
    protected $_rowClass = 'Core_Mailer_EmailTemplate';

    /**
     * Model name
     *
     * @var Core_Model_Super
     */
    protected $_domainObject = 'Core_Mailer_EmailTemplate';

    /**
     * Collection object
     *
     * @var Core_Collection_Super
     */
    protected $_collectionClass = 'Core_Mailer_Collection_EmailTemplates';
}