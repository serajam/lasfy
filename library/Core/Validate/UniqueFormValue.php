<?php

/**
 *
 * Custom unique field validation against non current
 *
 * @author Fedor Petry
 *
 */
class Core_Validate_UniqueFormValue extends Zend_Validate_Abstract
{
    const FORM_VALUE_EXISTS = 'valueExistsInForm';

    protected $_form;

    /**
     *
     * Field to check to
     *
     * @var String
     */
    protected $_field;

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates
        = [
            self::FORM_VALUE_EXISTS => 'The value exists in form'
        ];

    /**
     *
     * The validator element constrictur
     *
     * @param table name String | $table
     * @param field name String | $field
     * @param Form  view $form
     */
    public function __construct($field, $form)
    {
        $this->_field = $field;
        $this->_form  = $form;
    }

    /**
     * (non-PHPdoc)
     *
     * @see Zend/Validate/Zend_Validate_Interface::isValid()
     */
    public function isValid($value, $context = null)
    {
        $this->_setValue($value);
        $elements = $this->_form->getElements();

        foreach ($elements as $el) {
            if ($el->getName() == $this->_form->getElement($this->_field)->getName()) {
                continue;
            }

            if ($el->getValue() == $this->_form->getElement($this->_field)->getValue()) {
                $this->_error(self::FORM_VALUE_EXISTS);

                return false;
            }
        }

        return true;
    }
}
