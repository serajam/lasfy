<?php

ini_set('iconv.internal_encoding', 'UTF-8');
ini_set('strtolower.internal_encoding', 'UTF-8');
setlocale(LC_ALL, 'ru_RU.UTF-8');
define('APPLICATION_PATH', realpath(dirname(dirname(__FILE__))));

$base  = realpath(dirname(__FILE__) . '/../');
$paths = ['.', $base . '/../library'];
ini_set('include_path', implode(PATH_SEPARATOR, $paths));

ini_set('display_errors', true);
error_reporting(E_ALL);

include('Zend/Loader/Autoloader.php');
include('functions.php');

/**
 * Class sendSubscriptions
 * It sends the ads to users according to their settings of subscriptions.
 */
class sendSubscriptions
{
    /**
     * Types of ads that we should send
     */
    const AD_TYPE_VACANCY = 'vacancy';

    const AD_TYPE_RESUME = 'resume';

    const AD_TYPE_ALL = 'all';

    /**
     * @var Zend_Db_Adapter_Abstract;
     */
    protected $_db;

    /**
     * @var Zend_Mail_Transport_Smtp
     */
    protected $_mailTransport;

    /**
     * @var Array
     */
    protected $_typesArray;

    /**
     * @var Array type
     */
    protected $_subTypeArray;

    /**
     * @var int Count of subscriptions
     */
    protected $_countAllSubscriptions = 0;

    /**
     * @var int Limit of select request
     */
    protected $_limit = 1000;

    /**
     * @var array
     */
    protected $_periodsArray = [];

    public function __construct()
    {
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->setFallbackAutoloader(true);

        $config = new Zend_Config_Ini (APPLICATION_PATH . '/config/application.ini', 'development');
        Zend_Registry::set('config', $config);
        $types             = new Zend_Config_Ini(APPLICATION_PATH . '/config/system_types.ini');
        $this->_typesArray = $types->toArray();

        date_default_timezone_set($config->timezone);

        foreach ($this->_typesArray['subscriptionPeriods'] as $number => $ta) {
            $this->_periodsArray[$number] = '-' . str_replace('_', ' ', $ta);
        }

        $this->_db = Zend_Db::factory(
            $config->database->adapter,
            $config->database->params->toArray()
        );

        $this->_db->query("SET NAMES 'utf8' collate 'utf8_general_ci'");
        Zend_Registry::set('DB', $this->_db);
        $configArr            = $config->toArray();
        $this->_mailTransport = new Zend_Mail_Transport_Smtp($configArr['mail']['server'], $configArr['mail']);
        Zend_Mail::setDefaultTransport($this->_mailTransport);

        $this->_subTypeArray = [
            1 => self::AD_TYPE_ALL,
            2 => self::AD_TYPE_VACANCY,
            3 => self::AD_TYPE_RESUME
        ];

        $this->_countAllSubscriptions =
            $this->_db
                ->query('SELECT COUNT(*) as subsCount FROM UserSubscriptions as us WHERE us.active = 1')
                ->fetch();

        $this->_countAllSubscriptions = ceil((int)$this->_countAllSubscriptions['subsCount'] / 1000);
    }

    public function sendEmails($subscriptionId = 0)
    {
        $subscriptions = $this->getSubscriptions($subscriptionId);

        if (empty($subscriptions)) {
            echo 'No subscriptions';

            return false;
        }

        $subsGroup = [];
        $lastId    = 0;
        foreach ($subscriptions as $sub) {
            $subsGroup[$this->_subTypeArray[$sub['type']]][] = $sub;
            $lastId                                          = $sub['subscriptionId'];
        }

        $tagsGroup = $this->_groupTags($subsGroup);

        $maxGroupPeriods = $this->_getMaxGroupPeriod($subsGroup);

        foreach ($subsGroup as $type => $sg) {
            $tagsIds = implode(',', array_values($tagsGroup[$type]));

            /** Process subscription for both type of ads. */
            if (strcmp($type, self::AD_TYPE_ALL) == 0) {
                $startAdId             = 0;
                $countVacanciesInGroup = $this->_getCountOfVacanciesOfGroup(
                    $tagsIds,
                    $this->_periodsArray[$maxGroupPeriods[$type]]
                );
                $countResumesInGroup   = $this->_getCountOfResumesOfGroup(
                    $tagsIds,
                    $this->_periodsArray[$maxGroupPeriods[$type]]
                );

                if (empty($countVacanciesInGroup) && empty($countResumesInGroup)) {
                    continue;
                }

                $vacanciesIterationCount = ceil((int)$countVacanciesInGroup / 1000);
                $resumesIterationCount   = ceil((int)$countResumesInGroup / 1000);

                while ($vacanciesIterationCount > 0) {
                    $vacancies = $this->_getVacanciesByTagsIds(
                        $tagsIds,
                        $this->_periodsArray[$maxGroupPeriods[$type]],
                        $startAdId);

                    foreach ($sg as $sub) {
                        $isProcessed = $this->_processSubs($vacancies, self::AD_TYPE_VACANCY, $sub);
                        if (!$isProcessed) {
                            echo 'Not processed type: ' . self::AD_TYPE_VACANCY;

                            return false;
                        }
                    }

                    $startAdId = array_keys($vacancies);
                    sort($startAdId);
                    $startAdId = array_pop($startAdId);
                    $vacanciesIterationCount--;
                }

                $startAdId = 0;
                while ($resumesIterationCount > 0) {
                    $resumes = $this->_getResumesByTagsIds(
                        $tagsIds,
                        $this->_periodsArray[$maxGroupPeriods[$type]],
                        $startAdId);

                    foreach ($sg as $sub) {
                        $isProcessed = $this->_processSubs($resumes, self::AD_TYPE_RESUME, $sub);
                        if (!$isProcessed) {
                            echo 'Not processed type: ' . self::AD_TYPE_RESUME;

                            return false;
                        }
                    }

                    $startAdId = array_keys($resumes);
                    sort($startAdId);
                    $startAdId = array_pop($startAdId);
                    $resumesIterationCount--;
                }

                foreach ($sg as $sub) {
                    $isUpdate = $this->_db->update(
                        'UserSubscriptions',
                        ['lastSent' => date('Y-m-d H:i:s')],
                        ['subscriptionId = ?' => $sub['subscriptionId']]
                    );
                }
            }

            /** Process subscription for resumes. */
            if (strcmp($type, self::AD_TYPE_RESUME) == 0) {
                $startAdId           = 0;
                $countResumesInGroup = $this->_getCountOfResumesOfGroup(
                    $tagsIds,
                    $this->_periodsArray[$maxGroupPeriods[$type]]
                );

                if (empty($countResumesInGroup)) {
                    continue;
                }

                $resumesIterationCount = ceil((int)$countResumesInGroup / 1000);

                while ($resumesIterationCount > 0) {
                    $resumes = $this->_getResumesByTagsIds(
                        $tagsIds,
                        $this->_periodsArray[$maxGroupPeriods[$type]],
                        $startAdId);

                    foreach ($sg as $sub) {
                        $isProcessed = $this->_processSubs($resumes, self::AD_TYPE_RESUME, $sub);
                        if (!$isProcessed) {
                            echo 'Not processed type: ' . self::AD_TYPE_RESUME;

                            return false;
                        }
                        $isUpdate = $this->_db->update(
                            'UserSubscriptions',
                            ['lastSent' => date('Y-m-d H:i:s')],
                            ['subscriptionId = ?' => $sub['subscriptionId']]
                        );
                    }

                    $startAdId = array_keys($resumes);
                    sort($startAdId);
                    $startAdId = array_pop($startAdId);
                    $resumesIterationCount--;
                }
            }

            /** Process subscription for vacancies. */
            if (strcmp($type, self::AD_TYPE_VACANCY) == 0) {
                $startAdId             = 0;
                $countVacanciesInGroup = $this->_getCountOfVacanciesOfGroup(
                    $tagsIds,
                    $this->_periodsArray[$maxGroupPeriods[$type]]
                );
                if (empty($countVacanciesInGroup)) {
                    continue;
                }

                $vacanciesIterationCount = ceil((int)$countVacanciesInGroup / 1000);

                while ($vacanciesIterationCount > 0) {
                    $vacancies = $this->_getVacanciesByTagsIds(
                        $tagsIds,
                        $this->_periodsArray[$maxGroupPeriods[$type]],
                        $startAdId);

                    foreach ($sg as $sub) {
                        $isProcessed = $this->_processSubs($vacancies, self::AD_TYPE_VACANCY, $sub);
                        if (!$isProcessed) {
                            echo 'Not processed type: ' . self::AD_TYPE_VACANCY;

                            return false;
                        }
                        $isUpdate = $this->_db->update(
                            'UserSubscriptions',
                            ['lastSent' => date('Y-m-d H:i:s')],
                            ['subscriptionId = ?' => $sub['subscriptionId']]
                        );
                    }

                    $startAdId = array_keys($vacancies);
                    sort($startAdId);
                    $startAdId = array_pop($startAdId);
                    $vacanciesIterationCount--;
                }
            }
        }

        $this->_countAllSubscriptions--;
        if ($this->_countAllSubscriptions > 0) {
            $this->sendEmails($lastId);
        }

        return true;
    }

    protected function getSubscriptions($subscriptionId = 0)
    {
        $selectAllSubscriptions = $this->_db->select()
            ->from(['us' => 'UserSubscriptions'])
            ->where('us.subscriptionId > ?', $subscriptionId)
            ->where('us.active = 1')
            ->order('us.subscriptionId ASC')
            ->limit(1000);

        $subscriptions = $this->_db->fetchAll($selectAllSubscriptions);

        return $subscriptions;
    }

    /**
     * Get all tags for each type of subscriptions (all, vacancy, resume).
     *
     * @param $subsGroup
     *
     * @return array
     * @throws Zend_Json_Exception
     */
    protected function _groupTags(&$subsGroup)
    {
        $tagsGroup = [];
        foreach ($subsGroup as $type => &$sg) {
            $tagsGroup[$type] = [];
            foreach ($sg as &$sgt) {
                $tags             = Zend_Json_Decoder::decode($sgt['tags']);
                $keys             = array_keys($tags);
                $tagsGroup[$type] = array_unique(array_merge($tagsGroup[$type], $keys));
                $sgt['tagsIds']   = $keys;
            }
        }

        return $tagsGroup;
    }

    /**
     * Get max period of ads inside each type of subscriptions (all, vacancy, resume).
     *
     * @param $tagsGroup
     *
     * @return array
     */
    protected function _getMaxGroupPeriod($tagsGroup)
    {
        $maxGroupPeriod = [];

        foreach ($tagsGroup as $type => $tg) {
            $maxGroupPeriod[$type] = 0;
            foreach ($tg as $t) {
                if ($t['period'] > $maxGroupPeriod[$type]) {
                    $maxGroupPeriod[$type] = $t['period'];
                }
            }
        }

        return $maxGroupPeriod;
    }

    /**
     * Get count of vacancies for specific subscription group.
     *
     * @param $tagsIds
     * @param $period
     *
     * @return string
     */
    protected function _getCountOfVacanciesOfGroup($tagsIds, $period)
    {
        $dateRange = date('Y-m-d', strtotime($period));

        $select = $this->_db->select()
            ->from(['v' => 'Vacancies'], ['count(*)'])
            ->joinLeft(['vt' => 'VacancyTags'], 'vt.vacancyId = v.vacancyId', [])
            ->joinLeft(['t' => 'Tags'], 't.tagId = vt.tagId', [])
            ->where('vt.tagId IN (' . $tagsIds . ')')
            ->where('v.isBanned = 0')
            ->where('v.isPublished = 1')
            ->where('v.createdAt > ?', $dateRange)
            ->orWhere('v.renewedAt > ?', $dateRange);

        $res = $this->_db->fetchOne($select);

        return $res;
    }

    /**
     * Get count of resumes for specific subscription group.
     *
     * @param $tagsIds
     * @param $period
     *
     * @return string
     */
    protected function _getCountOfResumesOfGroup($tagsIds, $period)
    {
        $dateRange = date('Y-m-d', strtotime($period));

        $select = $this->_db->select()
            ->from(['r' => 'Resumes'], ['count(*)'])
            ->joinLeft(['rt' => 'ResumeTags'], 'rt.resumeId = r.resumeId', [])
            ->joinLeft(['t' => 'Tags'], 't.tagId = rt.tagId', [])
            ->where('rt.tagId IN (' . $tagsIds . ')')
            ->where('r.isBanned = 0')
            ->where('r.isPublished = 1')
            ->where('r.createdAt > ?', $dateRange)
            ->orWhere('r.renewedAt > ?', $dateRange);

        $res = $this->_db->fetchOne($select);

        return $res;
    }

    /**
     * Get vacancies according to tags (it uses ids of tags and max period).
     *
     * @param $tagsIds
     * @param $period
     * @param $vacancyId
     *
     * @return array|bool
     */
    protected function _getVacanciesByTagsIds($tagsIds, $period, $vacancyId = 0)
    {
        $dateRange = date('Y-m-d', strtotime($period));

        $select = $this->_db->select()
            ->from(['v' => 'Vacancies'])
            ->joinLeft(['vt' => 'VacancyTags'], 'vt.vacancyId = v.vacancyId', [])
            ->joinLeft(['t' => 'Tags'], 't.tagId = vt.tagId')
            ->where('vt.tagId IN (' . $tagsIds . ')')
            ->where('v.vacancyId > ?', $vacancyId)
            ->where('v.isBanned = 0')
            ->where('v.isPublished = 1')
            ->where('v.createdAt > ?', $dateRange)
            ->orWhere('v.renewedAt > ?', $dateRange)
            ->order('v.vacancyId ASC')
            ->limit(1000);

        $res = $this->_db->fetchAll($select);

        $vacancies = [];

        if (!empty($res)) {
            foreach ($res as $r) {
                if (!isset($vacancies[$r['vacancyId']])) {
                    $vacancies[$r['vacancyId']]['seat']      = $r['seat'];
                    $vacancies[$r['vacancyId']]['createdAt'] = $r['createdAt'];
                    $vacancies[$r['vacancyId']]['renewedAt'] = $r['renewedAt'];
                    $vacancies[$r['vacancyId']]['tags']      = $r['tagName'];
                    $vacancies[$r['vacancyId']]['tagsIds'][] = $r['tagId'];
                } else {
                    $vacancies[$r['vacancyId']]['tags']      = $vacancies[$r['vacancyId']]['tags'] . ', ' . $r['tagName'];
                    $vacancies[$r['vacancyId']]['tagsIds'][] = $r['tagId'];
                }
            }

            return $vacancies;
        }

        return false;
    }

    protected function _processSubs($ad, $adsType, $sub)
    {
        $dateRange = strtotime('now ' . $this->_periodsArray[$sub['period']]);
        $lastSent  = strtotime($sub['lastSent']);

        foreach ($ad as $id => $a) {
            $createdAt = strtotime($a['createdAt']);
            $renewedAt = strtotime($a['renewedAt']);
            $ifInTags  = false;

            foreach ($a['tagsIds'] as $tagId) {
                if (in_array($tagId, $sub['tagsIds'])) {
                    $ifInTags = true;
                    break;
                }
            }

            /** if tags of ad not in the set of tags of subscription */
            if (!$ifInTags) {
                continue;
            }

            /** if we need only new ads */
            if ($sub['onlyNew'] && $renewedAt > 0) {
                continue;
            }

            /** Compare date of ad with period of subscription */
            if ($dateRange > $createdAt) {
                continue;
            }

            /** if we already sent email we skip this ad */
            if ($lastSent > $dateRange) {
                continue;
            }

            $user        = $this->_getUsersDetails($sub['userId']);
            $config      = Zend_Registry::get('config')->toArray();
            $defaultLang = $user['language'];
            Zend_Registry::set('language', $defaultLang);

            $params = [
                'fio'  => $user['userName'],
                'seat' => $a['seat'],
                'link' => $config['baseHttpPath'] . $defaultLang .
                    '/default/search/get-ads/type/' . $adsType . '/' . $adsType . 'Id/' . $id,
            ];

            $mail = new Core_Mailer('newAd', $params, true);
            $mail->addTo($user['email'], $user['userName']);
            $mail->send();
        }

        return true;
    }

    /**
     * Get user details by userId
     *
     * @param $userId
     *
     * @return mixed
     */
    protected function _getUsersDetails($userId)
    {
        $selectUser = $this->_db->select()
            ->from(['u' => 'Users'])
            ->where('u.userId = ?', $userId);

        $user = $this->_db->fetchRow($selectUser);

        return $user;
    }

    /**
     * Get resumes according to tags (it uses ids of tags and max period).
     *
     * @param $tagsIds
     * @param $period
     * @param $resumeId
     *
     * @return array|bool
     */
    protected function _getResumesByTagsIds($tagsIds, $period, $resumeId = 0)
    {
        $dateRange = date('Y-m-d', strtotime($period));

        $select = $this->_db->select()
            ->from(['r' => 'Resumes'])
            ->joinLeft(['rt' => 'ResumeTags'], 'rt.resumeId = r.resumeId')
            ->joinLeft(['t' => 'Tags'], 't.tagId = rt.tagId')
            ->where('rt.tagId IN (' . $tagsIds . ')')
            ->where('r.resumeId > ?', $resumeId)
            ->where('r.isBanned = 0')
            ->where('r.isPublished = 1')
            ->where('r.createdAt > ?', $dateRange)
            ->orWhere('r.renewedAt > ?', $dateRange)
            ->order('r.resumeId ASC')
            ->limit(1000);

        $res = $this->_db->fetchAll($select);

        $resumes = [];

        if (!empty($res)) {
            foreach ($res as $r) {
                if (!isset($resumes[$r['resumeId']])) {
                    $resumes[$r['resumeId']]['seat']      = $r['seat'];
                    $resumes[$r['resumeId']]['createdAt'] = $r['createdAt'];
                    $resumes[$r['resumeId']]['renewedAt'] = $r['renewedAt'];
                    $resumes[$r['resumeId']]['tags']      = $r['tagName'];
                    $resumes[$r['resumeId']]['tagsIds'][] = $r['tagId'];
                } else {
                    $resumes[$r['resumeId']]['tags']      = $resumes[$r['resumeId']]['tags'] . ', ' . $r['tagName'];
                    $resumes[$r['resumeId']]['tagsIds'][] = $r['tagId'];
                }
            }

            return $resumes;
        }

        return false;
    }

    /**
     * Get all ads for each type of subscriptions (all, vacancy, resume)
     * according to tags (it uses ids of tags and max period).
     *
     * @param $tagsGroup
     * @param $maxGroupPeriods
     *
     * @return array
     */
    protected function _getAds($tagsGroup, $maxGroupPeriods)
    {
        $ads = [];
        foreach ($tagsGroup as $type => $tg) {
            if (strcmp($type, self::AD_TYPE_ALL) == 0) {
                $tagsIds                                       = implode(',', array_values($tg));
                $ads[self::AD_TYPE_ALL][self::AD_TYPE_VACANCY] =
                    $this->_getVacanciesByTagsIds($tagsIds, $this->_periodsArray[$maxGroupPeriods[self::AD_TYPE_ALL]]);

                $ads[self::AD_TYPE_ALL][self::AD_TYPE_RESUME] =
                    $this->_getResumesByTagsIds($tagsIds, $this->_periodsArray[$maxGroupPeriods[self::AD_TYPE_ALL]]);
            }

            if (strcmp($type, self::AD_TYPE_VACANCY) == 0) {
                $tagsIds                    = implode(',', array_values($tg));
                $ads[self::AD_TYPE_VACANCY] =
                    $this->_getVacanciesByTagsIds($tagsIds, $this->_periodsArray[$maxGroupPeriods[self::AD_TYPE_VACANCY]]);
            }

            if (strcmp($type, self::AD_TYPE_RESUME) == 0) {
                $tagsIds                   = implode(',', array_values($tg));
                $ads[self::AD_TYPE_RESUME] =
                    $this->_getResumesByTagsIds($tagsIds, $this->_periodsArray[$maxGroupPeriods[self::AD_TYPE_RESUME]]);
            }
        }

        return $ads;
    }
}

$subSendingCron = new sendSubscriptions();
$subSendingCron->sendEmails();

