<?php
require_once '../vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

function sendNotification($title, $mess, $id) {

    // Khởi tạo Firebase
    $factory = (new Factory)->withServiceAccount('D:\xampp\htdocs\roomfinder\firebase-key.json');
    $messaging = $factory->createMessaging();

    // Tạo nội dung thông báo
    $message = CloudMessage::fromArray([
        'topic' => 'all',
        'data' => [
            'title' => $title,
            'message' => $mess,
            'postId'=> $id,
            'action' => 'send_notification'
        ],
    ]);

    // Gửi thông báo
    $messaging->send($message);
}

?>