<?php

/**
 *
 * The collection of Domain objects
 *
 * @author     Aleksey Kagarlykskiy
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_Collection_CustomContent extends Core_Collection_Super
{
    protected $_domainObjectClass = 'Core_Model_CustomContent';

    public function getGroups()
    {
        $groups = [];
        foreach ($this as $p) {
            if (!array_key_exists($p->type, $groups)) {
                $gr = new SettingsCollection;
                $gr->add($p);
                $groups[$p->type] = $gr;
            } else {
                $groups[$p->type]->add($p);
            }
        }

        return $groups;
    }

    public function getGroup($type)
    {
        $gr = new SettingsCollection;
        foreach ($this as $p) {
            if ($p->type == $type) {
                $gr->add($p);
            }
        }

        return $gr;
    }
}
