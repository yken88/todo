<?php
class UserRegisterService {
    // 本登録用のURLを記載したメールを送信
    public static function sendEmailForRegister($token){
        $url = sprintf('http://localhost:8000/view/auth/main_register.php?user_name=%s&email=%s&token=%s', $_POST['user_name'], $_POST["email"], $token);

        mail($_POST["email"], '登録確認メール', $url);
    }

    // token発行メソッド内で、メール送信
    public static function publishToken(){
        $token = uniqid();
        self::sendEmailForRegister($token);

        return $token;
    }
}