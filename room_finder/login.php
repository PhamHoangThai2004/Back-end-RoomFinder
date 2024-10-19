<?php

// Check login vào Account

require 'config.php';
require 'vendor/autoload.php'; // Đường dẫn tương đối đến file autoload.php

use Firebase\JWT\JWT;

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM account WHERE Username = '$username' AND Password = '$password'";

    $data = mysqli_query($connect, $query);

    if($row = mysqli_fetch_assoc($data)) {
        $key = "login_account";
        $payload = [
            'iat' => time(),
            'exp' => time() + (60 * 60),
            'accountID' => $row['AccountID'],
            'role' => $row['Role']
        ];

        $jwt = JWT::encode($payload, $key, 'HS256');

        echo json_encode([
            "status" => true,
            "message" => "Đăng nhập thành công",
            "token" => $jwt
        ]);
    }
    else {
        echo json_encode([
            "status" => false,
            "message" => "Tên tài khoản hoặc mật khẩu không chính xác"
        ]);
    }
        
}

mysqli_close($connect);

?>