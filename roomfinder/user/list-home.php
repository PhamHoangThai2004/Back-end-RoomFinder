<?php

require_once '../vendor/autoload.php';
require_once '../config.php';

use Pht\Roomfinder\Post;

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    $post = new Post($connect);
    // echo json_encode($post->list());
    echo $post->listHome();
}

else {
    echo json_encode([
        'status' => false,
        'message' => 'Phương thức truy cập không chính xác'
    ]);
}

?>