<?php

require_once '../vendor/autoload.php';
require_once '../config.php';
require_once '../process-token.php';

use Pht\Roomfinder\Category;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $category = new Category($connect);

    // echo $category->listCategory();

    $user = checkToken(getToken());

    if ($user['role']['roleName'] == "User") {
        echo $category->listCategory();
    }
}

else {
    echo json_encode([
        'status' => false,
        'message' => 'Phương thức truy cập không chính xác'
    ]);
}

?>