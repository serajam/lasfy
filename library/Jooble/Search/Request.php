<?php

/**
 * Class Jooble_Search_Request
 *
 * @author Fedor Petryk
 * @deprecated 
 */
class Jooble_Search_Request
{
    /**
     * Xml request object
     *
     * @var SimpleXMLElement
     */
    protected $xml;

    /**
     * Encoding of xml request
     *
     * @var String
     */
    protected $encoding;

    /**
     * Offers count per page
     *
     * @var int
     */
    protected $offersCount;

    /**
     * Number of offers sources
     *
     * @var int
     */
    protected $sourcesCount;

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
    public function __construct(array $tags, $encoding, $offersCount, $sourcesCount, &$regions, $page)
    {
        $this->xml          = new SimpleXMLElement('<?xml version="1.0"?><request></request>');
        $this->tags         = $tags;
        $this->encoding     = $encoding;
        $this->offersCount  = $offersCount;
        $this->sourcesCount = $sourcesCount;
        $this->regions      = $regions;
        $this->page         = $page;
    }

    /**
     * Build xml request
     *
     * @return bool
     */
    public function build()
    {
        $this->xml->addChild('preferred_encoding', $this->encoding);
        $keywordsXml = $this->xml->addChild('keywords');
        $keywordsXml->addAttribute('only_position', 1);

        if ($this->tags) {
            // keywords and group
            $keywordsXml->and = implode(',', $this->tags);

            foreach ($this->tags as $tag) {
                $tag = mb_ucfirst($tag);
                if (in_array($tag, $this->regions)) {
                    $regionXml = $this->xml->addChild('region');
                    $regionXml->addChild('city', "<![CDATA [{$tag}]]>");
                    break;
                }
            }
        }

        $salaryXml = $this->xml->addChild('salary');
        $salaryXml->addAttribute('required', 0);

        $dateXml = $this->xml->addChild('date', 'last7day');
        $dateXml->addAttribute('type', 'actual');

        $this->xml->addChild('page_no', $this->page);
        $this->xml->addChild('msg_count', $this->offersCount);
        $this->xml->addChild('src_count', $this->sourcesCount);

        return true;
    }

    /**
     * @return SimpleXMLElement
     */
    public function getXml()
    {
        return $this->xml;
    }

    /**
     * @return String
     */
    public function getRawXmlRequest()
    {
        return $this->xml->asXML();
    }
}
