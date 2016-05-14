<?php

/**
 * Class Job_Ads_Model_VacanciesCollection
 *
 * @author Fedor Petryk
 */
class Job_Ads_Model_VacanciesCollection extends Core_Collection_Super
{
    protected $_domainObjectClass = 'Job_Ads_Model_Vacancy';

    /**
     * @param $tags
     *
     * @author Fedor Petryk
     * @return bool
     */
    public function populateWithTags($tags)
    {
        if (!count($tags) || !count($this->_resultSet)) {
            return false;
        }

        foreach ($tags as $tag) {
            if (isset($this->_resultSet[$tag['vacancyId']])) {
                /** @var Job_Ads_Model_Vacancy $vacancy */
                $vacancy = $this->_resultSet[$tag['vacancyId']];
                if ($vacancy->tags instanceof TagsCollection) {
                    $vacancy->tags->add($tag);
                } else {
                    $vacancy->tags = new TagsCollection();
                    $vacancy->tags->add($tag);
                }
            }
        }

        return true;
    }

    public function getRelevant($tags)
    {
        $relevantVacancies = new self();
        foreach ($tags as $tag) {
            foreach ($this as $vacancy) {
                if (in_array($tag, $vacancy->getTagsArray())) {
                    $relevantVacancies->add($vacancy);
                }
            }
        }

        return $relevantVacancies;
    }
}