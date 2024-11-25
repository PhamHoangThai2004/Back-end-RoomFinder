<?php

require_once '../vendor/autoload.php';
require_once '../config.php';
require_once '../process-token.php';

use Pht\Roomfinder\Post;

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    $keySearch = $_GET['keySearch'];

    if($keySearch) {

        $post = new Post($connect);

        $numeric = extractNumeric($keySearch);
        // echo $numeric;

        $keyword = extractText($keySearch);
        // echo $keyword;

        // echo $post->listSearch($keyword, $numeric);

        $user = checkToken(getToken());

        if($user['role']['roleName'] == 'User') {
            echo $post->listSearch($keyword, $numeric);
        }
        else echo json_encode([
            'status' => false,
            'mesage' => "Không có quyền truy cập"
        ]);
    }

}
else {
    echo json_encode([
        'status' => false,
        'message' => 'Phương thức truy cập không chính xác'
    ]);
}

function extractNumeric($string) {
    preg_match('/\d+(\.\d+)?/', $string, $matches);
    return isset($matches[0]) ? (float)$matches[0] : null;
}

function extractText($str) {
    // Biểu thức chính quy loại bỏ các ký tự không phải chữ (số và dấu câu)
    $result = preg_replace('/[0-9\-]+/', '', $str); // Loại bỏ số và dấu "-"

    // Trả về chuỗi sau khi loại bỏ số
    return trim($result);
}



?>