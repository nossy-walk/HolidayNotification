<?php

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$url = 'https://slack.com/api/chat.postMessage';

$POST_DATA = [
    'channel' => '#random',
    'text' => 'おかわり自由',
];

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_POST, TRUE);
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    'Content-type: application/json; charset=utf-8',
    'Authorization: Bearer ' . $_ENV['OAUTH_ACCESS_TOKEN'],
]);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($POST_DATA));

return curl_exec($curl);
