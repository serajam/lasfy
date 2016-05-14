<?php

/**
 *
 * The Default mapper class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class DefaultMapper extends Core_Mapper_Front
{
    /**
     *
     * Users DbTable class
     *
     * @var Core_DbTable_Pages
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
                ['i' => 'Images'],
                'i.imageId = gi.imageId AND (i.imageSizeType = g.thumbnailSize OR i.imageSizeType = g.fullSize)'
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

    /**
     * Get page by url (slug) or pageId and lang
     *
     * @param bool $pageSlug
     * @param bool $pageId
     * @param      $lang
     *
     * @return bool|Page
     * @throws Exception
     * @throws Zend_Exception
     */
    public function getPage($pageSlug = false, $pageId = false, $lang)
    {
        $db     = $this->getAdapter();
        $select = $db->select()->from(['p' => 'Pages']);

        if (!empty($pageSlug)) {
            $select->where('p.slug = ?', $pageSlug);
        } elseif (!empty($pageId)) {
            $select->where('p.pageId = ?', $pageId);
        } else {
            return false;
        }

        $conf   = Zend_Registry::get('types');
        $langId = $conf['language'][$lang];
        $select->where('p.lang = ?', $langId);

        $res = $db->fetchRow($select);

        if (!empty($res)) {
            $page = new Page();
            $page->populate($res);

            return $page;
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

    public function getActivationCode($code)
    {
        $db     = $this->getAdapter();
        $select = $db->select()
            ->from(['uac' => 'UsersActivationCodes'])
            ->where('uac.activationCode = ?', $code);

        $res = $db->fetchRow($select);

        return $res;
    }

    public function activateUser($code)
    {
        $db         = $this->getAdapter();
        $updateData = [
            'isUsed'         => 1,
            'activationDate' => date('Y-m-d H:i:s')
        ];

        $where = [
            'activationCode = ?' => $code['activationCode']
        ];

        $resUpdate = $db->update('UsersActivationCodes', $updateData, $where);

        if (empty($resUpdate)) {
            return false;
        }

        $updateData = [
            'isActivated' => 1,
        ];

        $where = [
            'userId = ?' => $code['userId']
        ];

        $resUpdate = $db->update('Users', $updateData, $where);

        return $resUpdate;
    }

    public function getUserProfile($identifier, $providerName)
    {
        if (empty($identifier) || empty($providerName)) {
            return false;
        }

        $db = $this->getAdapter();

        // @TODO get user independently of social network
        $select = $db->select()
            ->from(['u' => 'Users'])
            ->where('u.userProfileIdentifier = ?', $identifier);

        $res = $db->fetchRow($select);

        if (!empty($res)) {
            return $res;
        }

        return $res;
    }

    public function setUserRole($userId)
    {
        $db = $this->getAdapter();

        $select = $db->select()
            ->from(['UsersRoles'])
            ->where('userId = ?', $userId);

        $row = $db->fetchRow($select);

        if (empty($row)) {

            $res    = false;
            $select = $db->select()
                ->from(['Roles'], ['roleId'])
                ->where('active = 1 AND roleCode = "user"');
            $roleId = $db->fetchOne($select);

            if (!empty($roleId)) {
                $data = [
                    'userId' => $userId,
                    'roleId' => $roleId
                ];

                $res = $db->insert('UsersRoles', $data);
            }

            return $res;
        } else {
            return false;
        }
    }

    /**
     *
     * Find user by e-mail
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

        $res = $db->fetchRow($select);

        if (!empty($res)) {
            return new Core_Users_User($res);
        }

        return false;
    }

    public function addPasswordActivationCode($user, $code, $passwd)
    {
        $data = [
            'userId'         => $user->userId,
            'email'          => $user->email,
            'password'       => $passwd,
            'activationCode' => $code,
            'isActivated'    => 0,
            'generateDate'   => Zend_Date::now()->toString('y-M-d H:i:s')
        ];
        $res  = $this->getDbTable()->getAdapter()
            ->insert('UsersPasswordActivationCodes', $data);

        return $res;
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
            ->from('UsersPasswordActivationCodes')
            ->where('activationCode = ?', $code);

        return $db->fetchRow($select);
    }

    public function activatePassword($id)
    {
        $data = [
            'isActivated'    => 1,
            'activationDate' => Zend_Date::now()->toString('y-M-d H:i:s')
        ];
        $db   = $this->getDbTable()->getAdapter();
        $db->update('UsersPasswordActivationCodes', $data, 'codeId ="' . $id . '"');

        $res = $db->select()
            ->from(['upa' => 'UsersPasswordActivationCodes'], 'upa.password')
            ->where('codeId = ?', $id);

        $passwd = $db->fetchOne($res);

        return $passwd;
    }

    public function setNewPassword($userId, $passwd)
    {
        $data = ['securityCode' => $passwd];
        $db   = $this->getDbTable()->getAdapter();
        $db->update('Users', $data, 'userId ="' . $userId . '"');
    }

    public function getBlogPages()
    {
        $db   = $this->getAdapter();
        $sql  = $db->select()->from('Pages')->where('type = 3');
        $conf = Zend_Registry::get('types');

        $langId = $conf['language'][Zend_Registry::get('language')];
        $sql->where('lang = ?', $langId);
        $sql->order('dateCreated DESC');
        $pageArr = $db->fetchAll($sql);
        if (!$pageArr) {
            return false;
        }

        return new Core_Collection_Pages($pageArr);
    }

    public function getNewsPages()
    {
        $db   = $this->getAdapter();
        $sql  = $db->select()->from('Pages')->where('type = 2');
        $conf = Zend_Registry::get('types');

        $langId = $conf['language'][Zend_Registry::get('language')];
        $sql->where('lang = ?', $langId);
        $sql->order('dateCreated DESC');
        $pageArr = $db->fetchAll($sql);
        if (!$pageArr) {
            return false;
        }

        return new Core_Collection_Pages($pageArr);
    }

    /**
     * @param string $type
     * @param string $lang
     *
     * @return Zend_Db_Table_Rowset_Abstract
     *
     * @author Fedor Petryk
     */
    public function getTagsByTypeWithText($type, $lang)
    {
        $conf   = Zend_Registry::get('types');
        $langId = $conf['language'][$lang];

        $db     = $this->getAdapter();
        $select = $db->select()
            ->from(['t' => 'Tags'], ['t.tagName'])
            ->joinInner(['p' => 'Pages'], 'p.slug = t.tagName', [])
            ->where('t.tagType = ?', $type)
            ->where('t.enable = 1')
            ->where('t.lang = ?', $langId)
            ->order('t.tagWeight DESC')
            ->order('t.tagName DESC');
        $res    = $db->fetchAll($select);

        return $res;
    }
}