<?php

/**
 *
 * The Users service class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class ClientService extends Core_Service_Front
{
    /**
     * Users mapper object
     *
     * @var UsersMapper
     */
    protected $_mapperName = 'ClientMapper';

    /**
     * @var UsersMapper
     */
    protected $_mapper;

    /**
     *
     * Login form name
     *
     * @var string
     */
    protected $_formName = 'LoginForm';

    /**
     * the login form class
     *
     * @var LoginForm
     */
    protected $_loginForm = null;

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
        $this->initLoginForm();
        if ($this->_loginForm->isValid($data)) {
            $user->populate($this->_loginForm->getValues());
            $auth = $user->authenticate();
            if ($auth) {
                $this->_mapper->getUserRole($user);
                if (empty($user->role)) {
                    $this->_loginForm->password->addError(
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
                $this->_loginForm->password->addError($user->getError());

                return false;
            }
        } else {
            return false;
        }
    }

    /**
     *
     * creates login form instance
     */
    public function initLoginForm()
    {
        $this->_loginForm = new LoginForm();
    }

    /**
     *
     * Returns login form
     *
     * @return Form_LoginForm
     */
    public function getLoginForm()
    {
        if (null == $this->_loginForm) {
            $this->initLoginForm();
        }

        return $this->_loginForm;
    }

    /**
     *
     * creates login form instance
     */
    public function initRegistrationForm()
    {
    }

    /**
     * Register supplier
     *
     * @param array $data | supplier date, see reg form
     * @param int   $id | id of the tender
     *
     * @return boolean
     */
    public function register(array $data, $id)
    {

        return false;
    }

    /**
     *
     * creates RecoveryPassword form instance
     */
    public function initRecoveryPasswordForm()
    {
    }

    public function recoverypasswd($email)
    {
        if (!$this->getRecoveryPasswordForm()->isValid($email)) {
            $this->setError(Core_Messages_Message::getMessage(300));

            return false;
        }

        $user = $this->_mapper->getUserByEmail($email['email']);

        if (!$user) {
            $this->setError(Core_Messages_Message::getMessage(304));

            return false;
        }

        $code   = mt_rand() . mt_rand() . mt_rand() . mt_rand() . mt_rand();
        $passwd = mt_rand();

        $this->_mapper->addPasswordActivationCode($user, $code, $passwd);
        $this->sendPasswordActivationCode($user, $code, $passwd);

        $this->setMessage(Core_Messages_Message::getMessage('successfull_recover_password'));

        return true;
    }

    /**
     *
     * Returns RecoveryPassword form
     *
     * @return RecoveryPasswordForm
     */
    public function getRecoveryPasswordForm()
    {
    }

    public function sendPasswordActivationCode($user, $code, $passwd)
    {
        $config = Zend_Registry::get('appConfig');
        $params = [
            'fio'    => $user->name,
            'link'   => $config['baseHttpPath'] . 'default/index/passwordactivation/code/' . $code,
            'passwd' => $passwd
        ];
        $mail   = new Core_Mailer('passwordactivation', $params, true);
        $mail->addTo($user->email, $user->name);
        $mail->send();

        return true;
    }

    public function activatepassword($code)
    {
        $alnum = new Zend_Validate_Alnum();
        if ($alnum->isValid($code)) {
            $dbcode = $this->_mapper->getPasswordActivationCode($code);
            if ($dbcode) {
                $passwd = $this->_mapper->activatePassword($dbcode['id']);

                $conf   = Zend_Registry::get('appConfig');
                $passwd = sha1($passwd . '-' . $conf['salt']);
                $this->_mapper->setNewPassword($dbcode['userId'], $passwd);

                return true;
            }
        }

        return false;
    }
}