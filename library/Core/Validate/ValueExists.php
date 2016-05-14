<?php

/**
 * Class
 *
 * @author     : Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_Validate_ValueExists extends Core_Validate_DbValidate
{
    const INVALID_DB_VALUE = 'valueDoesntExistsInDatabase';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates
        = [
            self::INVALID_DB_VALUE => "Value '%value%' doesnt exists in database"
        ];

    /**
     * @var array
     */
    protected $_messageVariables
        = [
            'value' => '_value'
        ];

    protected $_field;

    protected $_value;

    /**
     * Sets validator options
     *
     * @param  string|Zend_Config $options OPTIONAL
     *
     * @return void
     */
    public function __construct($table, $field)
    {
        $this->_dbName = $table;
        $this->_field  = $field;
        $this->init($table);
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if $value is a valid date of the format YYYY-MM-DD
     * If optional $format or $locale is set the date format is checked
     * according to Zend_Date, see Zend_Date::isDate()
     *
     * @param  string|array|Zend_Date $value
     *
     * @return boolean
     */
    public function isValid($value)
    {
        $isExists = $this->_dbTable
            ->valueExists($this->_dbName, $this->_field, $value);
        if (empty($isExists)) {
            $this->_value = $value;
            $this->_error(self::INVALID_DB_VALUE);

            return false;
        }

        return true;
    }
}
