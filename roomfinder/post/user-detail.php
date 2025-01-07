<?php

require_once '../vendor/autoload.php';
require_once '../config.php';
require_once '../process-token.php';

use Pht\Roomfinder\Post;

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_POST['userId'];

    $post = new Post($connect);

    $user = checkToken(getToken());

    if($user['role']['roleName'] == "User") {
        echo $post->userDetail($userId);
    }
    else echo json_encode([
        'status' => false,
        'message' => "Không có quyền truy cập"
    ]);
}

else {
    echo json_encode([
        'status' => false,
        'message' => 'Phương thức truy cập không chính xác'
    ]);
}

?>