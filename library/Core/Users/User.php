<?php

/**
 * User  domain class
 * Singleton class
 *
 * @author     Fedor Petryk
 * @package    Core_Model
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_Users_User extends Core_Model_Super
{
    const MANAGER_USER = 1;
    const SIMPLE_USER = 2;

    /**
     *
     * @see Core_Model_Super
     */
    protected $_data = [
        'userId'                => null,
        'login'                 => null,
        'email'                 => null,
        'userName'              => null,
        'language'              => 'ru',
        'securityCode'          => null,
        'type'                  => null,
        'password'              => null,
        'repeatPassword'        => null,
        'roleName'              => null,
        'roleId'                => null,
        'registrationDate'      => null,
        'lastLoginDate'         => null,
        'isActivated'           => 0,
        'isBanned'              => 0,
        'activationCode'        => null,
        'userProfileIdentifier' => null,
        'providerName'          => null,
        'agreement'             => 1,
    ];

    public function getName()
    {
        return $this->_data['userName'];
    }

    public function setupRegistration()
    {
        $config = Zend_Registry::get('appConfig');
        $this->setPasswordHash();
        $this->language = $config['lang'];
    }

    public function setPasswordHash()
    {
        // Base-2 logarithm of the iteration count used for password stretching
        $hashCostLog2 = 8;
        // Do we require the hashes to be portable to older systems (less secure)?
        $hashPortable       = false;
        $hasher             = new PasswordHash($hashCostLog2, $hashPortable);
        $this->securityCode = $hasher->HashPassword($this->password);

        return $this;
    }
}