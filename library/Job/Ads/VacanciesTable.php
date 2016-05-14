<?php

/**
 * Class Job_Ads_VacanciesTable
 *
 * @author Fedor Petryk
 */
class Job_Ads_VacanciesTable extends Job_Ads_TableGateway
{
    const JOIN_VACANCY = 'Vacancy';

    /** Table name */
    protected $_name = 'Vacancies';

    protected $_primary = 'vacancyId';

    protected $alias = 'v';

    protected $aliasTagsJoin = 'vt';

    protected $tagsJoinTable = 'VacancyTags';
}