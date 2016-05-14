<?php

/**
 * Loading Roles and resources, verifying Users rights
 *
 * @author     Petryk Fedor
 * @uses       Zend_Acl
 * @package    Core_Acl
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_Acl_AclBuilder extends Zend_Acl
{
    CONST DEFAULT_ROLE_CODE = 'guest';

    /**
     * Db gateway
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db = null;

    /**
     * ACL Tree  registry
     *
     * @var Array
     */
    protected $_rolesAcl = null;

    /**
     * Initializes DB from registry
     *
     */
    public function init()
    {
        $this->_db = Zend_Registry::get('DB');
    }

    /**
     * Returns the ACL
     *
     * @return Array (Tree)
     */
    public function getRoleAcl()
    {
        return $this->_rolesAcl;
    }

    /**
     * Builds ACL Tree according to that in DB
     *
     * @uses   Zend_Db_Adapter_Abstract::get()
     */
    public function buildAcl($role)
    {
        $permissions = $this->getAllRoles($role);
        $this->_buildAcl($permissions);
    }

    /**
     * Get all roles in the system
     *
     * @param $role
     *
     * @return array
     * @throws Zend_Exception
     */
    protected function getAllRoles($role)
    {
        $cache    = Zend_Registry::get('cache');
        $cacheKey = 'acl_' . $role;
        if (($acl = $cache->load($cacheKey)) === false) {
            $select = $this->_aclQuery();
            $select->where('r.roleCode = "' . $role . '"')
                ->orWhere('r.roleCode = ?', self::DEFAULT_ROLE_CODE);
            $acl = $this->_db->fetchAll($select);
            $cache->save($acl, $cacheKey, [], 3600);
        }

        return $acl;
    }

    protected function _aclQuery()
    {
        $select = $this->_db->select()
            ->from(
                ['r' => 'Roles'],
                [
                    'roleCode',
                    'roleName',
                    'roleId',
                    'parentRoleId',
                    'editable'
                ]
            )
            ->joinLeft(
                ['rr' => 'RolesRights'],
                'rr.roleId = r.roleId',
                []
            )
            ->joinLeft(
                ['ri' => 'Rights'],
                'ri.rightId = rr.rightId',
                ['action', 'rightName', 'menu']
            )
            ->joinLeft(
                ['rer' => 'ResourcesRights'],
                'rer.rightId = ri.rightId',
                []
            )
            ->joinLeft(
                ['res' => 'Resources'],
                'res.resourceId = rer.resourceId',
                ['resourceCode', 'resourceName']
            )
            ->joinLeft(
                ['m' => 'SystemModules'],
                'm.id = res.moduleId',
                ['id', 'moduleName', 'moduleCode AS defaultModule', 'show']
            )
            ->order('r.roleId ASC')
            ->order('m.menuPosition ASC')
            ->order('res.resourceId ASC')
            ->order('ri.rightId ASC')
            ->order('r.parentRoleId ASC');

        return $select;
    }

    /**
     * Builds ACL Tree according to that in DB
     *
     * @uses   Zend_Db_Adapter_Abstract::get()
     */
    protected function _buildAcl($permissions)
    {
        $groups = [];
        foreach ($permissions as $key => $group) {
            $groups[$group['roleCode']][$key] = $group;
        }
        $this->_rolesAcl = $groups;
        $cache           = Zend_Registry::get('cache');
        $cacheKey        = 'Resources';
        if (($resources = $cache->load($cacheKey)) === false) {
            $select    = $this->_db->select()->from('Resources');
            $resources = $this->_db->fetchAssoc($select);
            try {
                $cache->save($resources, $cacheKey, [], '3600');
            } catch (Exception $e) {
                error_log('Could not save data in memcache; Key: ' . $cacheKey);
            }
        }
        foreach ($resources as $res) {
            $this->addResource($res['resourceCode']);
        }
        foreach ($groups as $role => $g) {
            if ($role != self::DEFAULT_ROLE_CODE) {
                $this->addRole(new Zend_Acl_Role($role), self::DEFAULT_ROLE_CODE);
            } else {
                $this->addRole(new Zend_Acl_Role($role));
            }
        }
        foreach ($groups as $key => $g) {
            foreach ($g as $user) {
                $this->allow(
                    $user['roleCode'],
                    $user['resourceCode'],
                    $user['action']
                );
            }
        }
    }

    /**
     * Builds ACL Tree according to that in DB
     *
     * @uses   Zend_Db_Adapter_Abstract::get()
     */
    public function buildFullAcl()
    {
        $res_permissions = $this->getFullRoles();
        $this->_buildAcl($res_permissions);
    }

    /**
     *
     * Returns all Roles in the system
     *
     * @return array | Users Roles list
     */
    protected function getFullRoles()
    {
        $select = $this->_aclQuery();
        $acl    = $this->_db->fetchAll($select);

        return $acl;
    }
}