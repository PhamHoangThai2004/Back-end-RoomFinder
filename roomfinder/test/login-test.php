<?php

// Login by email and password

require_once '../vendor/autoload.php';
require_once '../config.php';

use Pht\Roomfinder\Test;

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $email    = $data['email'];
    $password = $data['password'];

    $auth = new Test($connect);

    echo $auth->login($email, $password);

}
else {
    echo json_encode([
        'status' => false,
        'message' => 'Phương thức truy cập không chính xác'
    ]);
}

?>