<?php

/**
 * Class Jooble_SearchResponse
 *
 * @author Fedor Petryk
 */
class Jooble_Search_ResponseParser
{
    /**
     * @var Jooble_Model_Vacancies
     */
    protected $vacancies;

    /**
     * Jooble_Search_ResponseParser constructor.
     *
     * @param Jooble_Model_Vacancies $collection
     */
    public function __construct(Jooble_Model_Vacancies $collection)
    {
        $this->vacancies = $collection;
    }

    /**
     * @param string $response
     */
    public function parse($response)
    {
        $jobs = json_decode($response);

        $this->validate($jobs);

        $this->vacancies->setPagesCount(floor((int)$jobs->totalCount / count($jobs->jobs)));

        foreach ($jobs->jobs as $job) {
            $vacancy = new Jooble_Model_Vacancy();
            $vacancy->setPosition((string)strip_tags($job->title));
            $vacancy->setRegion((string)strip_tags($job->location));
            $vacancy->setDescription((string)html_entity_decode(strip_tags($job->snippet)));
            $vacancy->setSalary((string)strip_tags($job->salary));
            $vacancy->setSourceUrl((string)strip_tags($job->link));
            $vacancy->setSource((string)strip_tags($job->source));
            if (property_exists($job, "company")) {
                $vacancy->setCompany((string)strip_tags($job->company));
            }
            $vacancy->setExternalId((int)strip_tags($job->id));

            $this->vacancies->addVacancy($vacancy);
        }
    }

    /**
     * @param $response
     *
     * @throws Jooble_Exception_InvalidResponse
     */
    protected function validate($response)
    {
        if (isset($response->statusCode) && $response->statusCode != 200) {
            throw new Jooble_Exception_InvalidResponse($response->message . "\n" . $response->details);
        }
    }

    /**
     * @return Jooble_Model_Vacancies
     */
    public function getVacancies()
    {
        return $this->vacancies;
    }
}
