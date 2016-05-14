<?php

/**
 *
 * System Message row class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 * @package    Core_Model
 *
 * @property int    $id
 * @property string $mail_code
 * @property string $mail_subject
 * @property string $mail_body
 */
class Core_Model_SystemMessage extends Core_Model_Super
{
    protected $_data
        = [
            'id'           => null,
            'mail_code'    => null,
            'mail_subject' => null,
            'mail_body'    => null
        ];

    public function replace(array $params)
    {
        foreach ($params as $var => $replacment) {
            $this->mail_body = preg_replace(
                '/{{' . $var . '}}/', $replacment, $this->mail_body
            );
        }
    }
}