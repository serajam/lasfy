<?php

/**
 * Job_Tags_TagMapper class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Job_Tags_TagsMapper extends Core_Mapper_Super
{
    const TAG_TYPE_CITY = 'city';
    const TAG_TYPE_COUNTRY = 'country';
    const TAG_TYPE_JOB = 'job';
    const TAG_TYPE_COMPANY = 'company';
    const TAG_TYPE_TECHNOLOGY = 'technology';
    const JOIN_RESUME = 'Resume';
    const JOIN_VACANCY = 'Vacancy';

    /**
     * @var Job_Tags_TagsTable
     */
    protected $_tableName = 'Job_Tags_TagsTable';

    protected $_rowClass = 'Job_Tags_TagModel';

    protected $_collectionClass = 'Job_Tags_TagsCollection';

    /**
     * Get tags like %$tag%
     *
     * @param $tag string
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getTags($tag)
    {
        $db     = $this->getAdapter();
        $select = $db->select()
            ->from(['t' => 'Tags'], ['t.tagId', 't.tagName'])
            ->where('t.tagName LIKE ' . $db->quote('%' . $tag . '%'));
        $res    = $db->fetchPairs($select);

        return $res;
    }

    public function getTagsByFilter($tagsNamesArray = [], $page = 1)
    {
        $db  = $this->getDbTable();
        $sql = $db->select()
            ->from(['t' => 'Tags'], ['t.tagId', 'tagName']);
        foreach ($tagsNamesArray as $tag) {
            if (!$tag) {
                continue;
            }
            $sql->orWhere('t.tagName LIKE ' . $db->getAdapter()->quote('%' . $tag . '%'));
        }
        $sql->order('tagId DESC');

        if ($page) {
            $adapter     = new Zend_Paginator_Adapter_DbTableSelect($sql);
            $selectCount = $this->getDbTable()->select()
                ->from(
                    'Tags',
                    [
                        Zend_Paginator_Adapter_DbSelect::ROW_COUNT_COLUMN => 'count(*)'
                    ]
                );

            foreach ($tagsNamesArray as $tag) {
                if (!$tag) {
                    continue;
                }
                $selectCount->orWhere('tagName LIKE ' . $db->getAdapter()->quote('%' . $tag . '%'));
            }

            $adapter->setRowCount($selectCount);
            $paginator  = new Zend_Paginator($adapter);
            $config     = Zend_Registry::get('appConfig');
            $collection = new $this->_collectionClass;
            $paginator->setCurrentPageNumber($page);
            $paginator->setItemCountPerPage(50);
            $collection->populate($paginator->getCurrentItems());
            $collection->setPaginator($paginator);

            return $collection;
        }

        $result     = $db->fetchAll($sql);
        $collection = new $this->_collectionClass($result);

        return $collection;
    }

    public function getExistedTagsByName($tagsNamesArray, $withName = true)
    {
        $db     = $this->getAdapter();
        $sql    = $db->select()
            ->from(['t' => 'Tags'], ['t.tagId', 'tagName'])
            ->where('t.enable = 1')
            ->where('t.tagName IN ("' . implode('","', $tagsNamesArray) . '")');
        $result = $db->fetchPairs($sql);
        if (!empty($result)) {
            return $result;
        }

        return false;
    }

    public function saveTags($tagsArray)
    {
        $db = $this->getAdapter();
        $db->beginTransaction();
        foreach ($tagsArray as $key => $name) {
            $info = [
                'tagName' => $name,
            ];
            $res  = $db->insert('Tags', $info);
        }
        try {
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();

            return false;
        }

        return $res;
    }

    public function setDependecy($model, $tags, $tablePrefix)
    {
        $db = $this->getAdapter();
        $db->beginTransaction();
        foreach ($tags as $tagId => $tagName) {
            $info = [
                'tagId'                         => $tagId,
                strtolower($tablePrefix) . 'Id' => (int)$model->getFirstProperty()
            ];
            $res  = $db->insert($tablePrefix . 'Tags', $info);
        }
        try {
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();

            return false;
        }

        return $res;
    }

    public function deleteAdTags($modelId, $tablePrefix)
    {
        $db  = $this->getAdapter();
        $res = $db->delete($tablePrefix . 'Tags', [strtolower($tablePrefix) . 'Id = ?' => (int)$modelId]);

        return $res;
    }

    public function getTagsByModelId($modelId, $tablePrefix)
    {
        $db  = $this->getAdapter();
        $sql = $db->select()
            ->from(['mt' => $tablePrefix . 'Tags'], [])
            ->joinLeft(['t' => 'Tags'], 't.tagId = mt.tagId', ['t.tagId', 't.tagName'])
            ->where('mt.' . strtolower($tablePrefix) . 'Id = ?', $modelId);
        $res = $db->fetchPairs($sql);

        return $res;
    }

    public function getTagsByJoinedAds($ids, $joinedTable, $joinedColumn)
    {
        $joinedTableId = 'mt.' . $joinedColumn;
        $db            = $this->getAdapter();
        $sql           = $db->select()
            ->from(['mt' => $joinedTable], [$joinedTableId])
            ->joinLeft(['t' => 'Tags'], 't.tagId = mt.tagId', ['t.tagId', 't.tagName'])
            ->group([$joinedTableId, 't.tagId'])
            ->where($joinedTableId . ' IN (?)', $ids);

        return $db->fetchAll($sql);
    }
}