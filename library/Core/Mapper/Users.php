<?php

/**
 *
 * RolesMapper class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_Mapper_Users extends Core_Mapper_Super
{
    public function getRolesPairs($where = null)
    {
        $db     = $this->getDbTable()->getAdapter();
        $select = $db->select()
            ->from(['r' => 'Roles'], ['r.roleId', 'r.roleName'])
            ->where('r.editable = ?', 1);

        if ($where != null) {
            $select->where($where);
        }

        return $db->fetchPairs($select);
    }
}