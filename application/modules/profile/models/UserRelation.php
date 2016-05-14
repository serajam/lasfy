<?php

/**
 * UserRelation class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 *
 * @property int $userId
 * @property int $friendId
 * @property int $status
 */
class UserRelation extends Core_Model_Super
{
    protected $_data = [
        'userId'   => null,
        'friendId' => null,
        'status'   => null,
    ];

    public function changeRelationsSides()
    {
        $temp           = $this->userId;
        $this->userId   = $this->friendId;
        $this->friendId = $temp;
    }
}