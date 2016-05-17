<?php

// do some testing with jooble api

$tags = ['php', 'Киев'];

require_once('../ApiConnector.php');
require_once('../ApiConnector.php');
require_once ('../../functions.php');

$connector = new Jooble_ApiConnector('../config.ini');
$connector->setLogEnabled(true);
$vacancies = $connector->search($tags, 1);

print_r($vacancies);
