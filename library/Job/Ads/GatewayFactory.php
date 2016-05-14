<?php

class Job_Ads_GatewayFactory
{
    protected static $gateways = [
        'partners' => 'Job_Ads_Jooble_ApiGateway',
        'vacancy'  => 'Job_Ads_VacanciesTable',
        'resume'   => 'Job_Ads_ResumesTable',
    ];

    /**
     * @param $type
     *
     * @return Job_Ads_IAdsGateway|null
     *
     * @author Fedor Petryk
     */
    static public function create($type)
    {
        $gateway = isset(self::$gateways[$type]) ? new self::$gateways[$type] : null;

        return $gateway;
    }
}