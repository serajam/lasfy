<?php

require_once('Search/Request.php');
require_once('Search/ResponseParser.php');
require_once('Model/Vacancy.php');
require_once('Exception/InvalidResponse.php');

/**
 * Class Jooble_ApiConnector
 *
 * @author Fedor Petryk
 */
class Jooble_ApiConnector
{
    /**
     * @var Zend_Config_Ini
     */
    protected $config;

    protected $regions;

    /**
     * @var Jooble_Search_Request
     */
    protected $request;

    /**
     * @var Jooble_Search_ResponseParser
     */
    protected $responseParser;

    /**
     * Do request & response logging
     *
     * @var bool
     */
    protected $logEnabled = false;

    /**
     * Path to where save logs
     *
     * @var String
     */
    protected $logDir;

    /**
     * Jooble Api Url
     *
     * @var String
     */
    protected $apiUrl;

    /**
     * @param $configPath
     */
    public function __construct($configPath = null)
    {
        if (!$configPath) {
            $configPath = 'config.ini';
        }

        $this->config  = parse_ini_file($configPath);
        $this->regions = parse_ini_file('regions.ini')['regions'];
        $this->apiUrl  = $this->config['apiUrl'];
        $this->logDir  = $this->config['logDir'];
    }

    /**
     * @param array $tags Search tags
     * @param int   $page
     *
     * @return Jooble_Model_Vacancies | bool
     */
    public function search(array $tags, $page)
    {
        try {
            $this->buildRequest($tags, $page);
            $responseXml = $this->sendRequest();
            $this->parseResponse($responseXml);

            if ($this->responseParser) {
                return $this->responseParser->getVacancies();
            }

            return [];
        } catch (Exception $e) {
            error_log('Error occurred: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Build & send request. Parse response xml into model
     *
     * @param array $tags
     * @param int   $page
     *
     * @return array
     */
    protected function buildRequest(array $tags, $page)
    {

        $this->request = new Jooble_Search_Request(
            $tags, $this->config['encoding'],
            $this->config['offersCount'],
            $this->config['sourcesCount'],
            $this->regions,
            $page
        );
        $this->request->build();
    }

    protected function sendRequest()
    {
        $opts = [
            'http' =>
                [
                    'method'  => 'POST',
                    'header'  => "Content-Type: text/xml\r\n"
                        . 'Accept-Encoding: gzip,deflate' . "\r\n",
                    'content' => $this->request->getXml()->asXML(),
                    'timeout' => 60
                ]
        ];

        $responseXml = @file_get_contents($this->apiUrl, false, stream_context_create($opts));

        if ($this->config['logEnabled']) {
            file_put_contents(
                __DIR__ . '/' . $this->logDir . sprintf('request_%s.xml', date('Y-m-d_h_m_i')),
                $this->request->getRawXmlRequest()
            );
            file_put_contents(__DIR__ . '/' . $this->logDir . sprintf('response_%s.xml', date('Y-m-d_h_m_i')), $responseXml);
        }

        return $responseXml;
    }

    /**
     * @param String $responseXml
     *
     * @return array
     */
    protected function parseResponse(&$responseXml)
    {
        $this->responseParser = new Jooble_Search_ResponseParser();
        $this->responseParser->parse($responseXml);
    }

    /**
     * @param boolean $logEnabled
     */
    public function setLogEnabled($logEnabled)
    {
        $this->logEnabled = $logEnabled;
    }
}