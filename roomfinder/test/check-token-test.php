<?php

// Check code token

require_once '../vendor/autoload.php';
require_once '../config.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Pht\Roomfinder\Test;

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'GET') {

     $auth = new Test($connect);

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
        return "Không tìm thấy";
    }
}

function checkToken($token) {

    if ($token) {
        echo "Token: " . $token;
        $key = 'login_account_pht';

        try {
            $decode = JWT::decode($token, new Key($key, 'HS256'));

            echo json_encode([
                'status' => true,
                'message' => 'Mã token hợp lệ',
                'data' => (array) $decode
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => 'Mã token không hợp lệ'
            ]);
        }
    } else {
        http_response_code(401);
        echo json_encode(['message' => 'Unauthorized']);
    }
}

?>