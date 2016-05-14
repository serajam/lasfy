<?php

/**
 * ResumeMapper class
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class ResumeMapper extends Core_Mapper_Super
{
    /**
     * DbTable Class
     * @var ResumeTable
     */
    protected $_tableName = 'ResumeTable';

    protected $_rowClass = 'AdminResume';

    protected $_collectionClass = 'ResumeCollection';

    /**
     *
     * Return a collection of domain objects
     *
     * @return Core_Collection_Super
     */
    public function fetchAll()
    {
        $db        = $this->getAdapter();
        $query     = $db->select()->from(['r' => 'Resumes'])
            ->joinLeft(['ur' => 'UsersResume'], 'r.resumeId = ur.resumeId', [])
            ->joinLeft(['u' => 'Users'], 'u.userId = ur.userId', ['userId', 'name'])
            ->order('r.createdAt DESC');
        $resultSet = $db->fetchAll($query);

        /** @var ResumeCollection $collection */
        $collection = new $this->_collectionClass($resultSet);

        $query     = $db->select()
            ->from(['rt' => 'ResumeTags'], ['GROUP_CONCAT(tagName) AS tags', 'resumeId'])
            ->joinLeft(['t' => 'Tags'], 'rt.tagId = t.tagId', [])
            ->where('rt.resumeId IN (?)', $collection->getIds())
            ->group('resumeId');
        $resultSet = $db->fetchAll($query);
        $collection->setTags($resultSet);

        return $collection;
    }
}
