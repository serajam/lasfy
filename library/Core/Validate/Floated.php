<?php

class Core_Validate_Floated extends Zend_Validate_Float
{
    public function isValid($value, $context = null)
    {

        $value = str_replace(',', '.', $value);
        if (!is_string($value) && !is_int($value) && !is_float($value)) {
            $this->_error(self::INVALID);

            return false;
        }

        if (preg_match('/^\d+(\.\d+)?$/', $value, $matches)) {
            $this->_setValue($value);

            return true;
        }
        $this->_setValue($value);
        $this->_error(self::NOT_FLOAT);

        return false;
    }
}
