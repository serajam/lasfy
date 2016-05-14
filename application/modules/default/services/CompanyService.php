<?php

/**
 *
 * Company class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class CompanyService extends Core_Service_Front
{
    /**
     * Users mapper object
     *
     * @var UsersMapper
     */
    protected $_mapperName = 'CompanyMapper';

    /**
     * @var CompanyMapper
     */
    protected $_mapper;

    public function getCompaniesList($page)
    {
        return $this->_mapper->getCompaniesList($page);
    }

    public function getCompanyById($companyId)
    {
        return $this->_mapper->getCompanyById($companyId);
    }

    public function getSearchCompanyForm()
    {
        if ($this->getFormLoader()->isExists('SearchCompanyForm')) {
            return $this->getFormLoader()->getForm('SearchCompanyForm');
        }

        $searchForm = $this->getFormLoader()->addForm('SearchCompanyForm');

        return $searchForm;
    }

    public function getCompaniesByName($searchRequest, $page)
    {
        return $this->_mapper->getCompaniesByName($searchRequest, $page);
    }
}