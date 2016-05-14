<?php

/**
 * Class Job_Ads_VacancyModel
 */
class Job_Ads_Model_Vacancy extends Core_Model_Super
{
    protected $_data = [
        'vacancyId'          => null,
        'seat'               => null,
        'requirements'       => null,
        'vacancyDescription' => null,
        'offer'              => null,
        'tags'               => null,
        'isPublished'        => 1,
        'isTemporary'        => 0,
        'isBanned'           => 0,
        'createdAt'          => null,
        'captchaCode'        => null,
        'userId'             => null,
        'process'            => 'vacancy'
    ];

    /**
     * @var Core_Model_Company
     */
    protected $company;

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

    /**
     * @return Core_Model_Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param Core_Model_Company $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
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