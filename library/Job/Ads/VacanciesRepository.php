<?php

/**
 * Class Job_Ads_VacanciesRepository
 *
 * @author Fedor Petryk
 */
class Job_Ads_VacanciesRepository extends Job_Ads_Repository
{
    protected $type = 'vacancies';

    protected $collection = 'Job_Ads_Model_VacanciesCollection';
}