<?php

/**
 *
 * Plan row class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 *
 * @property int    $mailId
 * @property string $mailCode
 * @property string $mailSubject
 * @property string $mailBody
 * @property string $lang
 */
class Core_Mailer_EmailTemplate extends Core_Model_Super
{
    protected $_data
        = [
            'mailId'      => null,
            'mailCode'    => null,
            'mailSubject' => null,
            'mailBody'    => null,
            'lang'        => null,
        ];

    public function replace(array $params)
    {
        foreach ($params as $var => $replacment) {
            $this->mailBody = preg_replace(
                '/{{' . $var . '}}/',
                $replacment,
                $this->mailBody
            );
        }
    }

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