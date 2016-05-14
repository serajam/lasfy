<?php

/**
 *
 * The Users service class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class UsersService extends Core_Service_Editor
{
    /**
     * Users mapper object
     *
     * @var UsersMapper
     */
    protected $_mapperName = 'UsersMapper';

    /**
     * @var UsersMapper
     */
    protected $_mapper;

    /**
     *
     * Clearing session
     */
    public static function logout()
    {
        Zend_Auth::getInstance()->clearIdentity();
    }

    public function isAlive()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $this->setMessage(Core_Messages_Message::getMessage('session_renewed'));
        } else {
            $this->setError(Core_Messages_Message::getMessage('session_renewal_failed'));
        }
    }

    /**
     *
     * Authentication of user
     *
     * @uses Core_Model_User
     *
     * @param array $data
     *
     * @return boolean
     */
    public function authenticate(array $data)
    {
        $user = Core_Model_User::getInstance();
        if ($this->getLoginForm()->isValid($data)) {
            $user->populate($this->getLoginForm()->getValues());
            $auth = $user->authenticate();
            if ($auth) {
                $this->_mapper->getUserRole($user);
                if (empty($user->role)) {
                    $this->getLoginForm()->password->addError(
                        Core_Messages_Message::getMessage(202, [0 => $user->login])
                    );
                    $user->clear();
                    Zend_Auth::getInstance()->clearIdentity();

                    return false;
                }
                $session = new Zend_Session_Namespace('Zend_Auth');
                $timeout = Core_Model_Settings::settings('sessionTime') * 60;
                $session->setExpirationSeconds($timeout);
                $user->timeout = $timeout - 10;
                $user->createUserStorage();

                return true;
            } else {
                $this->getLoginForm()->password->addError($user->getError());
            }
        }

        return false;
    }

    /**
     *
     * Returns login form
     *
     * @return Form_LoginForm
     */
    public function getLoginForm()
    {
        return $this->getFormLoader()->getForm('LoginForm');
    }

    public function changeUserRole(array $data)
    {
        $from = $this->getRoleForm();
        if ($from->isValid($data)) {
            $this->_mapper->saveUserRole($data);
            $this->setMessage(Core_Messages_Message::getMessage(1));

            return true;
        }

        $this->setError('303');
    }

    public function getRoleForm()
    {

        if ($this->getFormLoader()->isExists('UserRole')) {
            return $this->getFormLoader()->getForm('UserRole');
        }
        $form  = $this->getFormLoader()->getForm('UserRole');
        $roles = $this->_mapper->getRolesPairs();
        $form->getElement('roleId')->setMultioptions(
            ['0' => __('select')] + $roles
        );

        return $form;
    }

    public function getDefaultMenu()
    {
        return $this->_mapper->getSlugs();
    }

    /**
     * Get Users's data
     */
    public function getUsers($page, $type = 0, $userId = false)
    {
        $users = $this->_mapper->getUsers($page, $type, $userId);

        return $users;
    }

    /**
     * Get user's data
     */
    public function getUser($userId)
    {
        return $this->_mapper->getUser($userId);
    }

    /**
     *
     * Edit user's data
     */
    public function editUserData(array $data, $userId)
    {
        $form = $this->getFormLoader()->getForm('UserForm');
        if ($userId == null) {
            return $this->addUser($data);
        }

        if (!$form->isValid($data)) {
            $this->setError('300');
            $this->setFormMessages($form->getMessages());
        }
        $data = $form->getValues();
        $res  = $this->_mapper->edtiUserData($data, $userId);
        if ($res >= 0) {
            $this->setMessage(Core_Messages_Message::getMessage(1));
        }

        return false;
    }

    /**
     *
     * Add user
     */
    public function addUser(array $data)
    {
        $form = $this->getCreateUserForm();
        if ($form->isValid($data)) {
            $user = new Core_Users_User($data);
            $user->setPasswordHash();
            $user->isActivated = 1;
            $user->type        = Core_Users_User::MANAGER_USER;
            $user->userCode    = '0';

            $res = $this->_mapper->addUser($user->toArray());
            if ($res >= 0) {
                $this->setMessage(Core_Messages_Message::getMessage(1));
            }
            $data['userId'] = $res;
            $this->_mapper->saveUserRole($data);
        } else {
            $this->setError('300');
            $this->setFormMessages($form->getMessages());
        }

        return false;
    }

    public function getCreateUserForm()
    {
        if ($this->getFormLoader()->isExists('CreateUserForm')) {
            return $this->getFormLoader()->getForm('CreateUserForm');
        }
        $form  = $this->getFormLoader()->getForm('CreateUserForm');
        $roles = $this->_mapper->getRolesPairs();
        $form->getElement('roleId')->setMultioptions(
            ['0' => __('select')] + $roles
        );

        return $form;
    }

    /**
     * Edit user's password
     */
    public function changePassword($data, $userId)
    {
        $form = $this->getFormLoader()->getForm('NewPasswordForm');
        if ($form->isValid($data)) {
            $data     = $form->getValues();
            $tempPass = $data['password'];
            // Base-2 logarithm of the iteration count used for password stretching
            $hashCostLog2 = 8;
            // Do we require the hashes to be portable to older systems (less secure)?
            $hashPortable = false;
            $hasher       = new PasswordHash($hashCostLog2, $hashPortable);
            $password     = $hasher->HashPassword($tempPass);

            $res = $this->_mapper->changePassword($password, $userId);
            if ($res >= 0) {
                $this->setMessage(Core_Messages_Message::getMessage(1));
            }
        } else {
            $this->setError('300');
            $this->setFormMessages($form->getMessages());
        }

        return true;
    }

    public function getUserDetails($userId)
    {
        $profileDetails = $this->_mapper->getUserProfileDetails($userId);

        return $profileDetails;
    }

    public function isBanned($userId, $isBanned)
    {
        $data = ['isBanned' => $isBanned];
        $this->_mapper->isBanned($data, $userId);
        $this->setMessage(Core_Messages_Message::getMessage(1));
    }

    public function isActivated($userId, $isActivated)
    {
        $data = ['isActivated' => $isActivated];
        $this->_mapper->isActivated($data, $userId);
        $this->setMessage(Core_Messages_Message::getMessage(1));
    }

    public function delete($userId)
    {
        $this->_mapper->getDbTable()->delete(['userId = ?' => $userId]);
        $this->setMessage(__(1));
    }
}