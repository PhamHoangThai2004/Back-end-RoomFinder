<?php

// Check code token

require_once '../vendor/autoload.php';
require_once '../config.php';

use Pht\Roomfinder\Authentication;

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];

    $auth = new Authentication($connect);
    echo $auth->checkToken($token);

}
else {
    echo json_encode([
        'status' => false,
        'message' => 'Phương thức truy cập không chính xác'
    ]);
}

?>