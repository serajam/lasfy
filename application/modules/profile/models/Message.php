<?php

/**
 * Message class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 *
 * @property int    $messageId
 * @property int    $userFrom
 * @property int    $userTo
 * @property int    $userId
 * @property int    $userCode
 * @property string $sendDate
 * @property string $message
 * @property string $new
 * @property string $avatar
 * @property string $firstName
 * @property string $lastName
 */
class Message extends Core_Model_Super
{
    protected $_data = [
        'messageId' => null,
        'userFrom'  => null,
        'userTo'    => null,
        'sendDate'  => null,
        'message'   => null,
        'new'       => 1,
        'addId'     => null,
        'addType'   => null,
    ];

    protected $senderName;

    public function __construct($data = null)
    {
        parent::populate($data);
        $this->sendDate = date('Y-m-d H:i:s');
    }

    /**
     * @return mixed
     */
    public function getSenderName()
    {
        return $this->senderName;
    }

    /**
     * @param mixed $senderName
     */
    public function setSenderName($senderName)
    {
        $this->senderName = $senderName;
    }
}