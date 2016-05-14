<?php

/**
 * Job_Tags_TagsTable
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Job_Tags_TagsTable extends Core_DbTable_Base
{
    /** Table name */
    protected $_name = 'Tags';

    protected $_primary = 'tagId';
}
