<?php

/**
 *
 * Pages db table
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class PagesTable extends Core_DbTable_Base
{
    /** Table name */
    protected $_name = 'Pages';

    protected $_primary = ['pageId', 'lang'];

    /**
     * Delete page only for specified language
     *
     * @param int    $pageId
     * @param string $lang
     * @param string $slug
     *
     * @return int
     *
     * @author Fedor Petryk
     */
    public function deleteByPrimaryKey($pageId, $lang, $slug)
    {
        return $this->delete(['pageId = ?' => $pageId, 'lang = ?' => $lang, 'slug = ?' => $slug]);
    }
}
