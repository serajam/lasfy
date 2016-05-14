<?php

/** Zend_Controller_Plugin_Abstract */
require_once 'Zend/Controller/Plugin/Abstract.php';

/** Zend_Auth */
require_once 'Zend/Auth.php';

/**
 * Front Controller Plugin
 *
 * @uses       Zend_Controller_Plugin_Abstract
 * @subpackage Plugins
 */
class Core_Controller_Plugin_Authentication extends Zend_Controller_Plugin_Abstract
{
  /**
   * Base url for redirection
   * Default to admin panel
   * @var
   */
  protected $baseUrl = '/login/?redirect=';

  /**
   * IN Pre Dispatch
   * Checks if the user authenticated
   *
   * @return void
   * */
  public function preDispatch(Zend_Controller_Request_Abstract $request)
  {
    $conf = Zend_Registry::get('appConfig');
    // if no auth - redirect to login page
    if (!Zend_Auth::getInstance()->hasIdentity()) {
      if ($request->isXmlHttpRequest()) {
        $this->_ajaxSessionEndHandler();
      } else {
        $redir = substr($conf['baseHttpPath'], 0, strlen($conf['baseHttpPath']) - 1);
        $redir .= $request->get('REQUEST_URI');
        $redir = urlencode($redir);
        header('Location: ' . $conf['baseHttpPath'] . $this->getBaseUrl() . $redir);
        exit;
      }
    }
  }

  protected function _ajaxSessionEndHandler()
  {
    $conf      = Zend_Registry::get('appConfig');
    $loginLink = '<a target=_blank href="' . $conf['baseHttpPath'] . '">'
        . Zend_Registry::get('translation')->get('enter') . '</a>';

    $response = [
        'error_message' => Core_Messages_Message::getMessage('session_expire') . '<br />' . $loginLink,
        'error'         => 'true'
    ];
    $this->_response
        ->setHeader("Cache-Control", "no-cache, must-revalidate")
        ->setHeader("Pragma", "no-cache")
        ->setHeader("Content-type", "application/json;charset=utf-8")
        ->setBody(Zend_Json_Encoder::encode($response));
    $this->_response->sendResponse();
    exit;
  }

  /**
   * @return mixed
   */
  public function getBaseUrl()
  {
    return $this->baseUrl;
  }

  /**
   * @param mixed $baseUrl
   */
  public function setBaseUrl($baseUrl)
  {
    $this->baseUrl = $baseUrl;
  }
}
