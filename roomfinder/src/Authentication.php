<?php

namespace Pht\Roomfinder;

require_once '../vendor/autoload.php';
require_once '../phpmailer/Exception.php';
require_once '../phpmailer/PHPMailer.php';
require_once '../phpmailer/SMTP.php';

use Firebase\JWT\ExpiredException;
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

                $jwt = $this->setToken($user, $roleName);

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

    public function checkToken($token) {
        if ($token) {
            $key = 'login_account_pht';
            try {
                $decode = JWT::decode($token, new Key($key, 'HS256'));

                return json_encode([
                    'status' => true,
                    'message' => 'Mã token hợp lệ',
                    'data' => (array) $decode
                ]);
            }
            catch (ExpiredException $e) {
                http_response_code(401);
                return json_encode([
                    'status' => false,
                    'message' => 'Token hết hạn'
                ]);
            }
            catch (Exception $e) {
                return json_encode([
                    'status' => false,
                    'message' => 'Mã token không hợp lệ'
                ]);
            }
        } else {
            http_response_code(401);
            return json_encode([
                'status' => false,
                'message' => 'Có lỗi xảy ra'
            ]);
        }
    }

    public function register($email, $password, $roleName, $name, $phoneNumber) {
        $query = $this->connect->prepare("SELECT * FROM user WHERE Email = ?");
        $query->execute([$email]);
        $row = $query->rowCount();
        if($row == 0) {
            $this->checkTemporary($email);
            $otp = rand(100000, 999999);
            $roleID = $this->getRoleByName($roleName);
            $query = $this->connect->prepare("INSERT INTO temporary(Email, Password, RoleID, Name, PhoneNumber, OTP) VALUES (?, ?, ?, ?, ?, ?)");
            $query->execute([$email, $password, $roleID, $name, $phoneNumber, $otp]);

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
            $queryDel = $this->connect->prepare("DELETE FROM temporary WHERE Email = ?");
            $queryDel->execute([$email]);

            $queryIns = $this->connect->prepare("INSERT INTO user(Email, Password, RoleID, Name, PhoneNumber) VALUES (?, ?, ?, ?, ?)");
            $queryIns->execute([$user['Email'], $user['Password'], $user['RoleID'], $user['Name'], $user['PhoneNumber']]);

            return $this->login($user['Email'], $user['Password']);
        }
        else {
            return json_encode([
                'status' => false,
                'message' => 'Mã OTP không chính xác'
            ]);
        }
    }

    public function forgotPassword($email) {
        $query = $this->connect->prepare("SELECT UserID FROM user WHERE Email = ?");
        $query->execute([$email]);
        $row = $query->rowCount();

        if ($row == 0) {
            return json_encode([
                'status' => false,
                'message' => 'Email không tồn tại'
            ]);
        }
        else {
            $otp = rand(100000, 999999);
            $queryUpdate = $this->connect->prepare("UPDATE user SET OTP = ? WHERE Email = ?");
            $queryUpdate->execute([$otp, $email]);

            return $this->sendEmail($email, $otp);
        }
    }

    public function confirmEmail($email, $otp) {
        $query = $this->connect->prepare("SELECT UserID FROM user WHERE Email = ? AND OTP = ?");
        $query->execute([$email, $otp]);
        $row = $query->rowCount();

        if($row == 0) {
            return json_encode([
                "status" => false,
                "message" => "Mã OTP không chính xác"
            ]);
        }
        else {
            return json_encode([
                "status" => true,
                "message" => "Mã OTP chính xác"
            ]);
        }
    }

    public function createPassword($email, $newPassword) {
        $query = $this->connect->prepare("UPDATE user SET Password = ?, OTP = ? WHERE Email = ?");
        $query->execute([$newPassword, null, $email]);

        if($query->rowCount() > 0) {
            return json_encode([
                "status" => true,
                "message" => "Tạo mật khẩu thành công"
            ]);
        }
        else {
            return json_encode([
                "status" => false,
                "message" => "Có lỗi xảy ra"
            ]);
        }
    }

    public function changeInformation($user, $userID, $roleName) {
        $name        = $user['name'];
        $phoneNumber = $user['phoneNumber'];
        $query = $this->connect->prepare("UPDATE user SET Name = ?, PhoneNumber = ? WHERE UserID = ?");
        $query->execute([$name, $phoneNumber, $userID]);

        if($query->rowCount() > 0) {

            $queryGetUser = $this->connect->prepare("SELECT UserID, Email, Name, PhoneNumber FROM user WHERE UserID = ?");
            $queryGetUser->execute([$userID]);
            $user = $queryGetUser->fetch();

            $jwt = $this->setToken($user, $roleName);
            return json_encode([
                "status" => true,
                "message" => "Thay đổi thông tin thành công",
                "token" => $jwt
            ]);
        }
        else {
            return json_encode([
                "status" => false,
                "message" => "Cập nhập không thành công"
            ]);
        }
    }

    private function setToken($user, $roleName) {
        $payload = [
            'iat' => time(),
            'exp' => time() + (60 * 60),
            'user' => [
                'userID' => $user['UserID'],
                'role' => [
                    'roleName' => $roleName
                ],
                'email' => $user['Email'],
                'name' => $user['Name'],
                'phoneNumber' => $user['PhoneNumber']
            ]
        ];

        $jwt = JWT::encode($payload, $this->key, 'HS256');

        return $jwt;
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

    public function checkAccount($userID, $oldPassword, $newPassword) {
        $query = $this->connect->prepare("SELECT UserID FROM user WHERE UserID = ? AND Password = ?");
        $query->execute([$userID, $oldPassword]);
        $user = $query->fetch();

        if($user) {
            return $this->changePassword($userID, $newPassword);
        }
        else {
            return json_encode([
                'status' => false,
                'message' => "Mật khẩu không chính xác"
            ]);
        }
    }

    private function changePassword($userID, $newPassword) {
        $query = $this->connect->prepare("UPDATE user SET Password = ? WHERE UserID = ?");
        $query->execute([$newPassword, $userID]);

        if($query->rowCount() > 0) {
            return json_encode([
                "status" => true,
                "message" => "Đổi mật khẩu thành công"
            ]);
        }
        else {
            return json_encode([
                "status" => false,
                "message" => "Có lỗi xảy ra"
            ]);
        }
    }

    private function getRoleByName($roleName) {
        $query = $this->connect->prepare("SELECT RoleID FROM role WHERE RoleName = ?");
        $query->execute([$roleName]);

        $role = $query->fetch();
        if($role) {
            return $role['RoleID'];
        }
        else {
            return -1;
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

    private function checkTemporary($email) {
        $query = $this->connect->prepare("SELECT * FROM temporary WHERE Email = ?");
        $query->execute([$email]);
        $row = $query->rowCount();

        if($row != 0) {
            $queryDel = $this->connect->prepare("DELETE FROM temporary WHERE Email = ?");
            $queryDel->execute([$email]);
        }
    }

}

?>