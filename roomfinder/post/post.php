<?php

require_once '../vendor/autoload.php';
require_once '../config.php';
require_once '../process-token.php';
require_once '../notification/notification.php';

use Pht\Roomfinder\Post;

header('Content-Type: application/json');

$post = new Post($connect);
$user = checkToken(getToken());

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $postId = $_GET['postId'];

    if($user['role']['roleName'] == "User" || $user['role']['roleName'] == "Admin") {
        echo $post->postDetail($user['userId'], $postId);
    }
    else echo json_encode([
        'status' => false,
        'message' => "Không có quyền truy cập"
    ]);
}

else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if($user['role']['roleName'] == "User") {
        echo $post->newPost($data, $user['userId']);
    } 
    else echo json_encode([
        'status' => false,
        'message' => "Không có quyền truy cập"
    ]);
}

else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);

    if($user['role']['roleName'] == "User") {
        echo $post->updatePost($data, $user['userId']);
    }
    else echo json_encode([
        'status' => false,
        'message' => "Không có quyền truy cập"
    ]);
}

else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $uri = $_SERVER['REQUEST_URI'];
    $postId = explode('/', $uri)[4];

    if($user['role']['roleName'] == "User" || $user['role']['roleName'] == "Admin") {
        echo $post->deletePost($postId, $user['userId']);
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