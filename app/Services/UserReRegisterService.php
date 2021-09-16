<?php
class UserReRegisterService {
    // 本登録用のURLを記載したメールを送信
    private static function sendEmailForReRegister($token){
        $url = sprintf('http://localhost:8000/view/auth/main_reregister.php?token=%s', $token);

        mail($_POST["email"], '再登録', $url);
    }

    // token発行メソッド内で、メール送信
    public static function publishToken(){
        $token = uniqid();
        self::sendEmailForReRegister($token);

        return $token;
    }
}