<?php
/**
 * @author     Petryk Fedor
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */

require_once 'Zend/Validate/Abstract.php';

/**
 * @uses       ZExt_Validate_IdenticalField
 * @package    ZExt_Validate
 * @author     Sean P. O. MacCath-Moran
 * @email      zendcode@emanaton.com
 * @website    http://www.emanaton.com
 * @copyright  This work is licenced under a Attribution Non-commercial Share Alike Creative Commons licence
 * @license    http://creativecommons.org/licenses/by-nc-sa/3.0/us/
 */
class Core_Validate_FieldGreater extends Zend_Validate_Abstract
{
    const GREATER_THEN = 'greaterThan';
    const MISSING_FIELD_NAME = 'missingFieldName';
    const INVALID_FIELD_NAME = 'invalidFieldName';

    /**
     * @var array
     */
    protected $_messageTemplates
        = [
            self::MISSING_FIELD_NAME =>
                'DEVELOPMENT ERROR: Field name to match against was not provided.',
            self::INVALID_FIELD_NAME =>
                'DEVELOPMENT ERROR: The field "%fieldName%" was not provided to match against.',
            self::GREATER_THEN       =>
                'Поле должно быть больше чем %fieldValue%'
        ];

    /**
     * @var array
     */
    protected $_messageVariables
        = [
            'fieldName'  => '_fieldName',
            'fieldTitle' => '_fieldTitle',
            'fieldValue' => '_fieldValue',
        ];

    /**
     * Name of the field as it appear in the $context array.
     *
     * @var string
     */
    protected $_fieldName;

    /**
     * Title of the field to display in an error message.
     *
     * If evaluates to false then will be set to $this->_fieldName.
     *
     * @var string
     */
    protected $_fieldTitle;

    /**
     *
     * @var unknown_type
     */
    protected $_fieldValue;

    /**
     * @var Core_Form
     */
    protected $_form;

    protected $_validationType;

    /**
     * Sets validator options
     *
     * @param  string $fieldName
     * @param  string $fieldTitle
     *
     * @return void
     */
    public function __construct($fieldName, $form, $validationType = 'string', $fieldTitle = null)
    {
        $this->setFieldName($fieldName);
        $this->setFieldTitle($fieldTitle);
        $this->setValidationType($validationType);

        $this->_form = $form;

        $this->_messageTemplates = [
            self::MISSING_FIELD_NAME =>
                'DEVELOPMENT ERROR: Field name to match against was not provided.',
            self::INVALID_FIELD_NAME =>
                'DEVELOPMENT ERROR: The field "%fieldName%" was not provided to match against.',
            self::GREATER_THEN       =>
                Zend_Registry::get('translation')->get('field_greater_than')
        ];
    }

    /**
     * Returns the field title.
     *
     * @return integer
     */
    public function getFieldTitle()
    {
        return $this->_fieldTitle;
    }

    /**
     * Sets the field title.
     *
     * @param  string :null $fieldTitle
     *
     * @return Zend_Validate_IdenticalField Provides a fluent interface
     */
    public function setFieldTitle($fieldTitle = null)
    {
        $this->_fieldTitle = $fieldTitle ? $fieldTitle : $this->_fieldName;

        return $this;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if a field name has been set, the field name is available in the
     * context, and the value of that field name matches the provided value.
     *
     * @param  string $value
     *
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_setValue($value);
        $field             = $this->getFieldName();
        $this->_fieldValue = $this->_form->$field->getValue();

        if (empty($field)) {
            $this->_error(self::MISSING_FIELD_NAME);

            return false;
        } elseif (!isset($this->_fieldValue)) {
            $this->_error(self::INVALID_FIELD_NAME);

            return false;
        } else {
            // performing validation by type
            if ($this->getValidationType() == 'datetime') {
                if ($this->_validateDateTime($value)) {
                    return true;
                }
            } elseif ($this->getValidationType() == 'date') {
                if ($this->_validateDate($value)) {
                    return true;
                }
            } else {
                if ($this->_validateString($value)) {
                    return true;
                }
            }
        }

        $this->_error(self::GREATER_THEN);

        return false;
    }

    /**
     * Returns the field name.
     *
     * @return string
     */
    public function getFieldName()
    {
        return $this->_fieldName;
    }

    /**
     * Sets the field name.
     *
     * @param  string $fieldName
     *
     * @return Zend_Validate_IdenticalField Provides a fluent interface
     */
    public function setFieldName($fieldName)
    {
        $this->_fieldName = $fieldName;

        return $this;
    }

    public function getValidationType()
    {
        return $this->_validationType;
    }

    public function setValidationType($validationType)
    {
        $this->_validationType = $validationType;
    }

    protected function _validateDateTime($value)
    {
        $validationDate = new Zend_Date($value, 'yyyy-MM-dd H:m');
        $againstDate    = new Zend_Date($this->_fieldValue, 'yyyy-MM-dd H:m');
        $dateCompare    = $validationDate->isLater($againstDate, 'yyyy-MM-dd H:m');
        if ($dateCompare != 1) {
            return false;
        }

        return true;
    }

    protected function _validateDate($value)
    {
        $validationDate = new Zend_Date($value, 'yyyy-MM-dd');
        $againstDate    = new Zend_Date($this->_fieldValue, 'yyyy-MM-dd');
        $dateCompare    = $validationDate->isLater($againstDate, 'yyyy-MM-dd');
        if ($dateCompare != 1) {
            return false;
        }

        return true;
    }

    protected function _validateString($value)
    {
        if ($value >= $this->_fieldValue) {
            return true;
        }

        return false;
    }
}
