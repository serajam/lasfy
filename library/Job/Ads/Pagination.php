<?php

/**
 * Class Job_Ads_Pagination
 *
 * @author Fedor Petryk
 */
trait Job_Ads_Pagination
{
    /**
     * @param Zend_Db_Select $select
     * @param int            $page
     * @param int            $itemsPerPage
     *
     * @return Zend_Paginator
     * @throws Zend_Db_Select_Exception
     * @throws Zend_Paginator_Exception
     *
     * @author Fedor Petryk
     */
    private function paginationDbSelect(Zend_Db_Select $select, $page, $itemsPerPage)
    {
        $adapter = new Zend_Paginator_Adapter_DbSelect($select);

        $selectCount = clone $select;
        $selectCount->reset(Zend_Db_Select::COLUMNS);
        $selectCount->reset(Zend_Db_Select::GROUP);
        $selectCount->columns([Zend_Paginator_Adapter_DbSelect::ROW_COUNT_COLUMN => 'count(*)']);

        $adapter->setRowCount($selectCount);
        $paginator = new Zend_Paginator($adapter);

        // set page and page count per page
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($itemsPerPage);

        return $paginator;
    }

    /**
     * @param int $page
     * @param int $pagesCount
     * @param int $itemsPerPage
     *
     * @return Zend_Paginator
     *
     * @author Fedor Petryk
     */
    private function paginationNull($page, $pagesCount, $itemsPerPage)
    {
        $adapter   = new Zend_Paginator_Adapter_Null($itemsPerPage * $pagesCount);
        $paginator = new Zend_Paginator($adapter);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($itemsPerPage);

        return $paginator;
    }
}