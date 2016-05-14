<?php

/**
 *
 * The page service class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class DefaultService extends Core_Service_Front
{
    const FORM_SIGN_IN = 'SignInForm';
    const FORM_SIGN_UP = 'SignUpForm';

    /**
     * Users mapper object
     *
     * @var DefaultMapper
     */
    protected $_mapperName = 'DefaultMapper';

    /**
     * @var DefaultMapper
     */
    protected $_mapper;

    public function getGalleries()
    {
        return $this->_mapper->getGalleries();
    }

    public function getPage($pageSlug)
    {
        $page = $this->_mapper->getPage($pageSlug);

        if (empty($page)) {
            $this->setError(Core_Messages_Message::getMessage('no_default_page'));

            return false;
        }

        return $page;
    }

    public function getDefaultMenu()
    {
        $slugs = $this->_mapper->getSlugs();

        return $slugs;
    }

    public function getSearchForm()
    {
        if ($this->getFormLoader()->isExists('SearchForm')) {
            return $this->getFormLoader()->getForm('SearchForm');
        }

        $searchForm = $this->getFormLoader()->addForm('SearchForm');

        return $searchForm;
    }

    public function sendEmail($data)
    {
        $form = $this->getFeedbackForm();
        if (!$form->isValid($data)) {
            $this->_processFormError($form);

            return false;
        }

        $mailVars = [
            'name'    => $data['name'],
            'email'   => $data['email'],
            'message' => $data['message']
        ];

        $mailer = new Core_Mailer('feedBackMessage', $mailVars, 1);
        $mailer->addTo($this->getConfigParam('infoEmail'));
        $mailer->send();

        $this->setMessage(__('successfulSend'));

        return true;
    }

    public function getFeedbackForm()
    {
        if ($this->getFormLoader()->isExists('FeedbackForm')) {
            return $this->getFormLoader()->getForm('FeedbackForm');
        }

        $feedbackForm = $this->getFormLoader()->addForm('FeedbackForm');

        return $feedbackForm;
    }

    public function activateUser($code)
    {
        if (empty($code)) {
            $this->setError(Core_Messages_Message::getMessage('codeEmpty'));

            return false;
        }

        $codeInDB = $this->_mapper->getActivationCode($code);

        if (empty($codeInDB)) {
            $this->setError(Core_Messages_Message::getMessage('wrongCode' . ' - ' . $code));

            return false;
        }

        $resActivation = $this->_mapper->activateUser($codeInDB);

        if ($resActivation) {
            $this->setMessage(__('successfulActivation'));

            return true;
        }

        return false;
    }

    public function thirdPartRegistration($userProfile, $providerName)
    {
        $existedUser = $this->_mapper->getUserProfile($userProfile->identifier, $providerName);

        if ($existedUser) {
            $auth = Zend_Auth::getInstance();
            if (!$auth->hasIdentity()) {
                $user = Core_Model_User::getInstance();
                $user->populate($existedUser);
                $res = $this->_createUserStorage($user);
                if (!$res) {
                    $this->setError(Core_Messages_Message::getMessage('cantCreateUserStorage'));

                    return false;
                }
            } else {
                $this->setError(__('youAlreadyLogged'));

                return false;
            }
        } else {
            $userData = [];

            $password                          = mt_rand();
            $userData['login']                 = $userProfile->email;
            $userData['password']              = $password;
            $userData['repeatPassword']        = $password;
            $userData['userProfileIdentifier'] = $userProfile->identifier;
            $userData['providerName']          = $providerName;
            $userData['userName']              = $userProfile->firstName . ' ' . $userProfile->lastName;
            $userData['agreement']             = 1;

            return $this->signUp($userData, true);
        }
    }

    protected function _createUserStorage(Core_Model_User $user)
    {
        $this->_mapper->getUserRole($user);
        if (empty($user->role)) {
            /*            $this->getSignInForm()->password->addError(
                            Core_Messages_Message::getMessage('userRoleNotAssigned', array(0 => $user->login))
                        );*/
            $this->setError(Core_Messages_Message::getMessage('userRoleNotAssigned'));

            $user->clear();
            Zend_Auth::getInstance()->clearIdentity();

            return false;
        }
        $session = new Zend_Session_Namespace('Zend_Auth');
        $timeout = Core_Model_Settings::settings('sessionTime') * 60;
        $session->setExpirationSeconds($timeout);
        $user->timeout       = $timeout - 10;
        $user->lastLoginDate = date('Y-m-d H:i:s');
        $user->createUserStorage();
        $this->_mapper->setDbTable('Core_DbTable_Users');
        $this->save($user);

        return true;
    }

    public function signUp($data, $isSocialNetRegistration = false)
    {
        $signUpForm = $this->getSignUpForm();

        if ($isSocialNetRegistration) {
            $signUpForm->removeElement('captchaCode');
        }

        if (!$signUpForm->isValid($data)) {
            $this->_processFormError($signUpForm);

            return false;
        }

        $newUser       = new Core_Users_User();
        $populatedData = array_merge($signUpForm->getValues(), $data);
        $newUser->populate($populatedData);

        $newUser->email = $newUser->login;
        !$newUser->userName ? $newUser->userName = $newUser->login : '';
        $newUser->setPasswordHash();

        $newUser->registrationDate = date('Y-m-d H:i:s');
        $newUser->type             = Core_Users_User::SIMPLE_USER;
        $newUser->activationCode   = time() . mt_rand() . time() . mt_rand() . time();
        if ($isSocialNetRegistration) {
            $newUser->isActivated = 1;
        }

        $this->_mapper->setDbTable('Core_DbTable_Users');
        $newUserSaved = $this->save($newUser);

        if (!$newUserSaved->userId || $newUserSaved->getError()) {
            $error = __('userIsNotSaved') . ' - ' . $newUserSaved->getError();
            $this->setError(Core_Messages_Message::getMessage($error));

            return false;
        }

        $this->_mapper->setUserRole($newUserSaved->userId);

        $this->_mapper->setDbTable('UsersActivationCodesTable');

        $codeArray = [
            'userId'         => $newUserSaved->userId,
            'activationCode' => $newUserSaved->activationCode,
            'dateAdd'        => date('Y-m-d H:i:s')
        ];
        $resInsert = $this->_mapper->getDbTable()->insert($codeArray);

        if (empty($resInsert)) {
            $this->setError(Core_Messages_Message::getMessage('userActivationCodeIsNotSaved'));

            return false;
        }

        $this->_mapper->setDefaultDbTable();

        if ($isSocialNetRegistration) {
            $mailVars['login']    = $newUser->email;
            $mailVars['password'] = $newUser->password;

            $emailTemplate = 'sendLoginAndPassword';

            $user = Core_Model_User::getInstance();
            $user->populate($newUserSaved->toArray());
            $res = $this->_createUserStorage($user);
            if (!$res) {
                $this->setError(Core_Messages_Message::getMessage('cantCreateUserStorage'));

                return false;
            }
        } else {
            $link = $this->getConfigParam('baseHttpPath') . Zend_Registry::get('language');;
            $mailVars = [
                'link' => $link . '/activation/' . $newUser->activationCode,
            ];

            $emailTemplate = 'sendActivationCode';
        }

        $mailer = new Core_Mailer($emailTemplate, $mailVars, 1);
        $mailer->addTo($newUser->email);
        $mailer->send();

        $this->setMessage(__('successfulRegistration'));

        return $this->signIn($data);
    }

    public function getSignUpForm()
    {
        $signUpForm = $this->getFormLoader()->isExists(self::FORM_SIGN_UP)
            ? $this->getFormLoader()->getForm(self::FORM_SIGN_UP)
            : $this->getFormLoader()->addForm(self::FORM_SIGN_UP);

        $signUpForm->setAction('/' . Zend_Registry::get('language') . '/sign-in/');

        return $signUpForm;
    }

    public function signIn($data)
    {
        $redirUrl['url'] = '';
        $auth            = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            if ($this->authenticate($data) === true) {
                $user = Core_Model_User::getInstance();
                if (empty($user->defaultModule)) {
                    $this->setError(Core_Messages_Message::getMessage('defaultModuleNotAssigned'));

                    return false;
                }
                $frontConrtoller = Zend_Controller_Front::getInstance();
                $request         = $frontConrtoller->getRequest();
                $redir           = $request->getParam('redirect', 0);
                if ($redir) {
                    $redirUrl['url'] = $redir;

                    return $redirUrl;
                } else {
                    $redirUrl['url'] = '/';

                    return $redirUrl;
                }
            }
        } else {
            $redirUrl['url'] = '/';

            return $redirUrl;
        }
    }

    public function authenticate($data)
    {
        $user = Core_Model_User::getInstance();
        if ($this->getSignInForm()->isValid($data)) {
            $user->populate($this->getSignInForm()->getValues());
            $auth = $user->authenticate();
            if ($auth) {
                return $this->_createUserStorage($user);
            } else {
                $this->getSignInForm()->password->addError($user->getError());
                $this->_processFormError($this->getSignInForm());
                $this->setError(Core_Messages_Message::getMessage('wrongCredentials'));

                return false;
            }
        } else {
            $this->_processFormError($this->getSignInForm());

            return false;
        }
    }

    public function getSignInForm()
    {
        $signInForm = $this->getFormLoader()->isExists(self::FORM_SIGN_IN)
            ? $this->getFormLoader()->getForm(self::FORM_SIGN_IN)
            : $this->getFormLoader()->addForm(self::FORM_SIGN_IN);

        //$link = $this->getConfigParam('baseHttpPath') . Zend_Registry::get('language');
        $signInForm->setAction('/' . Zend_Registry::get('language') . '/sign-in/?signIn=1');

        return $signInForm;
    }

    /**
     * Recover password
     *
     * @param $email
     *
     * @return bool
     * @throws Zend_Form_Exception
     */
    public function recoveryPassword($email)
    {
        if (!$this->getRecoveryPasswordForm()->isValid($email)) {
            $this->setError(Core_Messages_Message::getMessage('wrongData'));

            return false;
        }

        $user = $this->_mapper->getUserByEmail($email['email']);

        if (!$user) {
            $this->setError(Core_Messages_Message::getMessage('wrongEmail'));

            return false;
        }

        $code     = mt_rand() . mt_rand() . mt_rand() . mt_rand() . mt_rand();
        $password = mt_rand();

        $res = $this->_mapper->addPasswordActivationCode($user, $code, $password);

        if (!$res) {
            $this->setError(Core_Messages_Message::getMessage('cantAddPasswordActivationCode'));

            return false;
        }
        $isSending = $this->sendPasswordActivationCode($user, $code, $password);

        if ($isSending) {
            $this->setMessage(Core_Messages_Message::getMessage('sendingRecoveryEmailSuccessfully'));

            return true;
        }

        return false;
    }

    /**
     *
     * Returns RecoveryPassword form
     *
     * @return RecoveryPasswordForm
     */
    public function getRecoveryPasswordForm()
    {
        if ($this->getFormLoader()->isExists('RecoveryPasswordForm')) {
            return $this->getFormLoader()->getForm('RecoveryPasswordForm');
        }

        $recoveryPasswordForm = $this->getFormLoader()->addForm('RecoveryPasswordForm');

        return $recoveryPasswordForm;
    }

    public function sendPasswordActivationCode($user, $code, $password)
    {
        $lang   = Zend_Registry::get('language');
        $config = Zend_Registry::get('appConfig');
        $params = [
            'fio'    => $user->name,
            'link'   => $config['baseHttpPath'] . $lang . '/default/index/password-activation/code/' . $code,
            'passwd' => $password
        ];
        $mail   = new Core_Mailer('passwordActivation', $params, true);
        $mail->addTo($user->email, $user->name);
        $mail->send();

        return true;
    }

    public function activatePassword($code)
    {
        $alnum = new Zend_Validate_Alnum();
        if ($alnum->isValid($code)) {
            $dbcode = $this->_mapper->getPasswordActivationCode($code);
            if ($dbcode) {
                $passwd = $this->_mapper->activatePassword($dbcode['codeId']);

                $user = new Core_Users_User();
                $user->populate(['password' => $passwd]);
                $user->setPasswordHash();

                $this->_mapper->setNewPassword($dbcode['userId'], $user->securityCode);

                $this->setMessage(Core_Messages_Message::getMessage('yourPasswordIsActivated'));

                return true;
            } else {
                $this->setError(Core_Messages_Message::getMessage('wrongPasswordActivationCode'));

                return false;
            }
        }

        return true;
    }

    /**
     * @param $link
     *
     * @return bool|Core_Model_CustomContent
     */
    public function getCustomContentBySlug($link)
    {
        return $this->_mapper->getCustomContentBySlug($link);
    }

    /**
     * @return bool|Core_Collection_Pages
     */
    public function getBlogPages()
    {
        return $this->_mapper->getBlogPages();
    }

    /**
     * @return bool|Core_Collection_Pages
     */
    public function getNewsPages()
    {
        return $this->_mapper->getNewsPages();
    }

    /**
     * @param bool $type
     *
     * @return bool|Core_Collection_CustomContent
     */
    public function getCustomContent($type = false)
    {
        return $this->_mapper->getCustomContent($type);
    }
}