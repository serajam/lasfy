<?php

/**
 *
 * Plan row class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_Model_EmailTemplate extends Core_Model_SystemMessage
{
    protected $_data
        = [
            'id'            => null,
            'user_id'       => null,
            'user_type'     => null,
            'template_id'   => null,
            'template_data' => null,
            'sended'        => null,
            'email'         => null,
            'mail_code'     => null
        ];

    public function getParams()
    {
        $params    = explode(';;', $this->template_data);
        $paramsArr = [];
        foreach ($params as $p) {
            list($paramName, $value) = explode('==', $p);
            $paramsArr[trim($paramName)] = trim($value);
        }

        return $paramsArr;
    }
}