<?php
require_once './../../controller/BaseController.php';
require_once './../../Model/User.php';
require_once './../../validation/UserValidation.php';

class UserController
{
    // 本登録用のURLを記載したメールを送信
    public static function sendEmailForRegister($email){
        //token生成
        $token = uniqid();
        $url = sprintf('http://localhost:8000/view/auth/main_register.php?username=%s&email=%s&token=%s', $_POST['username'], $_POST["email"], $token);

        mail($email, '登録確認メール', $url);
        return $token;
    }

    public function login()
    {
        $data = array(
            "username" => $_POST["username"],
            "password" => $_POST["password"]
        );

        $validation = new UserValidation;
        $validation->setData($data);
        $result = $validation->check();

        if ($result === false) {
            session_start();
            $_SESSION["error_msgs"] = $validation->getErrorMessages();
            return;
        }

        $user = new User($_POST["username"], $_POST["password"]);
        $result = $user->login();
        $user->setUserId();

        if ($result === false) {
            session_start();
            $_SESSION["error_msgs"] = $user->getErrorMessages();
            return;
        }

        session_start();
        $_SESSION['user_id'] = $user->getUserId();
        header("Location: ../todo/index.php");
    }

    public function register()
    {
        // メールアドレス登録
        $username = $_POST["username"];
        $email = $_POST["email"];
        $password = $_POST["password"];

        $data = array(
            "username" => $username,
            "password" => $password,
            "email" => $email
        );

    
        $validation = new UserValidation;
        $validation->setData($data);
        $result = $validation->check();

        if ($result === false) {
            session_start();
            $_SESSION["error_msgs"] = $validation->getErrorMessages();
            return;
        }

        $result = User::checkEmailExists($email);
        var_dump($result);
        if ($result === false) {
            session_start();
            $_SESSION["error_msgs"][] = "メールアドレスはすでに存在します。";
            return;
        }

        // token発行
        $token = self::sendEmailForRegister($email);

        $user = new User($username, $password);
        $user->setEmail($email);
        $user->setToken($token);
        $user->register();
    }

    // ここおかしい。修正する。
    public function mainRegister(){
        $usename = $_GET["username"];
        $email = $_GET["email"];

        $user_id = User::mainRegister($_GET["token"]);
        if($user_id === false){
            session_start();
            $_SESSION['error_msgs'][] = "登録できていません。";

            return header(sprintf("Location: ../auth/register.php?username=%s&email=%s", $username, $email));
        }
;
        // session にユーザidを渡して、todo一覧へ遷移
        session_start();
        $_SESSION["user_id"] = $user_id;
        header('Location: ../todo/index.php');
    }

    public static function logout(){
        session_destroy();

        header("Location: ../auth/login.php");
    }

}
