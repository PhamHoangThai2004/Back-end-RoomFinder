<?php

// Check code token

require_once '../vendor/autoload.php';
require_once '../config.php';

use Pht\Roomfinder\Authentication;

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'GET') {

    $auth = new Authentication($connect);
    echo $auth->checkToken(getToken());

}
else {
    echo json_encode([
        'status' => false,
        'message' => 'Phương thức truy cập không chính xác'
    ]);
}

function getToken() {
    $headers = getallheaders();

    if (isset($headers['Authorization'])) {
        $token = str_replace('Bearer ', '', $headers['Authorization']);
        return $token;
    }
    else {
        return "Không tìm thấy token";
    }
}


?>