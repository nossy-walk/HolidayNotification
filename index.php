<?php

require_once __DIR__ . '/vendor/autoload.php';

use Carbon\Carbon;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

date_default_timezone_set('Asia/Tokyo');
$dt = Carbon::today();
//一週間前に通知するため「７」
$today = $dt->addDays(7)->format('Y-m-d');

// ------------------グーグルAPIで祝日情報を取得------------------

// 取得したAPIキー
$api_key = $_ENV['GOOGLE_CALENDAR_API_KEY'];
// カレンダーID
$calendar_id = urlencode('japanese__ja@holiday.calendar.google.com');  // Googleの提供する日本の祝日カレンダー
// データの開始日
$start = date('2021-01-01\T00:00:00\Z');
// データの終了日
$end = date('2021-12-31\T00:00:00\Z');

$url = "https://www.googleapis.com/calendar/v3/calendars/" . $calendar_id . "/events?";
$query = [
    'key' => $api_key,
    'timeMin' => $start,
    'timeMax' => $end,
    'maxResults' => 50,
    'orderBy' => 'startTime',
    'singleEvents' => 'true'
];

$holidays = [];
if ($data = file_get_contents($url . http_build_query($query), true)) {
    $data = json_decode($data);
    // $data->itemには日本の祝日カレンダーの"予定"が入ってきます
    foreach ($data->items as $row) {
        // [予定の日付 => 予定のタイトル]
        $holidays[$row->start->date] = $row->summary;
    }
}

// 以上----------------グーグルAPIで祝日情報を取得------------------



if (isset($holidays[$today])) {
    $url = 'https://slack.com/api/chat.postMessage';

    $POST_DATA = [
        'channel' => '#random',
        'text' => $today . 'は' . $holidays[$today] . 'です',
    ];

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, TRUE);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Content-type: application/json; charset=utf-8',
        'Authorization: Bearer ' . $_ENV['OAUTH_ACCESS_TOKEN'],
    ]);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($POST_DATA));

    return curl_exec($curl);
} else {
    echo "何の日でもないよ";
}
