<?php

require_once '../vendor/autoload.php';
require_once '../config.php';
require_once '../process-token.php';

use Pht\Roomfinder\Authentication;

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $avatar = $_POST["avatar"];

    $auth = new Authentication($connect);

    $user = checkToken(getToken());

    if($user['role']['roleName'] == "User") {
        echo $auth->changeAvatar($user['userId'], $avatar);
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
