<?php

require_once '../vendor/autoload.php';
require_once '../config.php';
require_once '../process-token.php';

use Pht\Roomfinder\Authentication;

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);

    $auth = new Authentication($connect);

    // echo $auth->changeInformation($userID, $name, $phoneNumber);

    $user = checkToken(getToken());

    if($user['role']['roleName'] == "User" || $user['role']['roleName'] == "Admin") {
        echo $auth->changeInformation($data, $user['userID'], $user['role']['roleName']);
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
