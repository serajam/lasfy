<?php

/**
 * Class SubscriptionService
 *
 * @author Fedor Petryk
 */
class SubscriptionService extends Core_Service_Editor
{
    /**
     * Data Mapper name
     *
     * @var string
     */
    protected $_mapperName = 'SubscriptionMapper';

    /**
     * @var SubscriptionMapper
     */
    protected $_mapper;

    /**
     * @param int $id Subscription id
     *
     * @return bool
     * @throws Zend_Db_Table_Exception
     *
     * @author Fedor Petryk
     */
    public function deactivate($id)
    {
        $table = $this->getMapper()->getDbTable();
        $row   = $table->fetchRow(['subscriptionId = ?' => $id, 'userId = ?' => Core_Model_User::getInstance()->userId]);

        if (!$row) {
            $this->setError((Core_Messages_Message::getMessage('no_subscription')));

            return false;
        }
        $row->active ? $row->active = 0 : $row->active = 1;
        $row->save();
        $this->setMessage(
            Core_Messages_Message::getMessage($row->active ? 'subscription_activated' : 'subscription_deactivated')
        );

        return true;
    }

    public function unSubscribe($id)
    {
        $table = $this->getMapper()->getDbTable();
        $row   = $table->fetchRow(['subscriptionId = ?' => $id, 'userId = ?' => Core_Model_User::getInstance()->userId]);

        if (!$row) {
            $this->setError((Core_Messages_Message::getMessage('no_subscription')));

            return false;
        }
        $row->delete();
        $this->setMessage(Core_Messages_Message::getMessage('unsubscribed'));

        return true;
    }

    public function edit($post, $id = null)
    {
        return $this->subscribe($post, $id);
    }

    /**
     * @param array $data post data
     * @param int   $id
     *
     * @return bool|mixed
     * @throws Zend_Form_Exception
     *
     * @author Fedor Petryk
     */
    public function subscribe(array $data, $id = null)
    {
        // validate
        $form = $this->getSubscriptionForm();
        if (!$form->isValid($data)) {
            $this->_processFormError($form);

            return false;
        }

        // get tags ids by names; validate that tags exists
        // @TODO such method must be replaced
        $validatedData = $form->getValues();
        $tags          = $this->_mapper->getExistedTagsByName(Job_Tags_TagModel::tagsStringToArray($validatedData['tags']));
        if (!$tags) {
            $this->setError(Core_Messages_Message::getMessage('invalid_tags'));

            return false;
        }

        if ($id) {
            $table = $this->getMapper()->getDbTable();
            $row   = $table->fetchRow(['subscriptionId' => $id, 'userId' => Core_Model_User::getInstance()->userId]);

            if (!$row) {
                $this->setError((Core_Messages_Message::getMessage('no_subscription')));

                return false;
            }

            $row->type = $validatedData['type'];;
            $row->active  = $validatedData['active'];
            $row->onlyNew = $validatedData['onlyNew'];
            $row->period  = $validatedData['period'];
            $row->tags    = json_encode($tags);
            $row->save();

            $this->setMessage(Core_Messages_Message::getMessage('subscription_updated'));

            return true;
        }

        $validatedData['tags'] = (array)$tags;

        // create subscription model with json encoded tags
        $subscription = new Subscription($validatedData);
        $subscription->setUserId(Core_Model_User::getInstance()->userId);
        $result = $this->_mapper->saveSubscription($subscription);

        if (!$result) {
            $this->setError(Core_Messages_Message::getMessage('failed_to_save_tags'));

            return false;
        }
        $this->setMessage(Core_Messages_Message::getMessage('subscription_added'));

        return $result;
    }

    /**
     *
     * @author Fedor Petryk
     */
    public function getSubscriptionForm()
    {
        if ($this->getFormLoader()->isExists('SubscriptionForm')) {
            return $this->getFormLoader()->getForm('SubscriptionForm');
        }
        $subscriptionForm = $this->getFormLoader()->addForm('SubscriptionForm');
        $trans            = Zend_Registry::get('translation');
        $empty            = ['' => __('make_choose')];

        $types   = Zend_Registry::get('types');
        $periods = $types['subscriptionPeriods'];
        $subscriptionForm->getElement('period')->addMultiOptions($empty + $trans->translatePairs(($periods)));

        $addsType = $types['addType'];
        $subscriptionForm->getElement('type')->addMultiOptions($empty + $trans->translatePairs(($addsType)));

        return $subscriptionForm;
    }

    public function duplicate($id)
    {
        $table = $this->getMapper()->getDbTable();
        $row   = $table->fetchRow(['subscriptionId = ?' => $id, 'userId = ?' => Core_Model_User::getInstance()->userId]);

        if (!$row) {
            $this->setError((Core_Messages_Message::getMessage('no_subscription')));

            return false;
        }
        $data = $row->toArray();
        unset($data['subscriptionId']);
        $table->insert($data);
        $this->setMessage(Core_Messages_Message::getMessage('subscription_duplicated'));

        return true;
    }

    /**
     *
     * @author Fedor Petryk
     */
    public function getUserSubscriptions()
    {
        $user = Core_Model_User::getInstance();

        return $this->_mapper->getUserSubscriptions($user->userId);
    }
}