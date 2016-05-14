<?php

// do some testing with jooble api

$tags = ['php'];

require_once('../ApiConnector.php');

$connector = new Jooble_ApiConnector('../config.ini');
$connector->setLogEnabled(true);
$vacancies = $connector->search($tags);

print_r($vacancies[0]);