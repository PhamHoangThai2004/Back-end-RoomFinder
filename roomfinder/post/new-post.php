<?php

// Create a new post

require_once '../vendor/autoload.php';
require_once '../config.php';
require_once '../process-token.php';

use Pht\Roomfinder\Category;
use Pht\Roomfinder\Images;
use Pht\Roomfinder\Location;
use Pht\Roomfinder\Post;

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $post = new Post($connect);

    $user = checkToken(getToken());

    if($user['role']['roleName'] == "User") {
        echo $post->newPost($data, $user['userId']);
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