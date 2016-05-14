<?php

/**
 *
 * Messenger helper class class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2015 Studio 105 (http://105.in.ua)
 */
class Core_View_Helper_Html_Notification extends Core_View_Helper_View
{
    const MESSAGE_TYPE_SUCCESS = 'success';
    const MESSAGE_TYPE_ERROR = 'alert';
    const MESSAGE_TEMPLATE = '<div data-alert="" class="alert-box %s">%s<a href="" class="close">×</a></div>';

    /**
     * @param Core_Service_Super $service
     * @param bool               $closeBtn
     *
     * @return string|bool
     */
    public function notification($service = null, $closeBtn = true)
    {
        empty($service) ? $service = $this->view->service : false;

        if (!$service->getError() && !$service->getMessage()) {
            return false;
        }

        $type        = $service->getError() ? self::MESSAGE_TYPE_ERROR : self::MESSAGE_TYPE_SUCCESS;
        $messageText = $service->getError() ? $service->getError() : $service->getMessage();

        $message = sprintf(self::MESSAGE_TEMPLATE, $type, __($messageText));

        if (count($this->view->messages) > 0) {
            $n = count($this->view->messages);
            for ($i = 0; $i < $n; $i++) {
                $message .= sprintf(self::MESSAGE_TEMPLATE, $type, $this->view->messages[$i]);
            }
        }

        return $message;
    }
}