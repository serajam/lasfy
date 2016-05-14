<?php

require_once('../Model/Vacancy.php');

$xml = file_get_contents('result.xml');
$xml = new SimpleXMLElement($xml);

$vacancies = [];

foreach ($xml->messages->msg as $message) {
    $vacancy = new Jooble_Model_Vacancy();
    $vacancy->setPosition((string)$message->position);
    $vacancy->setRegion((string)$message->region);
    $vacancy->setDescription((string)$message->desc);
    $vacancy->setLastUpdate((string)$message->updated);
    $vacancy->setSalary((string)$message->salary);
    $vacancy->setSourceUrl((string)$message->sources->source->url);
    $vacancy->setSource((string)$message->sources->source->name);
    $vacancy->setPrice((string)$message->price);
    $vacancy->setExternalId((int)$message['id']);

    $vacancies[] = $vacancy;
}

print_r($vacancies[0]);