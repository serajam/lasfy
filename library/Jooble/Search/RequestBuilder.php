<?php

/**
 * Class Jooble_Search_RequestBuilder
 */
class Jooble_Search_RequestBuilder
{
    const FIELD_KEYWORDS = 'keywords';
    const FIELD_LOCATION = 'location';
    const FIELD_PAGE = 'page';

    /**
     * @var array
     */
    protected $requestBody = [];

    /**
     * Search Tags
     *
     * @var array
     */
    protected $tags;

    /**
     * Array of provider regions
     *
     * @var array
     */
    protected $regions;

    /**
     * @var int
     */
    protected $page;

    /**
     * @param array  $tags
     * @param String $encoding
     * @param String $offersCount
     * @param int    $sourcesCount
     * @param array  $regions
     * @param int    $page
     */
    public function __construct(array $tags, &$regions, $page)
    {
        $this->tags    = $tags;
        $this->regions = $regions;
        $this->page    = $page;
    }

    /**
     * Build xml request
     *
     * @return bool
     */
    public function build()
    {
        foreach ($this->tags as $key => $tag) {
            $tag = mb_ucfirst($tag);
            if (in_array($tag, $this->regions)) {
                $this->requestBody[self::FIELD_LOCATION] = $tag;
                unset($this->tags[$key]);
                break;
            }
        }
        $this->requestBody[self::FIELD_KEYWORDS] = implode(' ', $this->tags);
        $this->requestBody[self::FIELD_PAGE]     = "{$this->page}";

        return true;
    }

    /**
     * @return SimpleXMLElement
     */
    public function getBody()
    {
        return $this->requestBody;
    }

    /**
     * @return String
     */
    public function getEncoded()
    {
        return json_encode($this->requestBody);
    }
}
