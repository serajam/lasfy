<?php

/**
 *
 * Users mapper class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class ClientMapper extends Core_Mapper_Front
{
    /**
     *
     * Users DbTable class
     *
     * @var DbTable_Users
     */
    protected $_tableName = 'Core_DbTable_Users';

    /**
     *
     * Users model
     *
     * @var Core_Mapper_Super
     */
    protected $_rowClass = 'Users';

    /**
     *
     * Returns current user role
     *
     * @param Core_Model_User $user
     *
     * @return boolean
     */
    public function getUserRole(Core_Model_User $user)
    {
        $db     = $this->getDbTable()->getAdapter();
        $select = $db->select()->from(['ur' => 'UsersRoles'], [])
            ->joinLeft(
                ['r' => 'Roles'],
                'ur.roleId = r.roleId',
                ['roleCode', 'roleName']
            )
            ->joinLeft(
                ['sm' => 'SystemModules'],
                'sm.id = r.defaultModule',
                ['sm.moduleCode as defaultModule']
            )
            ->joinLeft(
                ['rr' => 'Resources'],
                'rr.resourceId = r.defaultController',
                ['rr.resourceCode as defaultController']
            )
            ->joinLeft(
                ['a' => 'Rights'],
                'a.rightId = r.defaultAction',
                ['a.action as defaultAction']
            )
            ->where('ur.userId = ' . $user->userId);

        $result = $db->fetchRow($select);

        if (empty($result)) {
            return false;
        }

        $user->role          = $result['roleCode'];
        $user->defaultModule = $result['defaultModule'];
        list($mod, $cntrl) = explode(':', $result['defaultController']);
        $user->defaultController = $cntrl;
        $user->defaultAction     = $result['defaultAction'];
        $user->roleName          = $result['roleName'];

        return true;
    }

    /**
     *
     * Bind tole to supplier
     *
     * @param Supplier $supplier | supplier object
     *
     * @return boolean
     */
    public function addUserRole($roleId, $userId)
    {
        $data = [
            'roleId' => $roleId,
            'userId' => $userId
        ];

        return $this->getDbTable()->getAdapter()->insert('UsersRoles', $data);
    }

    /**
     *
     * TEMPORARY
     * Fetches Users list
     */
    public function getUsersList()
    {
        $db     = $this->getDbTable()->getAdapter();
        $select = $db->select()
            ->from(
                ['u' => 'Users'],
                ['u.login', 'u.password', 'u.name', 'u.surname', 'u.patronymic']
            )
            ->joinLeft(
                ['ur' => 'UsersRoles'],
                'u.userId = ur.userId',
                []
            )
            ->joinLeft(
                ['r' => 'Roles'],
                'ur.roleId = r.roleId',
                ['r.roleName', 'r.active']
            )
            ->joinLeft(
                ['sm' => 'SystemModules'],
                'sm.id = r.defaultModule',
                ['sm.moduleName']
            )
            ->joinLeft(
                ['ud' => 'users_departments'],
                'u.userId = ud.userId',
                []
            )
            ->joinLeft(
                ['d' => 'departments'],
                'd.department_id = ud.department_id',
                ['d.department_name']
            )
            ->order('d.department_id ASC');

        return $db->fetchAll($select);
    }

    /**
     *
     * Find supplier by e-mail
     *
     * @param string $email
     *
     * @return Zend_Db_Table_Row
     */
    public function getUserByEmail($email)
    {
        $db     = $this->getDbTable()->getAdapter();
        $select = $db->select()
            ->from(['u' => 'Users'])
            ->where('u.email = ?', $email);

        $res = $db->fetchRow($select);

        if (!empty($res)) {
            return new Core_Users_User($res);
        }

        return false;
    }

    /**
     * add password activation code
     *
     * @param Supplier $supp | supplier object
     * @param String   $code | activation code
     */
    public function addPasswordActivationCode($user, $code, $passwd)
    {
        $data = [
            'userId'       => $user->userId,
            'email'        => $user->email,
            'password'     => $passwd,
            'code'         => $code,
            'isActivated'  => 0,
            'generateDate' => Zend_Date::now()->toString('y-M-d H:i:s')
        ];
        $this->getDbTable()->getAdapter()->insert('UsersPasswordActivation', $data);
    }

    /**
     *
     * Get password's activation code
     *
     * @param bigint $code
     *
     * @return Zend_Db_Table_Row
     */
    public function getPasswordActivationCode($code)
    {
        $db     = $this->getDbTable()->getAdapter();
        $select = $db->select()
            ->from('UsersPasswordActivation')
            ->where('code = ?', $code);

        return $db->fetchRow($select);
    }

    /**
     *
     * Activate new password
     *
     * @param bigint $code
     *
     * @return Zend_Db_Table_Row
     */
    public function activatePassword($id)
    {
        $data = ['isActivated' => 1];
        $db   = $this->getDbTable()->getAdapter();
        $db->update('UsersPasswordActivation', $data, 'id ="' . $id . '"');

        $res = $db->select()
            ->from(['upa' => 'UsersPasswordActivation'], ['upa.password'])
            ->where('upa.id = ?', $id);

        return $db->fetchOne($res);
    }

    /**
     *
     * Set new password
     *
     * @param int $passwd
     *
     * @return Zend_Db_Table_Row
     */
    public function setNewPassword($id, $passwd)
    {
        $data = ['securityCode' => $passwd];
        $db   = $this->getDbTable()->getAdapter();
        $db->update('Users', $data, 'userId ="' . $id . '"');
    }

    public function getPage($pageSlug = false, $pageId = false, $lang)
    {
        $db     = $this->getAdapter();
        $select = $db->select()->from(['p' => 'Pages']);

        if (!empty($pageSlug)) {
            $select->where('p.slug = ?', $pageSlug);
        } else {
            $select->where('p.isDefaultPage = ?', 1);
        }
        $select->where('p.lang = ?', $lang);
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
            ->from(['p' => 'Pages'], ['p.slug'])
            ->order('p.pageId ASC');

        $res = $db->fetchAll($select);

        return $res;
    }
}