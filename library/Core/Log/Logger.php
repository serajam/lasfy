<?php

/**
 *
 * The access event logger class
 *
 * @author     Fedor Petryk
 * @copyright
 */
class Core_Log_Logger
{
  protected static $_logTable = 'Logs';

  /**
   *
   * Events messages types
   *
   * @var array
   */
  protected static $_eventsTypes
      = [
          0 => 'Page open',
          1 => 'Access denied',
          2 => 'Data send',
          3 => 'System login',
          4 => 'No page'
      ];

  /**
   * @param $table
   * @param $data
   *
   * @throws Exception
   */
  public static function logIt($table, $data)
  {
    if (!empty($data)) {
      self::setLogTable($table);
      self::insertEvent($data);
    }
  }

  /**
   * @param array $data
   *
   * @throws Exception
   * @throws Zend_Exception
   */
  protected static function insertEvent(array $data)
  {
    if (!self::$_logTable) {
      throw new Exception();
    }
    $date              = new Zend_Date();
    $data['eventDate'] = $date->toString('yyyy-MM-dd H:m:s');

    $db = Zend_Registry::get('DB');
    $db->insert(self::$_logTable, $data);
  }

  /**
   * @param Zend_Controller_Request_Abstract $request
   * @param int                              $type
   */
  public static function logAccessEvent(
      Zend_Controller_Request_Abstract $request,
      $type = 0
  )
  {
    $data       = [];
    $auth       = Zend_Auth::getInstance()->hasIdentity();
    $ip         = $_SERVER['REMOTE_ADDR'];
    $action     = $request->getActionName();
    $module     = $request->getModuleName();
    $controller = $request->getControllerName();
    $aclr       = Core_Controller_Plugin_AclNormalizer::fullAclNormalize(
        Zend_Registry::get('fullAcl')
    );
    $act        = '';

    if ($auth) {
      $user = Core_Model_User::getInstance();

      if (array_key_exists($module, $aclr['modules'])) {
        if (array_key_exists(
            $module . ':' . $controller,
            $aclr['modules'][$module]['resources']
        )
        ) {
          if (array_key_exists(
              $action,
              $aclr['modules'][$module]
              ['resources'][$module . ':' . $controller]
              ['actions']
          )
          ) {
            $act = ($aclr['modules'][$module]
                    ['resources'][$module . ':' . $controller]
                    ['actions'][$action]);
          } else {
            $act = 'Неизвестное действие';
          }
        } else {
          $act = 'Неизвестный контроллер';
        }
      } else {
        $act = 'Неизвестный модуль';
      }

      $userLog = $user->fullname . '('
          . $user->userId . ', '
          . $ip . ') - '
          . $act . ' ';

      $data = [
          'userId' => $user->userId
      ];
    } else {
      $userLog = 'Анонимный Пользователь (' . $ip . ')';
      if (array_key_exists($module, $aclr['modules'])) {
        if (array_key_exists(
            $module . ':' . $controller,
            $aclr['modules'][$module]['resources']
        )
        ) {
          if (array_key_exists(
              $action,
              $aclr['modules'][$module]
              ['resources'][$module . ':' . $controller]
              ['actions']
          )
          ) {
            $act = ($aclr['modules'][$module]
                    ['resources'][$module . ':' . $controller]
                    ['actions'][$action]);
          } else {
            $act = 'Неизвестное действие';
          }
        } else {
          $act = 'Неизвестный контроллер';
        }
      } else {
        $act = 'Неизвестный модуль';
      }
    }

    $data += [
        'accessPath' => $module . '/' . $controller . '/' . $action,
        'ip'         => $ip,
        'actionDesc' => $act,
        'message'    => self::$_eventsTypes[$type],
    ];

    self::insertEvent($data);
  }

  /**
   * @param      $errors
   * @param null $auth
   *
   * @throws Exception
   */
  public static function logErrorEvent($errors, $auth = null)
  {
    $data      = [];
    $request   = $errors->request;
    $exception = $errors->exception;
    $auth      = Zend_Auth::getInstance()->hasIdentity();
    $ip        = $_SERVER['REMOTE_ADDR'];

    $action     = $request->getActionName();
    $module     = $request->getModuleName();
    $controller = $request->getControllerName();

    if ($auth) {
      $user = Core_Model_User::getInstance();

      $userLog = __('user') . ' ('
          . $user->name . '('
          . $user->userId . '),' . $ip . ')';

      $data = [
          'userId' => $user->userId
      ];
    } else {
      $userLog = __('anonimUser') . ' (' . $ip . ')';
    }

    $data = $data + [
            'actionDesc' => $exception->getMessage(),
            'ip'         => $ip,
            'message'    => $exception->getTraceAsString(),
            'accessPath' => $module . '/' . $controller . '/' . $action,
        ];

    // Logging error into file

    if (defined('APPLICATION_PUB')) {
      $logPath = APPLICATION_PUB . '/log/error.log';
    } else {
      $logPath = APPLICATION_PATH . '/log/error.log';
    }
    if (!file_exists($logPath)) {
      file_put_contents($logPath, '');
    }
    $log = new Zend_Log (new Zend_Log_Writer_Stream ($logPath));
    $log->debug($userLog . ' ' . $exception->getMessage() . "\n" . $exception->getTraceAsString());
    self::insertEvent($data);
  }

  public static function getLogTable()
  {
    return self::$_logTable;
  }

  public static function setLogTable($logTable)
  {
    self::$_logTable = $logTable;
  }
}