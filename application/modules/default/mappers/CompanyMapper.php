<?php

/**
 *
 * Company mapper class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class CompanyMapper extends Core_Mapper_Front
{
    /**
     *
     * Users DbTable class
     *
     * @var Core_DbTable_Company
     */
    protected $_tableName = 'Core_DbTable_Company';

    /**
     *
     * Users model
     *
     * @var Core_Mapper_Super
     */
    protected $_rowClass = 'Company';

    public function getCompaniesList($page)
    {
        $db     = $this->getDbTable()->getAdapter();
        $select = $db->select()
            ->from(['u' => 'Users'], [])
            ->where('u.isBanned = 0')
            ->where('u.isActivated = 1')
            ->where('u.agreement = 1')
            ->joinInner(['c' => 'Companies'], 'c.userId = u.userId AND c.isActiveByUser = 1')
            ->group('c.companyId')
            ->order('c.companyId DESC');

        if ($page) {
            $adapter     = new Zend_Paginator_Adapter_DbSelect($select);
            $selectCount = $db->select()
                ->from(
                    ['u' => 'Users'],
                    [Zend_Paginator_Adapter_DbSelect::ROW_COUNT_COLUMN => 'count(*)']
                )
                ->where('u.isBanned = 0')
                ->where('u.isActivated = 1')
                ->where('u.agreement = 1')
                ->joinInner(['c' => 'Companies'], 'c.userId = u.userId AND c.isActiveByUser = 1');

            $adapter->setRowCount($selectCount);
            $paginator = new Zend_Paginator($adapter);
            $config    = Zend_Registry::get('appConfig');
            $paginator->setCurrentPageNumber($page);
            $paginator->setItemCountPerPage($config['itemsPerPage']);
            $collection = new CompanyCollection();
            $collection->populate($paginator->getCurrentItems());
            $collection->setPaginator($paginator);

            return $collection;
        }

        $res = $db->fetchAll($select);

        if (!empty($res)) {
            return new CompanyCollection($res);
        }

        return false;
    }

    public function getCompanyById($companyId)
    {
        $db     = $this->getDbTable()->getAdapter();
        $select = $db->select()
            ->from(['c' => 'Companies'])
            ->where('c.companyId = ?', $companyId);

        $res = $db->fetchRow($select);

        if ($res) {
            $company = new Company();
            $company->populate($res);

            return $company;
        }

        return false;
    }

    public function getCompaniesByName($searchRequest, $page)
    {
        $db     = $this->getDbTable()->getAdapter();
        $select = $db->select()
            ->from(['u' => 'Users'], [])
            ->where('u.isBanned = 0')
            ->where('u.isActivated = 1')
            ->where('u.agreement = 1')
            ->joinInner(
                ['c' => 'Companies'],
                "c.userId = u.userId AND c.isActiveByUser = 1 AND name LIKE '%{$searchRequest}%'"
            )
            ->group('c.companyId')
            ->order('c.companyId DESC');

        if ($page) {
            $adapter     = new Zend_Paginator_Adapter_DbSelect($select);
            $selectCount = $db->select()
                ->from(
                    ['u' => 'Users'],
                    [Zend_Paginator_Adapter_DbSelect::ROW_COUNT_COLUMN => 'count(*)']
                )
                ->where('u.isBanned = 0')
                ->where('u.isActivated = 1')
                ->where('u.agreement = 1')
                ->joinInner(
                    ['c' => 'Companies'],
                    "c.userId = u.userId AND c.isActiveByUser = 1 AND name LIKE '%{$searchRequest}%'"
                );

            $adapter->setRowCount($selectCount);
            $paginator = new Zend_Paginator($adapter);
            $config    = Zend_Registry::get('appConfig');
            $paginator->setCurrentPageNumber($page);
            $paginator->setItemCountPerPage($config['itemsPerPage']);
            $collection = new CompanyCollection();
            $collection->populate($paginator->getCurrentItems());
            $collection->setPaginator($paginator);

            return $collection;
        }

        $res = $db->fetchAll($select);

        if (!empty($res)) {
            return new CompanyCollection($res);
        }

        return false;
    }
}