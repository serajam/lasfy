<?php

class Job_Ads_Jooble_ApiGateway implements Job_Ads_IAdsGateway
{
    use Job_Ads_Pagination;

    /**
     * @var Jooble_ApiConnector
     */
    protected $connector;

    protected $itemsPerPage;

    protected $vacancies;

    public function __construct()
    {
        $this->connector = new Jooble_ApiConnector();
    }

    public function fetchByPeriod($page, $startDate, $endDate, array $tags)
    {
        $this->vacancies = $this->connector->search($tags, $page);
        $pagesCount      = count($this->vacancies) ? $this->vacancies->getPagesCount() : 1;

        return $this->paginationNull($page, $pagesCount, $this->itemsPerPage);
    }

    public function fetchByTags(array $tags, $page)
    {
        $this->vacancies = $this->connector->search($tags, $page);
        $pagesCount      = count($this->vacancies) ? $this->vacancies->getPagesCount() : 1;

        return $this->paginationNull($page, $pagesCount, $this->itemsPerPage);
    }

    /**
     * @return mixed
     */
    public function getVacancies()
    {
        return $this->vacancies;
    }

    public function setItemsPerPage($count)
    {
        $this->itemsPerPage = $count;
    }

    public function getTagsJoinTable()
    {
        return '';
    }

    public function getPrimary()
    {
        return '';
    }
}