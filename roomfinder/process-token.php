<?php

require_once '../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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

$token = getToken();

function checkToken($token) {
    if ($token) {
        $key = 'login_account_pht';

        try {
            $decode = JWT::decode($token, new Key($key, 'HS256'));

            return json_decode(json_encode($decode->user), true);
        } catch (Exception $e) {
            return  'Mã token không hợp lệ';
        }
    } else {
        http_response_code(401);
        return 'Không tìm thấy mã xác thực';
    }

}


