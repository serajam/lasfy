<?php

/**
 * Class Profile_SubscriptionController
 *
 * @author Fedor Petryk
 */
class Profile_SubscriptionController extends Core_Controller_Editor
{
    protected $_defaultServiceName = 'SubscriptionService';

    /**
     * @var SubscriptionService
     */
    protected $_service;

    public function preDispatch()
    {
        $this->_helper->layout->setLayout('profile');
        parent::preDispatch();
    }

    public function indexAction()
    {
        if ($this->_request->isPost()) {
            $this->_service->subscribe($this->_request->getParams());
        }
        $this->view->header           = __('yourSubscriptions');
        $this->view->subscriptionForm = $this->_service->getSubscriptionForm();
        $this->view->collection       = $this->_service->getUserSubscriptions();
    }

    public function unSubscribeAction()
    {
        $this->_service->unSubscribe((int)$this->getParam('id', null));
    }

    public function deactivateAction()
    {
        $this->_service->deactivate((int)$this->getParam('id', null));
    }

    public function duplicateAction()
    {
        $this->_service->duplicate((int)$this->getParam('id', null));
    }
}
