<?php

require_once '../vendor/autoload.php';
require_once '../config.php';

use Pht\Roomfinder\Authentication;

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $role        = $data['role'];

    $email       = $data['email'];
    $password    = $data['password'];
    $roleName    = $role['roleName'];
    $name        = $data['name'];
    $phoneNumber = $data['phoneNumber'];

    $auth = new Authentication($connect);
    echo $auth->register($email, $password, $roleName, $name, $phoneNumber);

}
else {
    echo json_encode([
        'status' => false,
        'message' => 'Phương thức truy cập không chính xác'
    ]);
}

?>
