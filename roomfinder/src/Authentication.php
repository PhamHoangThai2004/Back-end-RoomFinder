<?php

namespace Pht\Roomfinder;

require_once '../vendor/autoload.php'; 
require_once '../phpmailer/Exception.php';
require_once '../phpmailer/PHPMailer.php';
require_once '../phpmailer/SMTP.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Authentication {

    private $connect;
    private $key = 'login_account_pht';

    public function __construct($_connect)
    {
        $this->connect = $_connect;
    }

    public function login($email, $password) {
        $query = $this->connect->prepare("SELECT *FROM user WHERE Email = ? AND Password = ?");
        $query->execute([$email, $password]);
        $user = $query->fetch();

        if($user) {
            $roleName = $this->getRole($user['RoleID']);
            if($roleName == 'Not found') {
                return json_encode([
                    'status' => false,
                    'message' => 'Không tìm thấy roleName',
                ]);
            }
            else {
                $payload = [
                    'iat' => time(),
                    'exp' => time() + (60 * 60),
                    'userID' => $user['UserID'],
                    'email' => $user['Email'],
                    'roleName' => $roleName,
                    'name' => $user['Name'],
                    'phoneNumber' => $user['PhoneNumber']
                ];

                $jwt = JWT::encode($payload, $this->key, 'HS256');

                return json_encode([
                    'status' => true,
                    'message' => 'Đăng nhập thành công',
                    'token' => $jwt
                ]);
            }
           
        }
        else {
            return json_encode([
                'status' => false,
                'message' => 'Email hoặc mật khẩu không chính xác'
            ]);
        }
    }

    public function checkToken($jwt) {
        try {
            $decode = JWT::decode($jwt, new Key($this->key, 'HS256'));

            return json_encode([
                'status' => true,
                'message' => 'Mã token hợp lệ',
                'data' => (array) $decode
            ]);
        } catch (Exception $e) {
            return json_encode([
                'status' => false,
                'message' => 'Mã token không hợp lệ'
            ]);
        }
    }

    public function register($email, $password, $roleID) {
        $query = $this->connect->prepare("SELECT * FROM user WHERE Email = ?");
        $query->execute([$email]);
        $row = $query->rowCount();
        if($row == 0) {
            $otp = rand(100000, 999999);
            $query = $this->connect->prepare("INSERT INTO temporary(Email, Password, RoleID, OTP) VALUES (?, ?, ?, ?)");
            $query->execute([$email, $password, $roleID, $otp]);

            return $this->sendEmail($email, $otp);

        }
        else {
            return json_encode([
                'status' => false,
                'message' => 'Tên email đã được sử dụng'
            ]);
        }
    }

    public function checkOTP($email, $otp) {
        $query = $this->connect->prepare("SELECT * FROM temporary WHERE Email = ? AND OTP = ?");
        $query->execute([$email, $otp]);
        $row = $query->rowCount();
        if($row != 0) {
            $user = $query->fetch();
            $query = $this->connect->prepare("DELETE FROM temporary WHERE Email = ?");
            $query->execute([$email]);

            $query = $this->connect->prepare("INSERT INTO user(Email, Password, RoleID) VALUES (?, ?, ?)");
            $query->execute([$user['Email'], $user['Password'], $user['RoleID']]);

            return json_encode([
                'status' => true,
                'message' => 'Xác nhận email thành công'
            ]);
        }
        else {
            return json_encode([
                'status' => false,
                'message' => 'Mã OTP không chính xác'
            ]);
        }
    }

    private function getRole($roleID) {
        $query = $this->connect->prepare("SELECT * FROM role WHERE RoleID = ?");
        $query->execute([$roleID]);
        $role = $query->fetch();

        if($role) {
            return $role['RoleName'];
        } 
        else {
            return 'Not found';
        }
    }

    private function sendEmail($receiveEmail, $otp) {
        $mail = new PHPMailer(true);
    
        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'thai2k4hongquang@gmail.com';                     //SMTP username
            $mail->Password   = 'pquf xqel xlhd qlhd';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    
            //Recipients
            $mail->setFrom('phammindo2004@gmail.com', 'Room Finder');
            $mail->addAddress($receiveEmail);     //Add a recipient
            
    
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = "OTP Confirmation";
            $mail->Body    = "Mã OTP của bạn là $otp. Mã có thời hạn 5 phút.";
    
            $mail->send();
            
            return json_encode([
                "status" => true,
                "message" => "Gửi mã OTP đến email $receiveEmail"
            ]);
    
        } catch (Exception $e) {
            // echo "Gửi mail thất bại. Mailer Error: {$mail->ErrorInfo}";
            return json_encode([
                "status" => false,
                "message" => "Gửi mã OTP thất bại"
            ]);
        }
    }

}

?>