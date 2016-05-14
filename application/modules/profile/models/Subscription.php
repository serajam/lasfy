<?php

/**
 * Class Subscription
 * Model for UsersSubscriptions
 *
 * @author Fedor Petryk
 */
class Subscription extends UserSubscriptionsTable
{
    /**
     * @var int
     */
    protected $subscriptionId;

    /**
     * @var int
     */
    protected $userId;

    /**
     * Is subscription emailing active
     *
     * @var bool
     */
    protected $active;

    /**
     * Json array of tags ids
     *
     * @var string
     */
    protected $tags;

    /**
     * Do not get renewed ads
     *
     * @var int
     */
    protected $onlyNew;

    /**
     * Period(date now, date from) from which get new vacancies and resume
     *
     * @var string
     */
    protected $period;

    /**
     * 0 - all, 1 - resume, 2 - vacancy
     *
     * @var int
     */
    protected $type;

    /**
     * @var array
     */
    protected $cols = null;

    public function populate($modelArray)
    {
        if ($modelArray instanceof Zend_Db_Table_Row) {
            $modelArray = $modelArray->toArray();
        }
        $this->__construct($modelArray);
        $this->tags = $modelArray['tags'];
    }

    /**
     * Populate record from db
     *
     * @param array $modelArray
     */
    public function __construct(array $modelArray = [])
    {
        if (count($modelArray)) {
            $this->userId         = isset($modelArray['userId']) ? $modelArray['userId'] : false;
            $this->subscriptionId = $modelArray['subscriptionId'];
            $this->active         = (bool)$modelArray['active'];
            $this->onlyNew        = $modelArray['onlyNew'];
            $this->period         = $modelArray['period'];
            $this->type           = $modelArray['type'];

            $this->setTags((array)$modelArray['tags']);
        }
        parent::__construct();
    }

    public function toArray()
    {
        $tags = implode(',', json_decode($this->tags, true));

        return [
            'subscriptionId' => $this->subscriptionId,
            'active'         => $this->active,
            'onlyNew'        => $this->onlyNew,
            'period'         => $this->period,
            'type'           => $this->type,
            'tags'           => $tags
        ];
    }

    public function getDataKeys()
    {
        if (!$this->cols) {
            $this->cols = $this->info('cols');
        }

        return $this->cols;
    }

    public function getFirstProperty()
    {
        if (!$this->cols) {
            $this->cols = $this->info('cols');
        }

        return $this->{$this->cols[0]};
    }

    /**
     * @return int
     */
    public function getSubscriptionId()
    {
        return $this->subscriptionId;
    }

    /**
     * @param int $subscriptionId
     */
    public function setSubscriptionId($subscriptionId)
    {
        $this->subscriptionId = $subscriptionId;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return string
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     */
    public function setTags(array $tags)
    {
        $this->tags = json_encode($tags);
    }

    /**
     * @param $tags
     *
     * @author Fedor Petryk
     */
    public function setJsonEncodedTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return mixed
     */
    public function getOnlyNew()
    {
        return $this->onlyNew;
    }

    /**
     * @param mixed $onlyNew
     */
    public function setOnlyNew($onlyNew)
    {
        $this->onlyNew = $onlyNew;
    }

    /**
     * @return string
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * @param string $period
     */
    public function setPeriod($period)
    {
        $this->period = $period;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = (int)$type;
    }
}