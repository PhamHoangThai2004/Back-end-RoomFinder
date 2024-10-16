<?php

// Check login vào Account

require 'config.php';
require 'vendor/autoload.php'; // Đường dẫn tương đối đến file autoload.php

use Firebase\JWT\JWT;

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    $username = $_GET['username'];
    $password = $_GET['password'];

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
            "message" => "Login success",
            "token" => $jwt
        ]);
    }
    else {
        echo json_encode([
            "status" => false,
            "message" => "Invalid username or password"       
        ]);
    }
        
}

mysqli_close($connect);

?>