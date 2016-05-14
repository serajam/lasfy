<?php

interface Job_Ads_IAdsGateway
{
    /**
     * @param array $tags
     * @param int   $page
     *
     * @return Zend_Paginator
     *
     * @author Fedor Petryk
     */
    public function fetchByTags(array $tags, $page);

    public function fetchByPeriod($page, $startDate, $endDate);

    public function setItemsPerPage($count);
}