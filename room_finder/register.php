<?php

// Register account for User

require 'config.php';
require 'vendor/autoload.php'; // Đường dẫn đến composer

use Firebase\JWT\JWT;


if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "INSERT INTO account (Username, Password, Role) VALUES ('$username', '$password', 'User')";


    try {
        if(mysqli_query($connect, $query)) {

            $queryGet = "SELECT * FROM account WHERE Username = '$username' AND Password = '$password'";

            $data = mysqli_query($connect, $queryGet);
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
                    "message" => "Tạo tài khoản thành công",
                    "token" => $jwt
                ]);
            }

            else {
                echo json_encode([
                    "status" => false,
                    "message" => "Có lỗi xảy ra"
                ]);
            }
            
        }

        else {
            echo json_encode([
                "status" => false,
                "message" => "Tạo tài khoản không thành công"
            ]);
        }
    }
    catch (Exception $e) {
        echo json_encode([
            "status" => false,
            "message" => "Tài khoản đã tồn tại"
        ]);
    }
        
}

mysqli_close($connect);

?>