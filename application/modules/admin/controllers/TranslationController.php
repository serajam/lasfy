<?php

/**
 * User administration controller
 *
 * @author     Petryk Fedor
 *
 */
class Admin_TranslationController extends Core_Controller_Start
{
    /**
     * Default service class name for current controller
     *
     * @var String
     */
    protected $_defaultServiceName = 'TranslationService';

    /**
     * The service layer object
     *
     * @var TranslationService
     */
    protected $_service;

    public function indexAction()
    {
        if ($this->_request->isPost()) {
            $this->_service->translations(
                $this->_request->getParams()
            );
        }
    }

    public function saveAction()
    {
        if ($this->_request->isPost()) {
            $this->_service->save($this->_request->getParams());
        }
    }

    public function addAction()
    {
        if ($this->_request->isPost()) {
            $this->_service->add($this->_request->getPost());
        }
    }
}
