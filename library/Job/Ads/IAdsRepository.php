<?php

interface IAdsRepository
{
    public function fetchByTags(array $tags, $page);

    public function fetchByPeriod($page, $starDate, $endDate);

    public function getCollection($data);

    public function getPaginator();

    /**
     * Get ad type
     *
     * @return String
     *
     * @author Fedor Petryk
     */
    public function getType();
}