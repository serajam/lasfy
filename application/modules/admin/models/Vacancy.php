<?php

/**
 * Class Vacancy
 */
class Vacancy extends Job_Ads_VacancyModel
{
    /**
     * Set tags as string separated by comma
     *
     * @param $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }
}