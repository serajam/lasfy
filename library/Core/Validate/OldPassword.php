<?php

/**
 * Custom unique field validation
 * @author Fedor Petryk
 */
class Core_Validate_OldPassword extends Core_Validate_DbValidate
{
  const WRONG_OLD_PASSWORD = 'wrongOldPassword';

  protected $_form;

  /**
   * Message error
   *
   * @var String
   */
  protected $_messageTemplates
      = [
          self::WRONG_OLD_PASSWORD => 'wrongOldPassword',
      ];

  /**
   *
   * Field to check to
   *
   * @var String
   */
  protected $_field;

  /**
   * @var Core_DbTable_Validation
   */
  protected $_table;

  /**
   * Id of the table to check if object exists againts other rows
   */
  protected $_primary = null;

  /**
   *
   * The validator element constrictur
   *
   * @param table name String | $table
   * @param field name String | $field
   * @param Form  view $form
   */
  public function __construct($table, $field, $primary = null, $form = null)
  {
    $this->init($table);
    $this->_field   = $field;
    $this->_table   = $table;
    $this->_primary = $primary;

    if ($form) {
      $this->_form = $form;
    }
  }

  /**
   * (non-PHPdoc)
   *
   * @see Zend/Validate/Zend_Validate_Interface::isValid()
   */
  public function isValid($value, $context = null)
  {
    $rid = null;
    $this->_setValue($value);

    $user           = new Core_Users_User();
    $user->password = $value;
    $user->setPasswordHash();

    $oldPassword = $user->securityCode;
    $isValid     = $this->_dbTable->validateOldPassword(
        Core_Model_User::getInstance()->userId,
        $oldPassword
    );

    if ($isValid == true) {
      return true;
    }

    $this->_error(self::WRONG_OLD_PASSWORD);

    return false;
  }
}
