<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['subscription'], $data['payload'])) {
    http_response_code(400);
    exit('Invalid request');
}

$auth = [
    'VAPID' => [
        'subject' => 'mailto:admin@tukity.com',
        'publicKey' => 'BGac6XcWxJo4mIa7CCynt6n2Jx80V1BXVNrxMSrbLymmZ4hr7tVzpAauocv4JtIiGmLTOaHFktYRmQBFdeooH9E',
        'privateKey' => 'NwZ2XPptUFk7OvNVH1Ss2eH8MQcxHCMrMi7dgkiiqHw'
    ]
];

$webPush = new WebPush($auth);
$subscription = Subscription::create($data['subscription']);

$webPush->queueNotification($subscription, json_encode($data['payload']));

foreach ($webPush->flush() as $report) {
    if ($report->isSuccess()) {
        echo 'OK';
        exit;
    }
}

http_response_code(500);
echo 'FAILED';
