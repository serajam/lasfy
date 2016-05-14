<?php

class Job_Ads_RepositoryFactory
{
    protected static $repositories = [
        'partners' => 'Job_Ads_Jooble_VacanciesRepository',
        'vacancy'  => 'Job_Ads_VacanciesRepository',
        'resume'   => 'Job_Ads_ResumeRepository',
    ];

    /**
     * @param String              $type
     * @param Job_Ads_IAdsGateway $gateway
     *
     * @return null
     *
     * @author Fedor Petryk
     */
    static public function create($type, Job_Ads_IAdsGateway $gateway)
    {
        $gateway = isset(self::$repositories[$type]) ? new self::$repositories[$type]($gateway) : null;

        return $gateway;
    }
}