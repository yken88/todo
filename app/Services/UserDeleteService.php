<?php
class UserDeleteService {
    // 本登録用のURLを記載したメールを送信
    private static function sendEmailForUpdateEmail($email, $token){
        $url = sprintf('http://localhost:8000/view/user/delete_confirm.php?token=%s', $token);

        mail($email, '退会確認', $url);
    }

    // token発行メソッド内で、メール送信
    public static function publishToken($email){
        $token = uniqid();
        self::sendEmailForUpdateEmail($email, $token);

        return $token;
    }
}