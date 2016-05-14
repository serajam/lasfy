<?php

/**
 * Base user domain class
 * Singleton class
 *
 * @author     Fedor Petryk
 * @package    Core_Model
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 *
 *
 */
class Core_Model_User extends Core_Model_Super implements Zend_Auth_Adapter_Interface
{
  /**
   * The instance of class
   *
   * @var Core_Model_User
   */
  protected static $_instance = null;

  /**
   * @see Core_Model_Super
   */
  protected $_data = [
      'userId'                => null,
      'login'                 => null,
      'password'              => null,
      'email'                 => null,
      'userName'              => null,
      'telephones'            => null,
      'role'                  => null,
      'roleName'              => null,
      'language'              => null,
      'defaultModule'         => null,
      'defaultController'     => null,
      'defaultAction'         => null,
      'lastLoginDate'         => null,
      'timeout'               => 0,
      'messages'              => null,
      'userProfileIdentifier' => null,
      'providerName'          => null,
      'agreement'             => null,
  ];

  /**
   * Singleton cant have constructor
   */
  public function __construct()
  {
  }

  public static function isUserAuthenticated()
  {
    return (bool)self::getInstance()->userId;
  }

  /**
   * Returns an inctance of this class
   *
   * @return Core_Model_User
   */
  public static function getInstance()
  {
    if (null === self::$_instance) {
      self::$_instance = new self();
    }

    return self::$_instance;
  }

  /**
   * Singleton cant be cloned
   */
  public function __clone()
  {
  }

  /**
   * Singleton cant be wakeUp
   */
  public function __wakeUp()
  {
  }

  /**
   *
   * The authentication of the user
   *
   * @uses Zend_Auth_Adapter_DbTable
   * @see  library/Zend/Auth/Adapter/Zend_Auth_Adapter_Interface::authenticate()
   */
  public function authenticate()
  {
    $username = $this->login;
    $password = $this->password;
    $db       = Zend_Registry::get('DB');

    // getting password hash from db for entered user login
    $sql     = 'SELECT securityCode, login FROM Users WHERE login = :login OR email = :login';
    $stmt    = new Zend_Db_Statement_Pdo($db, $sql);
    $isValid = $stmt->execute([':login' => $username]);
    if (!$isValid) {
      $this->setError(__('error_sing_in'));

      return false;
    }
    $hash = $stmt->fetch();
    // Base-2 logarithm of the iteration count used for password stretching
    $hashCostLog2 = 8;
    // Do we require the hashes to be portable to older systems (less secure)?
    $hashPortable = false;
    $hasher       = new   PasswordHash($hashCostLog2, $hashPortable);
    // checking entered password to be valid against stored hash
    if (!$hasher->CheckPassword($password, $hash['securityCode'])) {
      $this->setError(__('error_sing_in'));

      return false;
    }
    $authAdapter = new Zend_Auth_Adapter_DbTable($db, 'Users', 'login', 'securityCode');
    // Set the input credential values to authenticate against
    $authAdapter->setIdentity($hash['login'])
        ->setCredential($hash['securityCode']);
    // do the authentication
    $auth   = Zend_Auth::getInstance();
    $result = $auth->authenticate($authAdapter);

    if ($result->isValid()) {
      $data = $authAdapter->getResultRowObject(null, 'securityCode');
      $this->populate($data);

      return $this;
    } else {
      if (!$result->isValid()) {
        $this->_error = Core_Messages_Message::getMessage(__('wrong_credentials'));

        return false;
      } else {
        $this->_error = Core_Messages_Message::getMessage(201);

        return false;
      }
    }
  }

  public function clearIdentity()
  {
    $auth = Zend_Auth::getInstance();
    $auth->clearIdentity();
  }

  /**
   *
   * Clears  all user data
   */
  public function clear()
  {
    foreach ($this->_data as $key => $value) {
      $this->$key = null;
    }
  }

  public function clearMessagesCount()
  {
    $this->_data['messages'] = 0;
    $this->createUserStorage();
  }

  /**
   *
   * Creates persistent user storage
   *
   * @uses Zend_Auth
   */
  public function createUserStorage()
  {
    Zend_Auth::getInstance()->getStorage()->write($this->toArray());
  }
}