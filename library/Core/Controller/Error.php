<?php

/**
 * Default ErrorController
 *
 * @author      Fedor Petryk
 * @copyright
 */
class Core_Controller_Error extends Core_Controller_Start
{
    /**
     * errorAction() is the action that will be called by the "ErrorHandler"
     * plugin.  When an error/exception has been encountered
     * in a ZF MVC application (assuming the ErrorHandler has not been disabled
     * in your bootstrap) - the Errorhandler will set the next dispatchable
     * action to come here.  This is the "default" module, "error" controller,
     * specifically, the "error" action.  These options are configurable, see
     * {@link
     * http://framework.zend.com/manual/en/zend.controller.plugins.html#zend.controller.plugins.standard.errorhandler
     * the docs on the ErrorHandler Plugin}
     *
     * @return void
     */

    public function ajaxResponseAction()
    {
    }

    public function postDispatch()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->_request->getActionName() != 'denied') {
                $code = $this->getRequest()->getParam('code') ? true : $code = '';
                $this->_helper->viewRenderer->setNoRender();
                $this->_helper->layout->disableLayout();
                $errors = $this->_getParam('error_handler');
                $this->ajaxResponse(
                    Zend_Json::encode(
                        [
                            'error_message' =>
                                Core_Messages_Message::getMessage($code) . '<br> ' .
                                (is_object($errors) ? $errors->exception : ''),
                            'error'         => 'true'
                        ]
                    ),
                    'application/json'
                );
            } else {
                $this->_helper->viewRenderer->setNoRender();
                $this->_helper->layout->disableLayout();
                $this->ajaxResponse(
                    Zend_Json::encode(
                        [
                            'error_message' => 'no_rights',
                            'error'         => 'true'
                        ]
                    ),
                    'application/json'
                );
                $this->_helper->viewRenderer->setViewSuffix('phtml');
                $this->_helper->layout()->setLayout('error');
            }
        }
        // Grab the error object from the request
    }

    public function init()
    {
        parent::init();
        //	$this->_helper->layout->setLayout('clear');
    }

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:

                // 404 error -- controller or action not found

                $this->getResponse()->setHttpResponseCode(404);
                $this->view->title   = __('404');
                $this->view->message = Core_Messages_Message::getServerError(404);
                Core_Log_Logger::logErrorEvent($errors);

                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->title   = __('500');
                $this->view->message = Core_Messages_Message::getServerError(500);
                Core_Log_Logger::logErrorEvent($errors);
                break;
        }

        $this->view->main_title       = __('error');
        $this->view->main_keywords    = __('error');
        $this->view->main_description = __('error');

        // pass the environment to the view script so we can conditionally
        // display more/less information
        $this->view->env = $this->getInvokeArg('env');

        // pass the actual exception object to the view
        $this->view->exception = $errors->exception;

        // pass the request to the view
        $this->view->request = $errors->request;

        $conf                   = Zend_Registry::get('appConfig');
        $this->view->showErrors = $conf['showViewError'];
    }

    public function deniedAction()
    {
        $this->getResponse()->setHttpResponseCode(404);
    }

    public function notallowedAction()
    {
        $this->getResponse()->setHttpResponseCode(404);
    }

    public function noAccessAction()
    {
        $this->getResponse()->setHttpResponseCode(404);
    }

    public function noPageAction()
    {
        $this->getResponse()->setHttpResponseCode(404);
    }
}
