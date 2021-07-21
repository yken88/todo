<?php

class BaseController{

    public function __construct(){
        $this->redirectToLogin();
    }

    private function redirectToLogin(){
        session_start();
        $user = $_SESSION["user_id"];

        if(!isset($user)){
            session_start();
            $_SESSION["error_msgs"][] = "ユーザ情報がありません";
            return header("Location: ../auth/login.php");
        }
    }
}