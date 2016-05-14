<?php

/**
 * Job_Tags_TagsService
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2015 Studio 105 (http://105.in.ua)
 */
class Job_Tags_TagsService extends Core_Service_Editor
{
    /**
     * Tags mapper class
     * @var String
     */
    protected $_mapperName = 'Job_Tags_TagsMapper';

    /**
     * @var Job_Tags_TagsMapper
     */
    protected $_mapper;

    /**
     * @var String
     */
    protected $_validatorName = 'Job_Tags_TagForm';

    public function getTags($tag)
    {
        $tag = filter_var($tag, FILTER_SANITIZE_STRING);
        $tag = filter_var($tag, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $tag = mb_strtolower($tag);

        if (empty($tag)) {
            return false;
        }

        $this->_jsonData = $this->_mapper->getTags($tag);

        return true;
    }

    public function getTagsCollection($filters = false)
    {
        $form = $this->getFormLoader()->getForm('TagSearchForm');
        if (!$form->isValid($filters)) {
            $this->setError(Core_Messages_Message::getMessage(300));

            return false;
        }

        $tagNames = [];
        if (isset($filters['searchTags'])) {
            $tagNames = explode(' ', $filters['searchTags']);;
        }

        $page = 1;
        if (isset($filters['page'])) {
            $page = $filters['page'];
        }

        return $this->_mapper->getTagsByFilter($tagNames, $page);
    }
}