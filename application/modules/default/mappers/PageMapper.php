<?php

/**
 *
 * The Page service class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class PageMapper extends Core_Mapper_Front
{
    /**
     *
     * Users DbTable class
     *
     * @var DbTable_Users
     */
    protected $_tableName = 'Core_DbTable_Pages';

    /**
     *
     * Users model
     *
     * @var Core_Mapper_Super
     */
    protected $_rowClass = 'Page';

    public function getGalleries()
    {
        $db   = $this->getAdapter();
        $sql  = $db->select()
            ->from(['g' => 'Galleries'])
            ->joinInner(['gi' => 'GalleriesImages'], 'gi.galleryId = g.galleryId')
            ->joinInner(
                ['i' => 'Images'], 'i.imageId = gi.imageId AND (i.imageSizeType = g.thumbnailSize OR i.imageSizeType = g.fullSize)'
            )
            ->order('gi.position ASC')
            ->group('g.galleryId');
        $rows = $db->fetchAll($sql);
        if (empty($rows)) {
            return false;
        }
        $collection = new Core_Image_Collection_Galleries($rows);

        return $collection;
    }

    public function getPage($pageSlug = false, $pageId = false)
    {
        $db     = $this->getAdapter();
        $select = $db->select()
            ->from(['p' => 'Pages']);

        if (!empty($pageSlug)) {
            $select->where('p.slug = ?', $pageSlug);
        } elseif (!empty($pageId)) {
            $select->where('p.pageId = ?', $pageId);
        } else {
            $select->where('p.isDefaultPage = ?', 1);
        }

        $res = $db->fetchRow($select);

        if (!empty($res)) {
            $page = new Page();
            $page->populate($res);

            return $page;
        } else {
            $select = $db->select()
                ->from(['p' => 'Pages'])
                ->order('p.pageId ASC')
                ->limit(1);

            $res = $db->fetchRow($select);

            if (!empty($res)) {
                $page = new Page();
                $page->populate($res);

                return $page;
            }
        }

        return false;
    }

    public function getSlugs()
    {
        $db     = $this->getAdapter();
        $select = $db->select()
            ->from(['p' => 'Pages'], ['slug'])
            ->order('p.pageId ASC');

        $res = $db->fetchAll($select);

        return $res;
    }
}