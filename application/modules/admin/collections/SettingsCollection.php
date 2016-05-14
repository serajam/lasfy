<?php

/**
 *
 * The collection of Domain objects
 *
 * @author     Aleksey Kagarlykskiy
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class SettingsCollection extends Core_Collection_Super
{
    protected $_domainObjectClass = 'Settings';

    public function getGroups()
    {
        $groups = [];
        foreach ($this as $p) {
            if (!array_key_exists($p->paramGroup, $groups)) {
                $gr = new SettingsCollection;
                $gr->add($p);
                $groups[$p->paramGroup] = $gr;
            } else {
                $groups[$p->paramGroup]->add($p);
            }
        }

        return $groups;
    }
}
