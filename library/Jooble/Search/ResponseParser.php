<?php

/**
 * Class Jooble_SearchResponse
 *
 * @author Fedor Petryk
 */
class Jooble_Search_ResponseParser
{
    /**
     * @var Jooble_Model_Vacancies
     */
    protected $vacancies;

    public function __construct()
    {
        $this->vacancies = new Jooble_Model_Vacancies();
    }

    /**
     * @param $responseXml
     */
    public function parse($responseXml)
    {
        $xml = new SimpleXMLElement($responseXml);

        $this->vacancies->setPagesCount((int)$xml->general->page_count);

        foreach ($xml->messages->msg as $message) {
            $vacancy = new Jooble_Model_Vacancy();
            $vacancy->setPosition((string)strip_tags($message->position));
            $vacancy->setRegion((string)strip_tags($message->region));
            $vacancy->setDescription((string)strip_tags($message->desc));
            $vacancy->setLastUpdate((string)strip_tags($message->updated));
            $vacancy->setSalary((string)strip_tags($message->salary));
            $vacancy->setSourceUrl((string)strip_tags($message->sources->source->url));
            $vacancy->setSource((string)strip_tags($message->sources->source->name));
            $vacancy->setPrice((string)strip_tags($message->price));
            $vacancy->setExternalId((int)strip_tags($message['id']));

            $this->vacancies->addVacancy($vacancy);
        }
    }

    /**
     * @return Jooble_Model_Vacancies
     */
    public function getVacancies()
    {
        return $this->vacancies;
    }
}