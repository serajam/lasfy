<?php

/**
 * Class Job_Ads_Model_ResumesCollection
 *
 * @author Fedor Petryk
 */
class Job_Ads_Model_ResumesCollection extends Core_Collection_Super
{
    protected $_domainObjectClass = 'Job_Ads_Model_Resume';

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
            if (isset($this->_resultSet[$tag['resumeId']])) {
                /** @var Job_Ads_Model_Resume $resume */
                $resume = $this->_resultSet[$tag['resumeId']];
                if ($resume->tags instanceof TagsCollection) {
                    $resume->tags->add($tag);
                } else {
                    $resume->tags = new TagsCollection();
                    $resume->tags->add($tag);
                }
            }
        }

        return false;
    }

    public function getRelevant($tags)
    {
        $relevantResumes = new self();
        foreach ($tags as $tag) {
            foreach ($this as $resume) {
                if (in_array($tag, $resume->getTagsArray())) {
                    $relevantResumes->add($resume);
                }
            }
        }

        return $relevantResumes;
    }
}