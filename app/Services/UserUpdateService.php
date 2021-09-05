<?php
class UserUpdateService {
    // 本登録用のURLを記載したメールを送信
    private static function sendEmailForUpdateEmail($email, $token){
        $url = sprintf('http://localhost:8000/view/auth/edit_email_confirm.php?token=%s', $token);

        mail($email, '登録確認メール', $url);
    }

    // token発行メソッド内で、メール送信
    public static function publishToken($email){
        $token = uniqid();
        self::sendEmailForUpdateEmail($email, $token);

        return $token;
    }
}