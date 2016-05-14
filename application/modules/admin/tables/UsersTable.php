<?php

/**
 *
 * Users Roles db table
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class UsersTable extends Core_DbTable_Base
{
    /** Table name */
    protected $_name = 'Users';

    protected $_primary = 'userId';
}
