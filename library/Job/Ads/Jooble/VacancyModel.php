<?php

/**
 * Class Job_Ads_Jooble_VacancyModel
 *
 * @author Fedor Petryk
 */
class Job_Ads_Jooble_VacancyModel extends Job_Ads_Model_Vacancy
{
    protected $_data = [
        'vacancyId'          => null,
        'seat'               => null,
        'requirements'       => null,
        'vacancyDescription' => null,
        'offer'              => null,
        'tags'               => null,
        'isPublished'        => 1,
        'isTemporary'        => 0,
        'isBanned'           => 0,
        'createdAt'          => null,
        'captchaCode'        => null,
        'userId'             => null,
        'process'            => 'vacancy',
        'jooble'             => 1,
        'link'               => null,
        'source'             => null,
        'salary'             => null
    ];
}