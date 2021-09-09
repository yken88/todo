<?php
require_once './../../Model/User.php';
require_once './../../validation/UserValidation.php';
require_once './../../Services/UserRegisterService.php';


// ログイン前の処理
class LoginController 
{
    public function login()
    {
        $data = array(
            "user_name" => $_POST["user_name"],
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
        $valid_data = $validation->getData();

        $user = new User;
        $user->setUsername($valid_data["user_name"]);
        $user->setPassword($valid_data["password"]);
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
            "user_name" => $_POST["user_name"],
            "password" => $_POST["password"],
            "password_confirm" => $_POST["password_confirm"],
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

        $valid_data = $validation->getData();

        $result = User::checkEmailExists($valid_data["email"]);
        if ($result === false) {
            session_start();
            $_SESSION["error_msgs"][] = "メールアドレスはすでに存在します。";
            return;
        }

        // token発行
        $token = UserRegisterService::publishToken();

        $user = new User;
        $user->setUsername($valid_data["user_name"]);
        $user->setPassword($valid_data["password"]);
        $user->setEmail($valid_data["email"]);
        $user->setToken($token);
        $result = $user->register();

        if($result === false){
            session_start();
            $_SESSION["error_msgs"] = "仮登録に失敗しました。";

            return header(sprintf("Location: ../auth/register.php?user_name=%s&email=%s", $user_name, $email));
        }
    }

    public function mainRegister(){
        $user_name = $_GET["user_name"];
        $email = $_GET["email"];

        $user_id = User::mainRegister($_GET["token"]);
        if($user_id === false){
            session_start();
            $_SESSION['error_msgs'][] = "登録できていません。";

            return header(sprintf("Location: ../auth/register.php?user_name=%s&email=%s", $user_name, $email));
        }

        // session にユーザidを渡して、todo一覧へ遷移
        session_start();
        $_SESSION["user_id"] = $user_id;
        header('Location: ../todo/index.php');
    }
}