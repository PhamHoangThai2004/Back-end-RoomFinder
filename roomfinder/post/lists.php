<?php

require_once '../vendor/autoload.php';
require_once '../config.php';
require_once '../process-token.php';

use Pht\Roomfinder\Post;

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    $typeSearch = $_GET['typeSearch'];
   
    $post = new Post($connect);


    $user = checkToken(getToken());

    if($user['role']['roleName'] == 'User') {
        if ($typeSearch == 1) {
            echo $post->listRandom($typeSearch);
        } else if ($typeSearch == 2) {
            $area = $_GET['area'];
            echo $post->listBonus($area);
        } else {
            $area = $_GET['area'];
            echo $post->listRoommate($area);
        }
    }
    else echo json_encode([
        'status' => false,
        'mesage' => "Không có quyền truy cập"
    ]);

}
else {
    echo json_encode([
        'status' => false,
        'message' => 'Phương thức truy cập không chính xác'
    ]);
}

?>