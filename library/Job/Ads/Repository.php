<?php

abstract class Job_Ads_Repository implements IAdsRepository
{
    /**
     * @var String ads type
     */
    protected $type;

    /**
     * @var int
     */
    protected $itemPerPage;

    /**
     * Gateway to data (Zend_Db_Table)
     *
     * @var Job_Ads_IAdsGateway
     */
    protected $gateway;

    /**
     * Collection class name
     *
     * @var String
     */
    protected $collection;

    /**
     * Model class name
     *
     * @var String
     */
    protected $model;

    /**
     * @var Zend_Paginator
     */
    protected $paginator;

    /**
     * Job_Ads_Repository constructor.
     *
     * @param Job_Ads_IAdsGateway $gateway
     */
    public function __construct(Job_Ads_IAdsGateway $gateway)
    {
        $this->gateway = $gateway;

        $config = Zend_Registry::get('appConfig');
        $this->gateway->setItemsPerPage($config['itemsPerPage']);
    }

    /**
     * @param array $tags
     * @param int   $page
     *
     * @return Core_Collection_Super
     *
     * @author Fedor Petryk
     */
    public function fetchByTags(array $tags, $page)
    {
        $this->paginator = $this->gateway->fetchByTags($tags, $page);

        return $this->getCollection($this->paginator->getCurrentItems());
    }

    /**
     * @param Traversable $array
     *
     * @return Core_Collection_Super
     *
     * @author Fedor Petryk
     */
    public function getCollection($array)
    {
        /** @var Core_Collection_Super $adsCollection */
        $adsCollection = new $this->collection;
        foreach ($array as $item) {
            $adsCollection->addByKey($item);
        }

        return $adsCollection;
    }

    /**
     * @param int    $page
     * @param String $starDate
     * @param String $endDate
     * @param array $tags
     *
     * @return Core_Collection_Super
     *
     * @author Fedor Petryk
     */
    public function fetchByPeriod($page, $starDate, $endDate, array $tags)
    {
        $this->paginator = $this->gateway->fetchByPeriod($page, $starDate, $endDate, $tags);

        return $this->getCollection($this->paginator->getCurrentItems());
    }

    /**
     * @return Zend_Paginator
     */
    public function getPaginator()
    {
        return $this->paginator;
    }

    /**
     * @return String
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return String
     */
    public function getTagsJoinTable()
    {
        return $this->gateway->getTagsJoinTable();
    }

    public function getPrimary()
    {
        return $this->gateway->getPrimary();
    }
}