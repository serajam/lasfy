<?php

class Core_Validate_Alpha extends Zend_Validate_Alpha
{
    public function __construct($allowWhiteSpace = true)
    {
        $this->allowWhiteSpace = (boolean)$allowWhiteSpace;
    }
}
