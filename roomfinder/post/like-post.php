<?php

require_once '../vendor/autoload.php';
require_once '../config.php';
require_once '../process-token.php';

use Pht\Roomfinder\Post;

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    $postId = $_GET['postId'];
    $isLiked = filter_var($_GET['isLiked'], FILTER_VALIDATE_BOOLEAN);

    $post = new Post($connect);

    $user = checkToken(getToken());

    if($user['role']['roleName'] == "User") {
        echo $post->likePost($user['userID'], $postId, $isLiked);
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