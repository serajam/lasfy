<?php

/**
 *
 * Custom unique field validation
 *
 * @author Fedor Petry
 *
 */
class Core_Validate_Unique extends Zend_Validate_Abstract
{
    const VALUE_EXISTS = 'mailExists';
    /**
     *
     * Message error
     *
     * @var String
     */
    /**
     * Message error
     *
     * @var String
     */
    protected $_messageTemplates
        = [
            self::VALUE_EXISTS => 'mailExists',
        ];

    protected $_form;

    /**
     *
     * Field to check to
     *
     * @var String
     */
    protected $_field;

    /**
     *
     * Table to look where
     *
     * @var String
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
        $this->_field   = $field;
        $this->_table   = $table;
        $this->_primary = $primary;

        if ($form) {
            $this->_form = $form;
        }

        $this->_messageTemplates = [
            self::VALUE_EXISTS => Zend_Registry::get('translation')->get('valueExists')
        ];
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
        $primary = $this->_primary;

        if ($this->_form != null) {
            $rid = $this->_form->$primary->getValue();
        }
        $service = new Core_Service_Super();

        $unique = $service->getMapper()->checkUnique(
            $this->_table,
            $this->_field,
            $value,
            $rid,
            $this->_primary
        );

        if ($unique == true) {
            return true;
        }

        $this->_error(self::VALUE_EXISTS);

        return false;
    }
}
