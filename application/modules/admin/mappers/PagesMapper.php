<?php

/**
 *
 * RolesMapper class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class PagesMapper extends Core_Mapper_Super
{
    /**
     *
     * DbTable Class
     *
     * @var PagesTable
     */
    protected $_tableName = 'PagesTable';

    /**
     *
     * Roles row class name
     *
     * @var Role
     */
    protected $_rowClass = 'Pages_Page';

    /**
     * @var string
     */
    protected $_collectionClass = 'PagesCollection';

    /**
     * @param $menuId
     * @param $pages
     *
     * @return bool|string
     *
     * @author Fedor Petryk
     */
    public function addMenuPages($menuId, $pages)
    {
        $db = $this->getAdapter();
        $db->beginTransaction();

        try {
            $db->delete('MenuPages', ['menuId = ?' => $menuId]);
            foreach ($pages as $key => $p) {
                $db->insert('MenuPages', ['menuId' => $menuId, 'pageId' => $p, 'position' => $key]);
            }
            $db->commit();
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    /**
     * @param            $id
     * @param bool|false $lang
     *
     * @return bool|mixed
     *
     * @author Fedor Petryk
     */
    public function getMenuItem($id, $lang = false)
    {
        $db  = $this->getAdapter();
        $sql = $db->select()
            ->from(['sm' => 'SiteMenu'])
            ->joinLeft(['hp' => 'Pages'], 'hp.pageId = sm.contentId', ['hp.slug', 'hp.title AS contentTitle'])
            ->joinLeft(['mp' => 'MenuPages'], 'mp.menuId = sm.menuId', ['mp.pageId'])
            ->where('sm.menuId = ?', $id)
            ->order('mp.position ASC');
        if ($lang) {
            $sql->where('sm.lang = ?', $lang);
        }

        $result = $db->fetchAll($sql);
        if (empty($result)) {
            return false;
        }

        $menu           = array_pop($result);
        $menu['pageId'] = [$menu['pageId']];
        foreach ($result as $r) {
            $menu['pageId'][] = $r['pageId'];
        }

        return $menu;
    }

    /**
     * @param $lang
     *
     * @return bool|Core_Collection_Menu
     *
     * @author Fedor Petryk
     */
    public function getMenu($lang)
    {
        $db     = $this->getAdapter();
        $sql    = $db->select()
            ->from(['sm' => 'SiteMenu'])
            ->joinLeft(['hp' => 'Pages'], 'hp.pageId = sm.contentId', ['hp.slug', 'hp.title AS contentTitle'])
            ->where('sm.lang = ?', $lang)
            ->order('sm.position ASC')
            ->group('sm.menuId');
        $result = $db->fetchAll($sql);
        if (empty($result)) {
            return false;
        }

        return new Core_Collection_Menu($result);
    }

    /**
     * @return array
     *
     * @author Fedor Petryk
     */
    public function getMenuIdNamePair()
    {
        $db     = $this->getAdapter();
        $sql    = $db->select()
            ->from(['sm' => 'SiteMenu'], ['menuId', 'name'])
            ->order('sm.position ASC')
            ->order('sm.parentId ASC')
            ->group('sm.menuId');
        $result = $db->fetchPairs($sql);
        if (empty($result)) {
            return false;
        }

        return $result;
    }

    /**
     * @param $position
     * @param $lang
     *
     * @author Fedor Petryk
     */
    public function reOrderMenu($position, $lang)
    {
        $db = $this->getAdapter();
        $db->update(
            'SiteMenu',
            ['position' => new Zend_Db_Expr('position + 1')],
            ['position >= ?' => $position, 'lang = ?' => $lang]
        );
    }

    /**
     * @param $lang
     *
     * @return mixed
     *
     * @author Fedor Petryk
     */
    public function getContentList($lang)
    {
        $db    = $this->getAdapter();
        $sql   = $db->select()
            ->from(['hp' => 'Pages'], ['hp.pageId', 'hp.title'])
            ->where('hp.lang = ?', $lang);
        $pairs = $db->fetchPairs($sql);

        return $pairs;
    }

    /**
     * @param  $page  int
     * @param  $data  array
     *
     * @return mixed | Core_Collection_Super
     */
    public function fetchAllWhereWithPage($data, $page = 1)
    {
        $select = $this->getDbTable()->select()->from(['hp' => 'Pages']);
        if (array_key_exists('lang', $data) && $data['lang'] > 0) {
            $select->where('hp.lang = ?', $data['lang']);
        }
        if (array_key_exists('pageType', $data) && $data['pageType'] > 0) {
            $select->where('hp.pageType = ?', $data['pageType']);
        }

        if ($page) {
            $adapter     = new Zend_Paginator_Adapter_DbTableSelect($select);
            $selectCount = $this->getDbTable()->select()
                ->from(
                    'Pages',
                    [
                        Zend_Paginator_Adapter_DbSelect::ROW_COUNT_COLUMN => 'count(*)'
                    ]
                );
            if (array_key_exists('lang', $data) && $data['lang'] > 0) {
                $selectCount->where('lang = ?', $data['lang']);
            }
            if (array_key_exists('pageType', $data) && $data['pageType'] > 0) {
                $selectCount->where('pageType = ?', $data['pageType']);
            }
            $adapter->setRowCount($selectCount);
            $paginator  = new Zend_Paginator($adapter);
            $config     = Zend_Registry::get('appConfig');
            $collection = new $this->_collectionClass;
            $paginator->setCurrentPageNumber($page);
            $paginator->setItemCountPerPage($config['itemsPerPage']);
            $collection->populate($paginator->getCurrentItems());
            $collection->setPaginator($paginator);

            return $collection;
        }
        $resultSet = $this->getDbTable()->fetchAll($select);
        $entries   = [];
        foreach ($resultSet as $row) {
            $entry = new $this->_rowClass;
            $entry->populate($row);
            $entries[] = $entry;
        }
        $collection = new $this->_collectionClass($entries);

        return $collection;
    }

    /**
     * @param bool|false $pageSlug
     * @param bool|false $pageId
     * @param bool|false $lang
     *
     * @return bool|Page
     * @throws Exception
     *
     * @author Fedor Petryk
     */
    public function getPage($pageSlug = false, $pageId = false, $lang = false)
    {
        $db     = $this->getAdapter();
        $select = $db->select()
            ->from(['p' => 'Pages']);

        if (!empty($pageSlug)) {
            $select->where('p.slug = ?', $pageSlug);
        }
        if (!empty($pageId)) {
            $select->where('p.pageId = ?', $pageId);
        }
        if (!empty($lang)) {
            $select->where('p.lang = ?', $lang);
        }

        $res = $db->fetchRow($select);

        if (!empty($res)) {
            $page = new Page();
            $page->populate($res);

            return $page;
        }

        return false;
    }
}
