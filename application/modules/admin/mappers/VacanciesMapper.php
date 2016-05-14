<?php

/**
 * VacanciesMapper class
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class VacanciesMapper extends Core_Mapper_Super
{
    /**
     * DbTable Class
     * @var VacanciesTable
     */
    protected $_tableName = 'VacanciesTable';

    protected $_rowClass = 'AdminVacancy';

    protected $_collectionClass = 'VacanciesCollection';

    /**
     *
     * Return a collection of domain objects
     *
     * @return Core_Collection_Super
     */
    public function fetchAll()
    {
        $db        = $this->getAdapter();
        $query     = $db->select()->from(['r' => 'Vacancies'])
            ->joinLeft(['ur' => 'UsersVacancy'], 'r.vacancyId = ur.vacancyId', [])
            ->joinLeft(['u' => 'Users'], 'u.userId = ur.userId', ['userId', 'name'])
            ->order('r.createdAt DESC');
        $resultSet = $db->fetchAll($query);

        /** @var VacanciesCollection $collection */
        $collection = new $this->_collectionClass($resultSet);

        $query     = $db->select()
            ->from(['rt' => 'VacancyTags'], ['GROUP_CONCAT(tagName) AS tags', 'vacancyId'])
            ->joinLeft(['t' => 'Tags'], 'rt.tagId = t.tagId', [])
            ->where('rt.vacancyId IN (?)', $collection->getIds())
            ->group('vacancyId');
        $resultSet = $db->fetchAll($query);
        $collection->setTags($resultSet);

        return $collection;
    }
}
