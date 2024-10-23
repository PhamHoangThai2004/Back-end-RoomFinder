<?php

namespace Pht\Roomfinder;

require_once '../vendor/autoload.php'; 
require_once '../config.php'; 

class Authentication {

    private $connect;

    public function __construct($_connect)
    {
        $this->connect = $_connect;
    }

    public function login($email, $password) {
        $query = $this->connect->prepare("SELECT *FROM user WHERE Email = ? AND Password = ?");
        $query->execute([$email, $password]);
        $user = $query->fetch();

        if($user) {
            return json_encode([
                'status' => true,
                'message' => 'Đăng nhập thành công',
                'userID' => $user['UserID']
            ]);
        }
        else {
            return json_encode([
                'status' => false,
                'mesage' => 'Email hoặc mật khẩu không chính xác'
            ]);
        }
    }

}

?>