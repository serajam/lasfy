<?php

/**
 * Class Job_Ads_Model_Jooble_VacanciesCollection
 *
 * @author Fedor Petryk
 */
class Job_Ads_Model_Jooble_VacanciesCollection extends Core_Collection_Super
{
    protected $_domainObjectClass = 'Job_Ads_Jooble_VacancyModel';

    /**
     * @param $tags
     *
     * @author Fedor Petryk
     */
    public function populateWithTags($tags)
    {
        $tagsCollection = new TagsCollection();
        foreach ($tags as $key => $tag) {
            $tagModel          = new Job_Tags_TagModel();
            $tagModel->tagId   = $key;
            $tagModel->tagName = $tag;
            $tagsCollection->add($tagModel);
        }

        foreach ($this->_resultSet as $ad) {
            $ad->tags = $tagsCollection;
        }
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