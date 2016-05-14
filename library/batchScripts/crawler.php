<?php

function jobCategoriesRabotaUa()
{
    if (!file_exists('rabotaua_cats.txt')) {
        $url                = 'http://rabota.ua/%D0%B2%D0%B0%D0%BA%D0%B0%D0%BD%D1%81%D0%B8%D0%B8';
        $categoriesPageHtml = file_get_contents($url);
        file_put_contents('rabotaua_cats.txt', $categoriesPageHtml);
    }

    preg_match_all(
        '/(?:<a data\-id="[0-9]*" href=\'(.*)\'>).*\s*(?:<span)/Um',
        file_get_contents('rabotaua_cats.txt'),
        $matchesCats
    );

    $i = 0;
    foreach ($matchesCats[1] as $key => $m) {
        if (!file_exists("rabotaua_cats{$key}.txt")) {
            sleep(15);
            $url                = 'http://rabota.ua' . $m;
            $categoriesPageHtml = file_get_contents($url);
            file_put_contents("rabotaua_cats{$key}.txt", $categoriesPageHtml);
        }

        $content = file_get_contents("rabotaua_cats{$key}.txt");
        preg_match(
            '/(?:<ul id="tags")(.*)\s*(?:<\/ul)/Um',
            $content,
            $matches
        );

        preg_match_all(
            '/(?:<a href=".*">)(.*)\s*(?:<\/a)/Um',
            $matches[0],
            $matches
        );

        $sql = 'INSERT INTO Tags (tagName, enable, lang) VALUES ';
        foreach ($matches[1] as $job) {
            $job = mb_strtolower($job);
            $sql .= sprintf('("%s", %s, %s),', $job, 1, 2);
        }
        file_put_contents("rabotaua_cats{$key}.sql", $sql);

        echo 'cat ' . $key . ' processed of ' . count($matchesCats[1]), PHP_EOL;

        if ($i > 10) {
            sleep(60);
            $i = 0;
        }
        $i++;
    }
}

function jobTypesRabotaUa()
{
    if (!file_exists('rabotaua_jobs.txt')) {
        $url                = 'http://rabota.ua/zapros';
        $categoriesPageHtml = file_get_contents($url);
        file_put_contents('rabotaua_jobs.txt', $categoriesPageHtml);
    }
    preg_match_all(
        '/(?:<a class="rua-p-c-default".*>)(.*)(?:<\/a><\/td>)/U',
        file_get_contents('rabotaua_jobs.txt'),
        $matches
    );

    $words = [];
    $sql   = 'INSERT INTO Tags (tagName, enable, lang) VALUES ';
    foreach ($matches[1] as $i => $job) {
        $parts = explode(' ', $job);
        foreach ($parts as $p) {
            if (mb_strlen($p) < 4) {
                continue;
            }
            $words[$i][] = $p;
        }

        $job = mb_strtolower($job);
        $sql .= sprintf('("%s", %s, %s),', $job, 1, 2);
    }
    file_put_contents('jobs_types.json', json_encode($words));
    file_put_contents('jobs_types.sql', $sql);

    print_r($words);
}
