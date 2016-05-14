<?php

/**
 * Class Conversation
 */
class Conversation
{
    protected $conversationId;

    protected $replierId;

    protected $name;

    protected $type;

    protected $newMessages;

    protected $lastMessageDate;

    protected $lastMessageAuthor;

    protected $lastMessage;

    /**
     * @param $type
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    public function getFirstProperty()
    {
        return $this->conversationId;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getNewMessages()
    {
        return $this->newMessages;
    }

    /**
     * @param mixed $newMessages
     */
    public function setNewMessages($newMessages)
    {
        $this->newMessages = $newMessages;
    }

    /**
     * @return mixed
     */
    public function getLastMessageDate()
    {
        return $this->lastMessageDate;
    }

    /**
     * @param mixed $lastMessageDate
     */
    public function setLastMessageDate($lastMessageDate)
    {
        $this->lastMessageDate = $lastMessageDate;
    }

    /**
     * @return mixed
     */
    public function getLastMessageAuthor()
    {
        return $this->lastMessageAuthor;
    }

    /**
     * @param mixed $lastMessageAuthor
     */
    public function setLastMessageAuthor($lastMessageAuthor)
    {
        $this->lastMessageAuthor = $lastMessageAuthor;
    }

    /**
     * @return mixed
     */
    public function getLastMessage()
    {
        return $this->lastMessage;
    }

    /**
     * @param mixed $lastMessage
     */
    public function setLastMessage($lastMessage)
    {
        $this->lastMessage = $lastMessage;
    }

    /**
     * @return mixed
     */
    public function getConversationId()
    {
        return $this->conversationId;
    }

    /**
     * @param mixed $conversationId
     */
    public function setConversationId($conversationId)
    {
        $this->conversationId = $conversationId;
    }

    /**
     * @return mixed
     */
    public function getReplierId()
    {
        return $this->replierId;
    }

    /**
     * @param mixed $replierId
     */
    public function setReplierId($replierId)
    {
        $this->replierId = $replierId;
    }
}