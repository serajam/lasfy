<?php

/**
 * Class SubscriptionMapper
 *
 * @author Fedor Petryk
 */
class SubscriptionMapper extends Core_Mapper_Front
{
    /**
     * DbTable class name
     *
     * @var string
     */
    protected $_tableName = 'UserSubscriptionsTable';

    /**
     *
     * Users model
     *
     * @var Core_Mapper_Super
     */
    protected $_rowClass = 'Subscription';

    /**
     * @var string
     */
    protected $_collectionClass = 'SubscriptionCollection';

    /**
     * Save user subscription
     *
     * @param Subscription $subscription
     *
     * @return bool|mixed
     *
     * @author Fedor Petryk
     */
    public function saveSubscription(Subscription $subscription)
    {
        $table = new UserSubscriptionsTable();

        try {
            return $table->saveModel($subscription);
        } catch (Exception $e) {
            error_log("{$e}");

            return false;
        }
    }

    /**
     * @param $userId
     *
     * @author Fedor Petryk
     */
    public function getUserSubscriptions($userId)
    {
        $db         = $this->getDbTable();
        $result     = $db->fetchAll('userId = ' . $userId);
        $collection = new $this->_collectionClass();
        if ($result->count()) {
            foreach ($result as $subscriptionRow) {
                $subscription = new Subscription($subscriptionRow->toArray());
                $subscription->setJsonEncodedTags($subscriptionRow->tags);
                $collection->add($subscription);
            }
        }

        return $collection;
    }
}