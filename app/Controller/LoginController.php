<?php
require_once './../../Model/User.php';
require_once './../../validation/UserValidation.php';
require_once './../../Services/UserRegisterService.php';
require_once './../../Services/UserReRegisterService.php';


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

        $user = new User;
        $result = $user->checkEmailExists($valid_data["email"]);
        if ($result === false) {
            session_start();
            $_SESSION["error_msgs"] = $user->getErrorMessages();
            return;
        }
        
        // token発行
        $token = UserRegisterService::publishToken();

        $user->setUsername($valid_data["user_name"]);
        $user->setPassword($valid_data["password"]);
        $user->setEmail($valid_data["email"]);
        $user->setToken($token);
        $result = $user->register();

        if($result === false){
            session_start();
            $_SESSION["error_msgs"][] = "仮登録に失敗しました。";

            return header(sprintf("Location: ../auth/register.php?user_name=%s&email=%s", $user_name, $email));
        }
    }

    
    public function reRegister(){
        $validation = new UserValidation;
        $validation->setEmail($_POST["email"]);
        $result = $validation->checkEmail();

        if ($result === false) {
            session_start();
            $_SESSION["error_msgs"] = $validation->getErrorMessages();
            return;
        }

        $valid_email = $validation->getEmail();

        $user = new User;
        $user->setEmail($valid_email);

        // 登録されていないメールアドレスは拒否
        $email_exists = $user->checkEmailExists($valid_email);
        if($email_exists === true){
            session_start();
            $_SESSION["error_msgs"][] = "こちらのメールアドレスは使われておりません。新規登録してください。";
            return;
        }

        // 退会チェック
        $is_soft_deleted = $user->isSoftDeleted();
        if($is_soft_deleted === false){
            session_start();
            $_SESSION["error_msgs"] = $user->getErrorMessages();
            return;
        }

        // 新たなトークンを生成、DB更新
        $token = UserReRegisterService::publishToken();
        $user->setToken($token);

        $result = $user->updateToken();
        if($result === false){
            session_start();
            $_SESSION["error_msgs"] = $user->getErrorMessages();
            return;
        }

    }

    public function mainRegister(){
        $user_name = $_GET["user_name"];
        $email = $_GET["email"];

        $user = new User;
        $user_id = $user->mainRegister($_GET["token"]);
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

    // 本再登録
    public function mainReRegister(){
        $user = new User;
        $user->setToken($_GET["token"]);
        $result = $user->updateDeletedAt();

        if($result === false){
            session_start();
            $_SESSION["error_msgs"] = $user->getErrorMessages();
            return;
        }

        return header("Location: ../auth/login.php");
    }
}