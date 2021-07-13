<?php
// ini_set("display_errors", "on");
require_once './../../Model/User.php';
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
        $username = $_POST["username"];
        $password = $_POST["password"];

        $data = array(
            "username" => $username,
            "password" => $password
        );

    
        $validation = new UserValidation;
        $validation->setData($data);
        $result = $validation->check();

        if ($result === false) {
            session_start();
            $_SESSION["error_msgs"] = $validation->getErrorMessages();
            return;
        }

        $result = User::checkUsernameExists($username);

        if ($result === false) {
            session_start();
            $_SESSION["error_msgs"] = $validation->getErrorMessages();
            return;
        }

        $user = new User($username, $password);

        $user_id = $user->register();

        session_start();
        $_SESSION["user_id"] = $user_id;

        header("Location: ../todo/index.php");
    }

}
