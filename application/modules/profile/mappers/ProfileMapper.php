<?php

/**
 *
 * The Profile mapper class
 *
 * @author     Alexey Kagarlykskiy
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class ProfileMapper extends Core_Mapper_Front
{
    /**
     *
     * Users DbTable class
     *
     * @var Core_DbTable_Pages
     */
    protected $_tableName = 'Core_DbTable_Users';

    /**
     *
     * Users model
     *
     * @var Core_Mapper_Super
     */
    protected $_rowClass = 'User';

    /**
     * Save user subscription
     *
     * @param Subscription $subscription
     *
     * @return bool|mixed
     *
     * @author Fedor Petryk
     */
    public function saveSubscription(Subscription $subscription)
    {
        $table = new UserSubscriptionsTable();

        try {
            return $table->saveModel($subscription);
        } catch (Exception $e) {
            error_log("{$e}");

            return false;
        }
    }

    public function getUserConversations($userId)
    {
        $db  = $this->getAdapter();
        $sql = $db->select()
            ->from(['um' => 'UsersMessages'])
            ->columns('SUM(um.new) AS messagesCount')
            ->columns('v.seat AS vseat')
            ->columns('r.seat AS rseat')
            ->joinLeft(
                ['v' => 'Vacancies'],
                'v.vacancyId = um.addId AND um.addType = "' . MessagesAccess::VACANCY_TYPE . '" AND v.vacancyId IS NOT NULL '
            )
            ->joinLeft(
                ['r' => 'Resumes'],
                'r.resumeId = um.addId AND um.addType = "' . MessagesAccess::RESUME_TYPE . '" AND r.resumeId  IS NOT NULL '
            )
            ->joinLeft(['u' => 'Users', 'name'], 'u.userId = um.userFrom OR u.userId = um.userTo');
        $sql
            ->where('um.userTo = ?', $userId)
            ->group('um.addId')
            ->group('um.addType')
            ->order('um.sendDate DESC')
            ->limit(100);

        $result = $db->fetchAll($sql);
        if (!$result) {
            return false;
        }
        $collection = new MessagesCollection();

        foreach ($result as $add) {
            // message corrupted;
            if (!isset($add['vacancyId']) && !isset($add['resumeId'])) {
                Core_Log_Logger::logErrorEvent(new Exception('Got corrupted message:' . $add['messageId']));
                continue;
            }
            $conversation = new Conversation($add['addType']);
            $conversation->setNewMessages($add['messagesCount']);
            $conversation->setLastMessage($add['message']);
            $conversation->setLastMessageDate($add['sendDate']);
            $conversation->setLastMessageAuthor($add['userName']);
            $conversation->setConversationId($add['addId']);
            $add['vseat'] ? $conversation->setName($add['vseat']) : $conversation->setName($add['rseat']);
            $collection->add($conversation);
        }

        return $collection;
    }

    /**
     * @param      $addId
     * @param      $addType
     * @param bool $replierId
     */
    public function markMessagesAsViewed($addId, $addType, $replierId = false)
    {
        $where = [
            'addId =?'      => $addId,
            'addType = ?'   => $addType,
            'userFrom != ?' => Core_Model_User::getInstance()->userId
        ];
        if ($replierId) {
            $where['userFrom = ?'] = $replierId;
        }
        $db = $this->getAdapter();
        $db->update(
            'UsersMessages',
            ['new' => '0'],
            $where
        );
    }

    /**
     * @param $addId
     * @param $userId
     * @param $addType
     *
     * @return bool|UsersCollection
     */
    public function getRepliers($addId, $userId, $addType)
    {
        $db  = $this->getAdapter();
        $sql = $db->select()->from(['um' => 'UsersMessages'])
            ->joinLeft(['u' => 'Users'], 'u.userId = um.userFrom');
        $sql
            ->where('um.addType = ?', $addType)
            ->where('um.addId = ?', $addId)
            ->where('um.userTo = ?', $userId)
            ->where('um.userFrom != ?', $userId)
            ->group('um.userFrom')
            ->order('um.sendDate DESC');
        $result = $db->fetchAll($sql);
        if (!$result) {
            return false;
        }

        $users = new UsersCollection($result);

        return $users;
    }

    /**
     * @param $addId
     * @param $type
     * @param $ownerId
     * @param $viewerId
     *
     * @return bool|MessagesCollection
     */
    public function getAddMessages($addId, $type, $ownerId, $viewerId)
    {
        $db  = $this->getAdapter();
        $sql = $db->select()->from(['um' => 'UsersMessages']);
        $sql
            ->where('um.addType = ?', $type)
            ->where('um.addId = ?', $addId)
            ->where(
                '(um.userFrom = ' . $ownerId . ' AND um.userTo = ' . $viewerId . ') OR (um.userFrom = ' . $viewerId . ' AND um.userTo = ' . $ownerId . ')'
            );
        $sql->order('um.sendDate DESC')
            ->limit(100);

        $result = $db->fetchAll($sql);
        if (!$result) {
            return false;
        }
        $collection = new MessagesCollection($result);

        return $collection;
    }

    /**
     * @param Message $message
     */
    public function saveMessage(Message $message)
    {
        $this->getAdapter()->insert('UsersMessages', $message->toArray());
    }

    /**
     * @param $senderId
     * @param $receiverId
     *
     * @return bool|UserRelation
     */
    public function getUsersRelations($senderId, $receiverId)
    {
        $db       = $this->getAdapter();
        $sql      = $db->select()->from(['ur' => 'UsersRelations'])
            ->where('userId = ?', $senderId)
            ->where('friendId = ?', $receiverId);
        $relation = $db->fetchRow($sql);
        if (!$relation) {
            return false;
        }

        return new UserRelation($relation);
    }

    /**
     * @param $id
     * @param $newPassword
     *
     * @return int
     */
    public function setPassword($id, $newPassword)
    {
        $passwd = ['securityCode' => $newPassword];

        return $this->getDbTable()->update($passwd, ['userId = ?' => $id]);
    }

    /**
     * @param UserRelation $userRelation
     */
    public function addUserRelations(UserRelation $userRelation)
    {
        $db = $this->getAdapter();
        $db->insert('UsersRelations', $userRelation->toArray());
    }

    public function getCompanyLogo($userId)
    {
        $db = $this->getAdapter();

        $sql = $db->select()
            ->from(['u' => 'Users'], [''])
            ->joinLeft(['c' => 'Companies'], 'u.userId = c.userId', ['logo'])
            ->where('u.userId=' . $userId);

        $result = $db->fetchOne($sql);

        if (empty($result)) {
            return false;
        }

        return $result;
    }

    public function getUserCompany($userId)
    {
        $db  = $this->getAdapter();
        $sql = $db->select()
            ->from(['c' => 'Companies'])
            ->where('c.userId=' . $userId);

        $result = $db->fetchRow($sql);

        if (empty($result)) {
            return false;
        }

        $company = new Core_Model_Company();
        $company->populate($result);

        return $company;
    }

    /**
     * @param $vacancyId
     *
     * @return bool|Vacancy
     * @throws Exception
     */
    public function getVacancy($vacancyId)
    {
        $db     = $this->getAdapter();
        $sql    = $db->select()
            ->from(['v' => 'Vacancies'])
            ->joinInner(['uv' => 'UsersVacancy'], 'uv.vacancyId = v.vacancyId')
            ->where('v.vacancyId = ?', $vacancyId);
        $result = $db->fetchRow($sql);

        if (empty($result)) {
            return false;
        }

        $vacancy = new Vacancy();
        $vacancy->populate($result);

        if (isset($result['companyId'])) {
            $company = new Core_Model_Company();
            $company->populate($result);
            $vacancy->setCompany($company);
        }

        return $vacancy;
    }

    /**
     * @param $resumeId
     *
     * @return bool|Resume
     * @throws Exception
     */
    public function getResume($resumeId)
    {
        $db     = $this->getAdapter();
        $sql    = $db->select()
            ->from(['r' => 'Resumes'])
            ->joinInner(['ur' => 'UsersResume'], 'r.resumeId = ur.resumeId')
            ->where('r.resumeId = ?', $resumeId);
        $result = $db->fetchRow($sql);

        if (empty($result)) {
            return false;
        }

        $resume = new Resume();
        $resume->populate($result);

        return $resume;
    }
}