<?php

/**
 * Class Job_Ads_TableGateway
 *
 * @author Fedor Petryk
 */
class Job_Ads_TableGateway extends Core_DbTable_Base implements Job_Ads_IAdsGateway
{
    /**
     * table alias for ads
     *
     * @var String
     */
    protected $alias;

    /**
     * tags join table alias
     *
     * @var String
     */
    protected $aliasTagsJoin;

    /**
     * tags join table name
     *
     * @var String
     */
    protected $tagsJoinTable;

    use Job_Ads_Pagination;

    /**
     * @param int    $page
     * @param string $startDate
     * @param string $endDate
     * @param array $tags
     *
     * @return Zend_Paginator
     *
     * @author Fedor Petryk
     */
    public function fetchByPeriod($page, $startDate, $endDate, array $tags)
    {
        $query = $this->buildQuery();
        $this->filterByPeriod($query, $startDate, $endDate);

        return $this->paginationDbSelect($query, $page, $this->_itemsPerPage);
    }

    /**
     * @return Zend_Db_Select
     *
     * @author Fedor Petryk
     */
    protected function buildQuery()
    {
        $db = $this->getAdapter();

        return $db->select()
            ->from([$this->alias => $this->_name])
            ->where($this->alias . '.isBanned = 0')
            ->where($this->alias . '.isPublished = 1')
            ->group($this->alias . '.' . $this->_primary)
            ->order($this->alias . '.' . $this->_primary . ' DESC');
    }

    /**
     * @param Zend_Db_Select $query
     * @param bool           $startDate
     * @param bool           $endDate
     *
     * @return $this
     *
     * @author Fedor Petryk
     */
    protected function filterByPeriod(Zend_Db_Select $query, $startDate = false, $endDate = false)
    {
        !$startDate ?: $query->where($this->alias . '.createdAt > ?', $startDate);
        !$endDate ?: $query->where($this->alias . '.createdAt < ?', $endDate);

        return $this;
    }

    /**
     * @param array $tagsIds
     * @param int   $page
     *
     * @return Zend_Paginator
     *
     * @author Fedor Petryk
     */
    public function fetchByTags(array $tagsIds, $page)
    {
        $query = $this->buildQuery();
        $this->filterByTags($query, $tagsIds);

        return $this->paginationDbSelect($query, $page, $this->_itemsPerPage);
    }

    /**
     * @param Zend_Db_Select $query
     * @param array          $tags
     *
     * @return $this
     *
     * @author Fedor Petryk
     */
    protected function filterByTags(Zend_Db_Select $query, array $tags)
    {
        $query->joinInner(
            [$this->aliasTagsJoin => $this->tagsJoinTable],
            $this->alias . '.' . $this->_primary . ' = ' . $this->aliasTagsJoin . '.' . $this->_primary,
            []
        )
            ->where($this->aliasTagsJoin . '.tagId IN (?)', array_keys($tags))
            ->having('COUNT(' . $this->aliasTagsJoin . '.tagId) = ?', count($tags));

        return $this;
    }

    /**
     * @param int $itemsPerPage
     */
    public function setItemsPerPage($itemsPerPage)
    {
        $this->_itemsPerPage = $itemsPerPage;
    }

    /**
     * @return String
     */
    public function getTagsJoinTable()
    {
        return $this->tagsJoinTable;
    }
}