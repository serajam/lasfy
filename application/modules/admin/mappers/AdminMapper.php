<?php

/**
 *
 * RolesMapper class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class AdminMapper extends Core_Mapper_Users
{
    /**
     *
     * DbTable Class
     *
     * @var RolesTable
     */
    protected $_tableName = 'RolesTable';

    /**
     *
     * Rols row class
     *
     * @var Role
     */
    protected $_rowClass = 'Role';

    /**
     * Alias for joined filters
     *
     * @var mixed
     */
    protected $_filtersAlias = [
        'roleId'        => 'r',
        'userId'        => 'u',
        'department_id' => 'd'
    ];

    /**
     *
     * Checks if rules are assignable by module (gets this from db)
     *
     * @param array $rules | the array of Resources and rules
     * @param Role  $model | The role model
     *
     * @return boolean
     */
    public function addRules($rules, $model)
    {
        $db     = $this->getDbTable()->getAdapter();
        $select = $db->select()
            ->from(['r' => 'Rights'], ['r.rightId'])
            ->joinLeft(
                ['rr' => 'ResourcesRights'], 'r.rightId = rr.rightId', []
            )
            ->joinLeft(
                ['res' => 'Resources'], 'res.resourceId = rr.resourceId', ['res.resourceId']
            )
            ->joinLeft(
                ['m' => 'SystemModules'], 'res.moduleId = m.id', ['m.assignable']
            );

        foreach ($rules as $res => $actions) {
            foreach ($actions as $action => $on) {
                $select->orWhere('r.rightId = ?', $action);
            }
        }
        $rules = $db->fetchAll($select);

        $query = 'INSERT INTO RolesRights (roleId, rightId) VALUES ';
        $i     = 1;
        foreach ($rules as $res => $actions) {
            $query .= ' (' . $model->roleId . ', ' . $actions['rightId'] . ')';
            if (count($rules) > $i) {
                $query .= ',';
            }
            $i++;
        }

        return $this->runQuery($query);
    }

    /**
     *
     * Clears all role Rights (rules)
     *
     * @param int $id roleId
     *
     * @return boolean
     */
    public function deleteAllRules($id)
    {
        return $this->getDbTable()->getAdapter()->query('DELETE FROM RolesRights WHERE roleId = ' . $id);
    }

    /**
     *
     * Get full role with rules by id
     *
     * @param int $id | role id
     *
     * @return Role
     */
    public function getRole($id)
    {
        $db     = $this->getDbTable()->getAdapter();
        $select = $db->select()
            ->from(['r' => 'Roles'])
            ->joinLeft(['rr' => 'RolesRights'], 'r.roleId = rr.roleId', ['rr.rightId'])
            ->joinLeft(['rer' => 'ResourcesRights'], 'rer.rightId = rr.rightId', ['rer.resourceId'])
            ->where('r.editable = ?', 1)
            ->where('r.roleId = ?', $id);
        $data   = $db->fetchAll($select);

        $model = new $this->_rowClass;
        if (!empty($data)) {
            $model->populate($data[0]);

            $Resources = [];
            foreach ($data as $reses) {
                $Resources['actions'][$reses['resourceId']]
                [$reses['rightId']] = $reses['rightId'];

                $Resources['Resources'][$reses['resourceId']] = $reses['resourceId'];

                $Resources['defaultModule']     = $reses['defaultModule'];
                $Resources['defaultController'] = $reses['defaultController'];
                $Resources['defaultAction']     = $reses['defaultAction'];
            }
            $model->setRules($Resources);
        } else {
            $model->setError(Core_Messages_Message::getMessage(302));
        }

        return $model;
    }

    /**
     *
     * Get ACL list from db
     *
     * @return array | access control list by modules
     */
    public function getFullAcl()
    {
        $db     = $this->getDbTable()->getAdapter();
        $select = $db->select()
            ->from(['m' => 'SystemModules'], ['m.moduleName', 'm.id'])
            ->joinLeft(['r' => 'Resources'], 'r.moduleId = m.id',
                ['r.resourceId', 'r.resourceName']
            )
            ->joinLeft(['rr' => 'ResourcesRights'], 'rr.resourceId = r.resourceId', [])
            ->joinLeft(['ri' => 'Rights'], 'ri.rightId = rr.rightId')
            ->where('m.assignable = ?', 1);

        return $db->fetchAll($select);
    }
}