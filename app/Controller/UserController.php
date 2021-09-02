<?php
require_once './../../controller/BaseController.php';
require_once './../../Model/User.php';
require_once './../../Model/ChangeEmail.php';
require_once './../../Services/UserRegisterService.php';
require_once './../../Services/UserUpdateService.php';
require_once './../../validation/UserValidation.php';

class UserController
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
        $user->setUsername($valid_data["password"]);
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

// 編集画面
    public function edit(){
        $user = User::getUserById($_GET["user_id"]);

        return $user;
    }

    public function update(){
        // 取得 
        $data = array(
            "user_name" => $_POST["user_name"],
            "password" => $_POST["password"],
        );
        
        // バリデーション
        $validation = new UserValidation;
        $validation->setData($data);
        $result = $validation->check();

        if($result === false){
            session_start();
            $_SESSION["error_msgs"][] = $validation->getErrorMessages();
            return;
        }

        $valid_data = $validation->getData();

        $user = new User($valid_data["user_name"], $valid_data["password"]);
        $result = $user->update($_GET["user_id"]);
        if($result === false){
            session_start();
            $_SESSION["error_msgs"][] = "ユーザ情報の更新に失敗しました。";
            return;
        }

        return header("Location: ../todo/index.php");
    }

    // メールを仮登録用のデータベースに保存する。
    public function editEmail(){
        $validation = new UserValidation;
        $validation->setEmail($_POST["email"]);
        $result = $validation->checkEmail();
        
        if($result === false){
            session_start();
            $_SESSION["error_msgs"] = $validation->getErrorMessages();
            return;
        }

        $valid_email = $validation->getEmail();
        $email_exists = User::checkEmailExists($valid_email);
        if($email_exists === false){
            $_SESSION["error_msgs"][] = "メールアドレスはすでに使われています";
            return;
        }

        $change_email = new ChangeEmail;
        $change_email->setEmail($valid_email);
        // メール送信
        $token = UserUpdateService::publishToken();
        $change_email->setToken($token);
        // 仮登録用のデータベースに登録
        $result = $change_email->insertEmail($_GET["user_id"]);
        if($result === false){
            session_start();
            $_SESSION["error_msgs"][] = "エラーが発生しました。";
            return header(sprintf("Location: ../auth/edit_email.php?user_id=%s", $_GET["user_id"]));
        }
    }

    public function updateEmail(){
        $email = ChangeEmail::getEmailByUserId($_GET["user_id"]);

        $user = new User;
        $user->setEmail($email["email"]);
        $result = $user->updateEmail($_GET["user_id"]);

        if($result === false){
            session_start();
            $_SESSION["error_msgs"] = "更新に失敗しました。";
            return header(sprintf("Location: ../auth/edit_email.php?user_id=%s", $_GET["user_id"]));
        }

        // 仮登録のテーブルから削除
        ChangeEmail::deleteEmail($_GET["user_id"]);
        return header("Location: ../todo/index.php");
        
    }


    public static function logout(){
        session_destroy();

        header("Location: ../auth/login.php");
    }

}
