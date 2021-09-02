<?php
class UserUpdateService {
    // 本登録用のURLを記載したメールを送信
    private static function sendEmailForUpdateEmail($token){
        $url = sprintf('http://localhost:8000/view/auth/edit_email_confirm.php?token=%s&user_id=%s', $token, $_GET["user_id"]);

        mail($_POST["email"], '登録確認メール', $url);
    }

    // token発行メソッド内で、メール送信
    public static function publishToken(){
        $token = uniqid();
        self::sendEmailForUpdateEmail($token);

        return $token;
    }
}