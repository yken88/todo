<?php
require_once './../../controller/BaseController.php';
require_once './../../Model/User.php';
require_once './../../Model/ChangeEmail.php';
require_once './../../Services/UserUpdateService.php';
require_once './../../Services/UserDeleteService.php';
require_once './../../validation/UserValidation.php';
require_once __DIR__.'/BaseController.php';


class UserController extends BaseController
{
    public function edit(){
        $user = User::getUserById($_SESSION["user_id"]);

        return $user;
    }

    public function update(){
        // 取得 
        $data = array(
            "user_name" => $_POST["user_name"],
            "password" => $_POST["password"],
            "password_confirm" => $_POST["password_confirm"]
        );
        
        // バリデーション
        $validation = new UserValidation;
        $validation->setData($data);
        $result = $validation->check();

        if($result === false){
            session_start();
            $_SESSION["error_msgs"] = $validation->getErrorMessages();
            return;
        }

        $valid_data = $validation->getData();

        $user = new User();
        $user->setUsername($valid_data["user_name"]);
        $user->setPassword($valid_data["password"]);
        $result = $user->update($_SESSION["user_id"]);

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
        $token = UserUpdateService::publishToken($valid_email);
        $change_email->setToken($token);

        // 仮登録用のデータベースに登録
        $result = $change_email->insertEmail($_SESSION["user_id"]);
        if($result === false){
            session_start();
            $_SESSION["error_msgs"][] = "エラーが発生しました。";
            return header(sprintf("Location: ../auth/edit_email.php?user_id=%s", $_SESSION["user_id"]));
        }
    }

    public function updateEmail(){
        $email = ChangeEmail::getEmailByUserId($_SESSION["user_id"]);

        $user = new User;
        $user->setEmail($email["email"]);
        $result = $user->updateEmail($_SESSION["user_id"]);

        if($result === false){
            session_start();
            $_SESSION["error_msgs"] = "更新に失敗しました。";
            return header(sprintf("Location: ../auth/edit_email.php?user_id=%s", $_SESSION["user_id"]));
        }

        // 仮登録のテーブルから削除
        ChangeEmail::deleteEmail($_SESSION["user_id"]);
        return header("Location: ../todo/index.php");
    }


    public static function logout(){
        session_destroy();

        header("Location: ../auth/login.php");
    }

    public function delete(){
        $user = User::getUserById($_SESSION["user_id"]);
        return $user;
    }

    public function deleteConfirm(){
        $data = array(
            "user_name" => $_POST["user_name"],
            "password" => $_POST["password"],
            "password_confirm" => $_POST["password_confirm"]
        );

        $validation = new UserValidation;
        $validation->setData($data);
        $result = $validation->check();

        if($result === false){
            session_start();
            $_SESSION["error_msgs"] = $validation->getErrorMessages();

            return header("Location: ../user/delete.php");
        }

        $validation->setEmail($_POST["email"]);
        $email_result = $validation->checkEmail();

        if($email_result === false){
            session_start();
            $_SESSION["error_msgs"] = $validation->getErrorMessages();

            return header("Location: ../user/delete.php");
        }

        $valid_email = $validation->getEmail();

        // トークン発行、メール送信
        $token = UserDeleteService::publishToken($valid_email);

    }

    public function destroy(){
        $user = new User;
        $result = $user->destroy($_SESSION["user_id"]);

        if($result === false){
            session_start();

            $_SESSION["error_msgs"] = 退会に失敗しました。;
            return header("Location: ../user/delete.php");
        }

        // 新規登録画面へ
        return header("Location: ../auth/register.php");
    }

}
