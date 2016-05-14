<?php

/**
 * Class Job_Tag_TagModel
 */
class Job_Tags_TagModel extends Core_Model_Super
{
    protected $_data = [
        'tagId'     => null,
        'tagName'   => null,
        'enable'    => 1,
        'lang'      => null,
        'tagType'   => null,
        'tagWeight' => 0,
    ];

    /**
     * Convert tags string to tags array
     *
     * @param $tags
     *
     * @return array
     */
    public static function tagsStringToArray($tags)
    {
        $tags      = mb_strtolower(trim($tags), 'UTF-8');
        $tagsArray = explode(',', $tags);
        $tagsArray = array_unique($tagsArray);

        $filteredTags = [];
        foreach ($tagsArray as $tag) {
            $tag            = trim($tag);
            $filteredTags[] = $tag;
        }

        return $filteredTags;
    }
}