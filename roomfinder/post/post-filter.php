<?php

require_once '../vendor/autoload.php';
require_once '../config.php';
require_once '../process-token.php';

use Pht\Roomfinder\Post;

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    $categoryName = $_GET['categoryName'];
    $area = $_GET['area'];
    $minPrice = $_GET['minPrice'];
    $maxPrice = $_GET['maxPrice'];
    $minAcreage = $_GET['minAcreage'];
    $maxAcreage = $_GET['maxAcreage'];

    $post = new Post($connect);

    $user = checkToken(getToken());

    if($user['role']['roleName'] == "User") {
        echo $post->postFilter($categoryName, $area, $minPrice, $maxPrice, $minAcreage, $maxAcreage);
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