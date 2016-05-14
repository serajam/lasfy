<?php

/**
 * Application start controller
 *
 * @author      Fedor Petryk
 * @copyright   Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_Controller_Super extends Zend_Controller_Action
{
  protected $_events;

  /**
   * Default service class name
   *
   * @var String
   */
  protected $_defaultServiceName;

  /**
   * The service layer object, generaly used in child controllers
   *
   * @var Core_Service_Super
   */
  protected $_service;

  /**
   * if true
   * Enables returning html response to ajax request
   * and disables service respinse
   *
   * @var bool
   */
  protected $_ajaxViewEnabled = false;

  /**
   * Initialize via DI config
   *
   * @var bool
   */
  protected $_diInit = false;

  /**
   * Use service impl structure
   * for several service usage in one controller
   *
   * @var string
   */
  protected $_serviceMediatorStructure = false;

  protected $_htmlContainer = 'default-list';

  protected $_lang = 'ru';

  public function init()
  {
    if ($this->_diInit != false) {
      $config              = Core_Model_Settings::getInstance()->getModuleDiSet($this->_diInit);
      $this->_service      = Core_Service_Factory::setup(
          $this->_diInit, $config, $this->_serviceMediatorStructure
      );
      $this->view->service = $this->_service;
    } else {
      if ($this->_defaultServiceName != null) {
        $this->_service      = new $this->_defaultServiceName;
        $this->view->service = $this->_service;
      } else {
        throw new Exception(Core_Messages_Message::getMessage(100));
      }
    }
    $this->view->container  = $this->_htmlContainer;
    $this->view->controller = $this->_request->getControllerName();
    $this->view->action     = $this->_request->getActionName();

    if ($this->_events === null) {
      $this->_events = new Zend_EventManager_EventManager(__CLASS__);
      $this->initEvents();
    }

    // init home page to setup title everywhere
    $settings = Core_Settings_Settings::getGroupSettings('site_settings');
    if (!empty($settings)) {
      $this->view->title       = __(arr_val($settings, 'meta_title'));
      $this->view->keywords    = __(arr_val($settings, 'meta_keywords'));
      $this->view->description = __(arr_val($settings, 'meta_description'));
    }

    $this->_lang = Zend_Registry::get('language');
  }

  public function initEvents()
  {
  }

  public function postDispatch()
  {
    if ($this->getRequest()->isXmlHttpRequest()) {
      $this->_helper->layout->disableLayout();
      if ($this->_ajaxViewEnabled == false) {
        $this->_helper->viewRenderer->setNoRender();
        $this->serviceResponse();
      }
    }
  }

  /**
   * function for returning json response via ajax with specific structure
   */
  protected function serviceResponse()
  {
    if ($this->_serviceMediatorStructure) {
      $this->_service->pullServiceMessages();
    }

    if ($this->_service->getError()) {
      $response = [
          'error_message' => $this->_service->getError(),
          'error'         => 'true'
      ];
    } else {
      $response = [
          'error'   => 'false',
          'message' => $this->_service->getMessage()
      ];
    }

    if ($this->_service->getFormMessages()) {
      $response['formMessages'] = $this->_service->getFormMessages();
    }

    $jsonData = $this->_service->getJsonData();
    if ($jsonData == true && !empty($jsonData)) {
      $response['data'] = $jsonData;
    }
    $data = Zend_Json::encode($response);

    // DO NOT CHANGE THIS. DO THIS ONLY LOCALLY
    $this->ajaxResponse($data, 'text/html');
  }

  /**
   * @param        $data
   * @param string $type
   */
  protected function ajaxResponse($data, $type = 'text/html')
  {
    $this->getResponse()
        ->setHeader("Cache-Control", "no-cache, must-revalidate")
        ->setHeader("Pragma", "no-cache")
        ->setHeader("Content-type", $type . ";charset=utf-8")
        ->setBody($data);
  }

  public function doDeny($redirect = null)
  {
    $this->_deny($redirect);
  }

  protected function _deny($redirect = null)
  {
    if ($this->getRequest()->isXmlHttpRequest()) {
      $this->_service->setError(Core_Messages_Message::getMessage('error'));
      $this->serviceResponse();
    } else {
      if ($redirect != null) {
        $this->_response->setRedirect($redirect);
      } else {
        $this->_response->setRedirect('/');
      }
    }
  }

  /**
   * @return \Core_Service_Super
   */
  public function getService()
  {
    return $this->_service;
  }

  /**
   * Redirect to another URL
   *
   * Proxies to {@link Zend_Controller_Action_Helper_Redirector::gotoUrl()}.
   *
   * @param string $url
   * @param array  $options Options to be used when redirecting
   *
   * @return void
   */
  protected function _redirect($url, array $options = [])
  {
    $conf = Zend_Registry::get('appConfig');
    if (substr($url, 0, 1) == '/') {
      $url = $conf['baseHttpPath'] . substr($url, 1, strlen($url));
    }
    $this->_helper->redirector->gotoUrl($url, $options);
  }

  protected function _ajaxHelper()
  {
    if ($this->getRequest()->isXmlHttpRequest()) {
      $this->_ajaxViewEnabled = true;
      $this->getResponse()
          ->setHeader("Cache-Control", "no-cache, must-revalidate")
          ->setHeader("Pragma", "no-cache")
          ->setHeader("Content-type", 'text/html' . ";charset=utf-8");
    }
  }

  protected function _error()
  {
    $this->getResponse()->setHttpResponseCode(404);
  }

  protected function _ajaxPostHelper()
  {
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();
    $this->serviceResponse('text/html');
  }

  protected function _setMeta($page)
  {
    $page->metaTitle ? $this->view->title = $page->metaTitle : false;
    $page->metaKeywords ? $this->view->keywords = $page->metaKeywords : false;
    $page->metaDescription ? $this->view->description = $page->metaDescription : false;
    $this->view->pageTitle = $page->title;
  }

  protected final function noPage()
  {
    $this->forward('no-page', 'error', 'default');
  }
}
