<?php

/**
 * Class UserSubscriptionsTable
 *
 * @author Fedor Petryk
 */
class UserSubscriptionsTable extends Core_DbTable_Base
{
    /** Table name */
    protected $_name = 'UserSubscriptions';

    protected $_primary = 'subscriptionId';
}