<?php

class Job_Ads_Jooble_VacanciesRepository extends Job_Ads_Repository
{
    protected $type = 'vacanciesJooble';

    protected $collection = 'Job_Ads_Model_Jooble_VacanciesCollection';

    /**
     * @param array $tags
     * @param int   $page
     *
     * @return Core_Collection_Super|Job_Ads_Model_VacanciesCollection
     *
     * @author Fedor Petryk
     */
    public function fetchByTags(array $tags, $page)
    {
        $this->paginator = $this->gateway->fetchByTags($tags, $page);

        return $this->getCollection($this->gateway->getVacancies());
    }

    /**
     * @param Traversable $array
     *
     * @return Job_Ads_Model_VacanciesCollection
     *
     * @author Fedor Petryk
     */
    public function getCollection($array)
    {
        /** @var Job_Ads_Model_VacanciesCollection $vacancies */
        $vacancies = new $this->collection;

        if (empty($array)) {
            return $vacancies;
        }

        /** @var Jooble_Model_Vacancy $vacancy */
        foreach ($array as $vacancy) {
            $vacancyLocal = Job_Ads_Jooble_VacancyAdapter::create($vacancy);
            $vacancies->add($vacancyLocal);
        }

        return $vacancies;
    }

    /**
     * @param int    $page
     * @param String $starDate
     * @param String $endDate
     *
     * @return Core_Collection_Super
     *
     * @author Fedor Petryk
     */
    public function fetchByPeriod($page, $starDate, $endDate)
    {
        $this->paginator = $this->gateway->fetchByPeriod($page, $starDate, $endDate);

        return $this->getCollection($this->gateway->getVacancies());
    }
}