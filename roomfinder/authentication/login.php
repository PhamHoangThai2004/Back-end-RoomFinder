<?php

require_once '../vendor/autoload.php'; 
require_once '../config.php'; 

use Pht\Roomfinder\Authentication;

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $email    = $data['email'];
    $password = $data['password'];

    $auth = new Authentication($connect);

    echo $auth->login($email, $password);
}

?>