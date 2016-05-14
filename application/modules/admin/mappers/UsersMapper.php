<?php

/**
 *
 * Users mapper class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class UsersMapper extends Core_Mapper_Users
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
            ->where('email = ?', $email);
        $res    = $db->fetchRow($select);

        if (!empty($res)) {
            return new Core_Users_User($res);
        }

        return false;
    }

    /**
     *
     * Pairs of Users and Roles
     *
     * @return array | list of possible Users Roles
     */
    public function getUsersRoles()
    {
        $db     = $this->getDbTable()->getAdapter();
        $select = $db->select()
            ->from(['u' => 'Users'], ['userId', 'login'])
            ->joinLeft(
                ['ur' => 'UsersRoles'],
                'ur.userId = u.userId',
                ['roleId']
            )
            ->order('u.login ASC');

        return $db->fetchAll($select);
    }

    public function getUsers($page = 1, $type, $userId)
    {
        $db  = $this->getDbTable()->getAdapter();
        $sql = $db->select()
            ->from(['u' => 'Users'])
            ->joinLeft(
                ['ur' => 'UsersRoles'],
                'ur.userId = u.userId',
                []
            )
            ->joinLeft(
                ['r' => 'Roles'],
                'ur.roleId = r.roleId',
                ['roleName']
            )
            ->order('u.userId DESC')
            ->where('u.type = ?', $type);

        if ($userId) {
            $sql->where('u.userId = ?', $userId);
        }

        $adapter = new Zend_Paginator_Adapter_DbSelect($sql);
        $adapter->setRowCount(
            $db->select()
                ->from(
                    'Users',
                    [
                        Zend_Paginator_Adapter_DbSelect::ROW_COUNT_COLUMN => 'count(*)'
                    ]
                )
                ->where('userId = ?', $userId)
        );
        $paginator = new Zend_Paginator($adapter);
        $config    = Zend_Registry::get('appConfig');
        $users     = new UsersCollection();
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($config['itemsPerPage']);
        $users->populate($paginator->getCurrentItems());
        $users->setPaginator($paginator);

        return $users;
    }

    public function getUser($userId)
    {
        $db     = $this->getDbTable()->getAdapter();
        $select = $db->select()
            ->from(['u' => 'Users'])
            ->where('u.userId = ' . $userId);
        $result = $db->fetchRow($select);

        return $result;
    }

    /**
     *
     * Edit user's data
     */
    public function edtiUserData($data, $userId)
    {
        $this->setDbTable('Core_DbTable_Users');

        return $this->getDbTable()->update($data, 'userId = "' . $userId . '"');
    }

    /**
     *
     * Edit user's data
     */
    public function addUser($data)
    {
        $this->setDbTable('Core_DbTable_Users');

        return $this->getDbTable()->insert($data);
    }

    /**
     *
     * Edit user's password
     */
    public function changePassword($password, $userId)
    {
        $this->setDbTable('Core_DbTable_Users');

        return $this->getDbTable()->update(['securityCode' => $password], ['userId = ?' => $userId]);
    }

    public function isBanned($data, $userId)
    {
        $this->setDbTable('Core_DbTable_Users');

        return $this->getDbTable()->update($data, 'userId = ' . $userId);
    }

    public function isActivated($data, $userId)
    {
        $this->setDbTable('Core_DbTable_Users');

        return $this->getDbTable()->update($data, 'userId = ' . $userId);
    }

    /**
     *
     * Saves new user role
     *
     * @param array $data | user id, role id
     *
     * @return boolean
     */
    public function saveUserRole(array $data)
    {
        $this->setDbTable('UsersRolesTable');
        $db = $this->getDbTable();

        if ($data['roleId'] == 0) {
            $db->delete('userId = "' . $data['userId'] . '"');

            return true;
        }

        $select = $db->getAdapter()->select()
            ->from(['Roles'])
            ->where('editable = 1 AND roleId = "' . $data['roleId'] . '"');
        $role   = $db->getAdapter()->fetchOne($select);
        $role   = new Role($role);
        if (empty($role)) {
            return false;
        } else {
            if (!$role->editable) {
                return false;
            }
        }

        $select = $db->select()
            ->from(['UsersRoles'])
            ->where('userId = "' . $data['userId'] . '"');

        $row = $db->fetchRow($select);

        if (empty($row)) {
            $data = $this->getDbTable()->cleanArray($data);
            $this->getDbTable()->insert($data);
        } else {
            $row->roleId = $data['roleId'];
            $row->save();
        }

        return true;
    }
}