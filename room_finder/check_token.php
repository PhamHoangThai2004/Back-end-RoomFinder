<?php

// Check token

require 'vendor/autoload.php'; // Đường dẫn tương đối đến file autoload.php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$key = "login_account";


if(isset($_POST['token'])) {
    $jwt = $_POST['token'];
    try {
        // Giải mã
        // $decoded = JWT::decode($jwt, $key, ['HS256']);
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        
        echo json_encode([
            "status" => true,
            "message" => "Token valid",
            "data" => (array) $decoded
        ]);

    } catch (Exception $e) {
        echo json_encode([
            "status" => false,
            "message" => "Token is invalid: " . $e->getMessage()
        ]);
    }
}

else {
    echo json_encode([
        "status" => false,
        "message" => "No token provided"
    ]);
}

?>