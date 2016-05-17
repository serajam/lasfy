<?php

/**
 * Class Jooble_Model_Vacancy
 *
 * @author Fedor Petryk
 */
class Jooble_Model_Vacancy
{
    /**
     * External vacancy id
     *
     * @var int
     */
    protected $externalId;

    /**
     * Vacancy short position description
     *
     * @var String
     */
    protected $position;

    /**
     * @var String
     */
    protected $salary;

    /**
     * Country or City
     *
     * @var String
     */
    protected $region;

    /**
     * Full vacancy description
     *
     * @var String
     */
    protected $description;

    /**
     * When was last update
     *
     * @var int
     */
    protected $lastUpdate;

    /**
     * Website source
     *
     * @var String
     */
    protected $source;

    /**
     * Redirect url
     *
     * @var String
     */
    protected $sourceUrl;

    /**
     * @var String
     */
    protected $company;

    /**
     * @return String
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param String $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return String
     */
    public function getSalary()
    {
        return $this->salary;
    }

    /**
     * @param String $salary
     */
    public function setSalary($salary)
    {
        $this->salary = $salary;
    }

    /**
     * @return String
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param String $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
     * @return String
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param String $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * @param int $lastUpdate
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->lastUpdate = $lastUpdate;
    }

    /**
     * @return String
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param String $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @return String
     */
    public function getSourceUrl()
    {
        return $this->sourceUrl;
    }

    /**
     * @param String $sourceUrl
     */
    public function setSourceUrl($sourceUrl)
    {
        $this->sourceUrl = $sourceUrl;
    }

    /**
     * @return String
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param String $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * @param int $externalId
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
    }

    /**
     * @return String
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param String $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }
}
