<?php

/**
 * User administration controller
 *
 * @author     Petryk Fedor
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Admin_AuthController extends Core_Controller_Start
{
    /**
     * Default service class name for current controller
     *
     * @var String
     */
    protected $_defaultServiceName = 'UsersService';

    /**
     * The service layer object
     *
     * @var UsersService
     */
    protected $_service;

    public function loginAction()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            if ($this->_request->isPost()) {
                $data = $this->_request->getPost();
                if ($this->_service->authenticate($data) === true) {
                    $user = Core_Model_User::getInstance();
                    if (empty($user->defaultModule)) {
                        throw new Exception(Core_Messages_Message::getMessage(203));
                    }
                    $redir = $this->_request->getParam('redirect', 0);
                    if ($redir) {
                        $this->_redirect($redir);
                    } else {
                        $this->_redirect(
                            $user->defaultModule . '/' .
                            $user->defaultController . '/' .
                            $user->defaultAction
                        );
                    }
                }
            }
            $this->view->service = $this->_service;
        } else {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
            $user = Core_Model_User::getInstance();
            $this->_redirect(
                $user->defaultModule . '/' .
                $user->defaultController . '/' .
                $user->defaultAction
            );
        }
    }

    /**
     * Clearing user session
     **/
    public function logoutAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::expireSessionCookie();

        $socialConf = APPLICATION_PATH . '/../library/hybridauth/config.php';

        require_once(APPLICATION_PATH . '/../library/hybridauth/config.php');

        // initialize Hybrid_Auth with a given file
        $hybridauth = new Hybrid_Auth($socialConf);
        $hybridauth->logoutAllProviders();

        $this->_helper->FlashMessenger->clearCurrentMessages();
        $this->_redirect('/');
    }
}
