<?php

require_once '../vendor/autoload.php';
require_once '../config.php';
require_once '../process-token.php';

use Pht\Roomfinder\Authentication;

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $oldPassword   = $_POST['oldPassword'];
    $newPassword   = $_POST['newPassword'];

    $auth = new Authentication($connect);

    $user = checkToken(getToken());

    if($user['role']['roleName'] == "User" || $user['role']['roleName'] == "Admin") {
        echo $auth->checkAccount($user['userId'], $oldPassword, $newPassword);
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
