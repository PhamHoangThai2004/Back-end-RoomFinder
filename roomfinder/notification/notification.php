<?php
require_once '../vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

// Khởi tạo Firebase
$factory = (new Factory)->withServiceAccount('D:\xampp\htdocs\roomfinder\firebase-key.json');
$messaging = $factory->createMessaging();

// Tạo nội dung thông báo
$message = CloudMessage::fromArray([
    'topic' => 'all',
    'data' => [
        'title' => 'Bài đăng mới trên Room Finder!',
        'message' => 'Có một phòng mới vừa được đăng, hãy kiểm tra ngay!',
        'action' => 'send_notification'
    ],
]);

// Gửi thông báo
$messaging->send($message);

echo "Thông báo đã gửi thành công!";

?>