<?php

namespace OpenSB;

global $twig, $database;

use SquareBracket\UnorganizedFunctions;

$whereRatings = UnorganizedFunctions::whereRatings();

$submissions = $database->fetchArray($database->query("SELECT v.* FROM videos v WHERE v.video_id NOT IN (SELECT submission FROM takedowns) AND $whereRatings ORDER BY RAND() LIMIT 24"));
$submissions_recent = $database->fetchArray($database->query("SELECT v.* FROM videos v WHERE v.video_id NOT IN (SELECT submission FROM takedowns) AND $whereRatings ORDER BY v.time DESC LIMIT 24"));
$news_recent = $database->fetchArray($database->query("SELECT j.* FROM journals j WHERE j.is_site_news = 1 ORDER BY j.date DESC LIMIT 3"));

$data = [
    "submissions" => UnorganizedFunctions::makeSubmissionArray($database, $submissions),
    "submissions_new" => UnorganizedFunctions::makeSubmissionArray($database, $submissions_recent),
    "news_recent" => $news_recent,
];

echo $twig->render('index.twig', [
    'data' => $data,
]);
