<?php

/**
 * Class ResumeService
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class ResumeService extends Core_Service_Editor
{
    /**
     * Role mapper class
     * @var String
     */
    protected $_mapperName = 'ResumeMapper';

    /**
     * @var ResumeMapper
     */
    protected $_mapper;

    /**
     * @var String
     */
    protected $_validatorName = 'ResumeEditForm';

    public function getCollection($order = false, $dir = false)
    {
        $rows = $this->_mapper->fetchAll();

        return $rows;
    }
}