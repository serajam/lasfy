<?php

/**
 * Class VacanciesService
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class VacanciesService extends Core_Service_Editor
{
    /**
     * Role mapper class
     * @var String
     */
    protected $_mapperName = 'VacanciesMapper';

    /**
     * @var VacanciesMapper
     */
    protected $_mapper;

    /**
     * @var String
     */
    protected $_validatorName = 'VacancyEditForm';

    public function getCollection($order = false, $dir = false)
    {
        $rows = $this->_mapper->fetchAll();

        return $rows;
    }
}