<?php

require_once('Search/RequestBuilder.php');
require_once('Search/ResponseParser.php');
require_once('Model/Vacancy.php');
require_once('Model/Vacancies.php');
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
     * @var Jooble_Search_RequestBuilder
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
     * Jooble api key
     * @var string
     */
    protected $apiKey;

    /**
     * @var array
     */
    protected $headers = ['Content-Type: application/x-www-form-urlencoded'];

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
        $this->apiKey  = $this->config['apiKey'];
        $this->logDir  = $this->config['logDir'];
    }

    /**
     * @param array $tags Search tags
     * @param int   $page
     *
     * @return Jooble_Model_Vacancies | bool
     */
    public function search(array $tags, $page = 1)
    {
        try {
            $this->buildRequest($tags, $page);
            $response = $this->sendRequest();
            $this->parseResponse($response);

            if ($this->responseParser) {
                return $this->responseParser->getVacancies();
            }

            return [];
        } catch (Exception $e) {
            error_log('Error occurred: ' . $e->getMessage() . " in " . __METHOD__);

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
        $this->request = new Jooble_Search_RequestBuilder($tags, $this->regions, $page);
        $this->request->build();
    }

    protected function sendRequest()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . "" . $this->apiKey);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->request->getEncoded());
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($this->config['logEnabled']) {
            file_put_contents($this->getLogDir() . $this->getLogFileName('request'), (string)$this->request->getEncoded());
            file_put_contents($this->getLogDir() . $this->getLogFileName('response'), (string)$response);
        }

        return $response;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getLogFileName($name)
    {
        return sprintf("{$name}_%s.json", date('Y-m-d_h_m_i'));
    }

    /**
     * @return string
     */
    protected function getLogDir()
    {
        return __DIR__ . '/' . $this->logDir;
    }

    /**
     * @param String $response
     *
     * @return array
     */
    protected function parseResponse(&$response)
    {
        try {
            $this->responseParser = new Jooble_Search_ResponseParser(new Jooble_Model_Vacancies());
            $this->responseParser->parse($response);
        } catch (Jooble_Exception_InvalidResponse $e) {
            error_log("Error: " . $e->getMessage() . " in " . __METHOD__);

            return [];
        }
    }

    /**
     * @param boolean $logEnabled
     */
    public function setLogEnabled($logEnabled)
    {
        $this->logEnabled = $logEnabled;
    }
}
