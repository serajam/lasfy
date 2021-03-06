<?php

/**
 * VacanciesCollection
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class VacanciesCollection extends Core_Collection_Super
{
    protected $_domainObjectClass = 'AdminVacancy';

    public function populate($results)
    {
        foreach ($results as $data) {
            /** @var AdminVacancy $model */
            $model = new $this->_domainObjectClass($data);
            $model->populate($data);
            $model->setOwnerId($data['userId']);
            $model->setOwnerName($data['name']);
            $this->_resultSet[$model->getFirstProperty()] = $model;
        }
    }

    public function setTags($tags)
    {
        foreach ($tags as $tagsData) {
            if (isset($this->_resultSet[$tagsData['vacancyId']])) {
                $this->_resultSet[$tagsData['vacancyId']]->setTags($tagsData['tags']);
            }
        }
    }

    public function getIds($key = null)
    {
        return array_keys($this->_resultSet);
    }
}
