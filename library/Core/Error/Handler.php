<?php

/**
 * Error Handler
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 *
 * Based on error type performs error handling
 * Checks env type
 * Uses default logist design template
 */
class Core_Error_Handler
{
    protected static $_body
        = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
                    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><!--[if IE 8]>
            <html class="no-js lt-ie9" lang="en"> <![endif]-->
            <!--[if gt IE 8]><!-->
            <html class="no-js" lang="en"> <!--<![endif]-->
            <head>
                <meta name="viewport" content="width=device-width"/>
                <meta http-equiv="Con\tent-Type" content="text/html;charset=utf-8"/>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
                <title>index_admin_pages</title>
                <link href="http://miracole.oleg/foundation/normalize.css" media="screen" rel="stylesheet" type="text/css"/>
                <link href="http://miracole.oleg/css/basic.css" media="screen" rel="stylesheet" type="text/css"/>
                <link href="http://miracole.oleg/foundation/foundation.min.css" media="screen" rel="stylesheet" type="text/css"/>
                <link href="http://miracole.oleg/foundation/icons/stylesheets/general_enclosed_foundicons.css" media="screen"
                      rel="stylesheet" type="text/css"/>
                <link rel="icon" href="/favicon.ico" type="image/x-icon"/>
                <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
            </head>
            <body>
            <div class="row">
                <div class="row"><b>%s</b></div>
                <footer class="row">
                    <div class="large-12 columns">
                        <hr/>
                        <div class="row">
                            <div class="large-6 columns">
                                <p>&copy; Copyright &copy; 2010-2014. Miracole</p>
                            </div>
                            <div class="large-6 columns">
                                <ul class="inline-list right">
                                    <li><a href="/">Home</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
            </body>
            </html>';

    public static function handle($errno, $errstr, $errfile, $errline)
    {
        $front = Zend_Controller_Front::getInstance();
        if (error_reporting() & $errno) {
            $request = $front->getRequest();
            if (!empty($request) && !$request->isXmlHttpRequest()) {
                if (APPLICATION_ENV == 'development') {
                    $text = "<b>Error: " . $errstr . " in $errfile:$errline error-" . $errno . "\n</b>";
                } else {
                    $text
                        = "<b>Sorry, the system is temporally unavailable.</b> Please try again later or contact system administrator";
                }
                ob_clean();
                echo sprintf(self::$_body, $text);
                exit;
            } else {
                if (!Core_Model_Settings::settings('showAjaxErrors')) {
                    $response = [
                        'error'         => 'true',
                        'error_message' => __('system_error'),
                    ];
                } else {
                    $response = [
                        'error'         => 'true',
                        'error_message' => $errstr . " in $errfile:$errline error-" . $errno,
                    ];
                }
                if ($front->getResponse()) {
                    $front->getResponse()
                        ->setHeader("Cache-Control", "no-cache, must-revalidate")
                        ->setHeader("Pragma", "no-cache")
                        ->setHeader("Content-type", "application/json;charset=utf-8")
                        ->setBody(Zend_Json_Encoder::encode($response))
                        ->setHttpResponseCode(200)
                        ->sendResponse();
                } else {
                    echo sprintf(self::$_body, $errstr . " in $errfile:$errline error-" . $errno);
                }
                exit;
            }
        }
    }

    public static function exceptionHandler($exception)
    {
        if (APPLICATION_ENV == 'development') {
            $text = "Error: " . $exception->getMessage() . "\n";
            $text .= '<b>' . $exception->getTraceAsString() . '</b>';
        } else {
            $text
                = "<b>Sorry, the system is temporally unavailable.</b> Please try again later or contact system administrator";
        }
        ob_clean();
        echo sprintf(self::$_body, $text);
    }

    public static function set()
    {
        set_error_handler([__CLASS__, 'handle']);
        set_exception_handler([__CLASS__, 'exceptionHandler']);
    }
}

