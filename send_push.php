<?php
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
        'publicKey' => 'TU_VAPID_PUBLICA',
        'privateKey' => 'TU_VAPID_PRIVADA'
    ]
];

$webPush = new WebPush($auth);

$subscription = Subscription::create($data['subscription']);

$webPush->queueNotification(
    $subscription,
    json_encode($data['payload'], JSON_UNESCAPED_UNICODE)
);

foreach ($webPush->flush() as $report) {
    if ($report->isSuccess()) {
        echo 'OK';
        exit;
    }
}

http_response_code(500);
echo 'FAILED';
