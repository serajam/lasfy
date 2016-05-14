<?php

/**
 * Class Job_Ads_ResumeModel
 */
class Job_Ads_Model_Resume extends Core_Model_Super
{
    protected $_data = [
        'resumeId'     => null,
        'seat'         => null,
        'experience'   => null,
        'expectations' => null,
        'goals'        => null,
        'tags'         => null,
        'isPublished'  => 1,
        'isTemporary'  => 0,
        'isBanned'     => 0,
        'createdAt'    => null,
        'captchaCode'  => null,
        'userId'       => null,
        'process'      => 'resume'
    ];

    public function getTagsArray()
    {
        $tags = [];
        if (isset($this->tags)) {
            foreach ($this->tags as $t) {
                $tags[] = $t->toArray();
            }
        }

        return $tags;
    }

    public function setTags($tags)
    {
        if (!empty($tags)) {
            foreach ($tags as $id => $t) {
                $tag = new Tag();
                $tag->populate(
                    [
                        'tagId'   => $id,
                        'tagName' => $t
                    ]
                );
                if ($this->tags instanceof TagsCollection) {
                    $this->tags->add($tag);
                } else {
                    $this->tags = new TagsCollection();
                    $this->tags->add($tag);
                }
            }
        }
    }
}