<?php

/**
 * Resume
 */
class Resume extends Job_Ads_ResumeModel
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