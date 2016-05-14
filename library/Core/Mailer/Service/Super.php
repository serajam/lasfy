<?php

/**
 *  Super Service Class
 *
 * @author     Alexey Kagarlykskiy
 * @package    Core_Mailer_Service
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_Mailer_Service_Super extends Core_Service_Super
{
    /**
     * @var Core_Mailer_Mapper_Super
     */
    protected $_mapper;

    /**
     * Mapper class name
     *
     * @var string
     */
    protected $_mapperName = 'Core_Mailer_Mapper_Super';

    /**
     * @var Core_Model_Super
     */
    protected $_model;

    /**
     * Хранилище переменных для замены
     *
     * @var array
     */
    protected $_variables = [];

    public function createMessage($parameters)
    {
        $func = '_' . $parameters['function'];
        $this->$func($parameters);
    }

    protected function _create($template = null, $vars = null, $recipientUser, $senderUser = null)
    {
        if (!empty($template) && !empty($vars) && !empty($recipientUser)) {

            $this->_variables = $vars;

            $letter = Core_DbTable_Mail::getMail($template);
            if (empty($letter)) {
                throw new Exception(Core_Messages_Message::getMessage('no_template_for_email'));

                return;
            }

            $message = $this->_replace($letter ['mailBody']);

            $this->_putInDB($message, $template, $recipientUser, $senderUser);
        } else {
            throw new Exception('Error: no params');
        }
    }

    /**
     * Заменяет все переменные найденные в тексте
     * $replacment на их значения
     *
     * @param string $replacment
     *
     * @return string
     */
    protected function _replace($text)
    {
        foreach ($this->_variables as $var => $replacment) {
            $text = preg_replace('/{{' . $var . '}}/', $replacment, $text);
        }

        return $text;
    }

    protected function _putInDB($message, $templateCode, $recipientUser, $senderUser)
    {
        $senderId = $senderUser;

        if (!empty($senderUser)) {
            $senderId = $senderUser->userId;
        }

        $data = [
            'recipientUserId' => $recipientUser->userId,
            'senderUserId'    => $senderId,
            'message'         => $message,
            'isNew'           => 1,
            'isSended'        => 0,
            'email'           => $recipientUser->email,
            'templateCode'    => $templateCode,
            'dateAdd'         => Zend_Date::now()->toString('yyyy-MM-dd H:m:s')
        ];

        $this->_mapper->addMailer($data);

        return true;
    }
// functions for getting parameters for each e-mail to store in Mailer
}