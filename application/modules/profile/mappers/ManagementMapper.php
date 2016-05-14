<?php

/**
 *
 * The management mapper class
 *
 * @author     Alexey Kagarlykskiy
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class ManagementMapper extends Job_Ads_Mapper
{
  public function getUserCompany($userId)
  {
    $db = $this->getAdapter();

    $sql = $db->select()
        ->from(['c' => 'Companies'])
        ->where('c.userId=' . $userId);

    $result = $db->fetchAll($sql);

    if (empty($result)) {
      return false;
    }

    $company = new Core_Model_Company($result);

    return $company;
  }
}