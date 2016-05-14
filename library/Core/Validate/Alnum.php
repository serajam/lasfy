<?php

class Core_Validate_Alnum extends Zend_Validate_Alnum
{
    public function __construct($allowWhiteSpace = true)
    {
        $this->allowWhiteSpace = (boolean)$allowWhiteSpace;
    }
}
