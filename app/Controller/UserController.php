<?php
require_once './../../controller/BaseController.php';
require_once './../../Model/User.php';
require_once './../../Services/UserRegisterService.php';
require_once './../../validation/UserValidation.php';

class UserController
{
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
        $data = array(
            "username" => $_POST["username"],
            "password" => $_POST["password"],
            "email" => $_POST["email"]
        );

        $validation = new UserValidation;
        $validation->setData($data);
        $result = $validation->check();

        if ($result === false) {
            session_start();
            $_SESSION["error_msgs"] = $validation->getErrorMessages();
            return;
        }

        $result = User::checkEmailExists($_POST["email"]);

        if ($result === false) {
            session_start();
            $_SESSION["error_msgs"][] = "メールアドレスはすでに存在します。";
            return;
        }

        // token発行
        $token = UserRegisterService::publishToken();

        $user = new User($_POST["password"], $_POST["password"]);
        $user->setEmail($_POST["email"]);
        $user->setToken($token);
        $result = $user->register();

        if($result === false){
            session_start();
            $_SESSION["error_msgs"] = "仮登録に失敗しました。";

            return header(sprintf("Location: ../auth/register.php?username=%s&email=%s", $username, $email));
        }
    }

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
