<?php

/**
 * Class Job_Ads_View
 */
class Job_Ads_ResponseFormatter
{
    protected $compliance;

    protected $tags = null;

    public function __construct($compliance, $tags)
    {
        $this->compliance = $compliance;
        $this->tags       = $tags;
    }

    public function json($data)
    {
        $rArray = false;
        if (empty($data)) {
            return $rArray;
        }

        foreach ($data as $d) {
            $tags = $d->getTagsArray();
            if ($this->compliance && !empty($this->tags)) {
                $complArray = $this->makeTagsArrayForCompliance($tags);
                sort($this->tags);
                if (count($this->tags) != count(array_intersect($this->tags, $complArray))) {
                    unset($d);
                    continue;
                }
            }

            $tagsFormatted = [];
            foreach ($tags as $tag) {
                $tagsFormatted[] = ['tagId' => $tag['tagId'], 'tagName' => $tag['tagName']];
            }

            $dArray         = $d->toArray();
            $dArray['tags'] = $tagsFormatted;
            $rArray[]       = $dArray;
        }

        return $rArray;
    }

    public function makeTagsArrayForCompliance($tags)
    {
        $this->tags = [];
        foreach ($tags as $t) {
            $this->tags[] = $t['tagName'];
        }

        sort($this->tags);

        return $this->tags;
    }

    /**
     * @param mixed $compliance
     */
    public function setCompliance($compliance)
    {
        $this->compliance = $compliance;
    }

    /**
     * @param null $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }
}