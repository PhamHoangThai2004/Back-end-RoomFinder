<?php

require_once '../vendor/autoload.php';
require_once '../config.php';
require_once '../process-token.php';

use Pht\Roomfinder\Post;

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    $post = new Post($connect);

    $role = checkToken(getToken());

    if($role == "User") {
        echo $post->listHome();
    }
    else {
        echo $role;
    }
}

else {
    echo json_encode([
        'status' => false,
        'message' => 'Phương thức truy cập không chính xác'
    ]);
}

?>