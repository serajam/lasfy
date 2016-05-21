<?php

/**
 * Class Job_Ads_JoobleVacancyAdapter
 * Adapt jooble model to local model
 *
 * @author Fedor Petryk
 */
class Job_Ads_Jooble_VacancyAdapter
{
    /**
     * @param Jooble_Model_Vacancy $joobleModelVacancy
     *
     * @return Job_Ads_Jooble_VacancyModel
     *
     * @author Fedor Petryk
     */
    static public function create(Jooble_Model_Vacancy $joobleModelVacancy)
    {
        $vacancy                     = new Job_Ads_Jooble_VacancyModel();
        $vacancy->vacancyDescription = $joobleModelVacancy->getDescription();
        $vacancy->seat               = $joobleModelVacancy->getPosition();
        $vacancy->vacancyId          = $joobleModelVacancy->getExternalId();
        $vacancy->createdAt          = date('Y-m-d');
        $vacancy->link               = $joobleModelVacancy->getSourceUrl();
        $vacancy->source             = $joobleModelVacancy->getSource();
        $vacancy->salary             = $joobleModelVacancy->getSalary();

        if ($joobleModelVacancy->getCompany()) {
            $vacancy->setTags([$joobleModelVacancy->getCompany()]);
            $vacancy->setCompany($joobleModelVacancy->getCompany());
        }

        return $vacancy;
    }
}
