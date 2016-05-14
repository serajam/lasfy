<?php

/**
 *
 * Approvals db table
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class UsersActivationCodesTable extends Core_DbTable_Base
{
    /** Table name */
    protected $_name = 'UsersActivationCodes';

    protected $_primary = 'codeId';
}
