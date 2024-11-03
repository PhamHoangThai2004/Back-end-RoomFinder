<?php

require_once '../vendor/autoload.php';
require_once '../config.php';

use Pht\Roomfinder\Authentication;

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $otp   = $_POST['otp'];

    $auth = new Authentication($connect);
    echo $auth->checkOTP($email, $otp);

}
else {
    echo json_encode([
        'status' => false,
        'message' => 'Phương thức truy cập không chính xác'
    ]);
}

?>
