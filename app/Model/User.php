<?php
require_once './../../config/db.php';

class User
{
    private $user_id;
    private $username;
    private $password;
    private $error_msgs = [];

    public function __construct($username, $password){
        $this->username = $username;
        $this->password = $password;
    }

    public function getErrorMessages(){
        return $this->error_msgs;
    }

    public function setUserId()
    {
        $pdo = new PDO(DSN, USERNAME, PASSWORD);
        $query = sprintf("SELECT `id` FROM `users` WHERE `username` = '%s' AND `password` = '%s';", $this->username, $this->password);
        $stmh = $pdo->query($query);

        $user_id = $stmh->fetch();

        $this->user_id = $user_id["id"];
    }

    public function getUserId(){
        return $this->user_id;
    }


    // ユーザのがログインできるのであれば、そのユーザの$user_idをsetする。
    public function login()
    {
        $pdo = new PDO(DSN, USERNAME, PASSWORD);
        $query = sprintf("SELECT `id` FROM `users` WHERE `username` = '%s' AND `password` = '%s';", $this->username, $this->password);
        $stmh = $pdo->query($query);

        if ($stmh) {
            $user_id = $stmh->fetch(PDO::FETCH_ASSOC);
        } else {
            $user = array();
        }

        if ($user_id === false) {
            $this->error_msgs[] = "ユーザが存在しません";
            return false;
        }

        $this->user_id = $user_id;
    }

    //新規ユーザ登録。ユーザの情報を登録し、新しくできた$user_idを返す。
    public function register()
    {
        $pdo = new PDO(DSN, USERNAME, PASSWORD);
        $query = sprintf("INSERT INTO `users` (`username`, `password`) VALUES ('%s', '%s')", $this->username, $this->password);
        $stmh = $pdo->query($query);

        // 保存されたユーザのidを返す。
        $user_id = $pdo->LastInsertId();

        return $user_id;
    }

    // ユーザネームが存在すれば、falseを返す。
    public static function checkUsernameExists($username)
    {
        $pdo = new PDO(DSN, USERNAME, PASSWORD);
        $query = sprintf("SELECT `id` FROM `users` WHERE `username` = '%s';", $username);
        $stmh = $pdo->query($query);

        $user = $stmh->fetch(PDO::FETCH_ASSOC);

        //存在すればfalse
        if ($user) {
            return false;
        } else {
            return true;
        }
    }

    //ユーザ情報が欲しい時。
    public function getUserById($user_id)
    {
        $pdo = new PDO(DSN, USERNAME, PASSWORD);
        $query = sprintf("SELECT * FROM `users` WHERE `id` = '%s'", $user_id);
        $stmh = $pdo->query($query);

        $user = $stmh->fetch(PDO::FETCH_ASSOC);

        return $user;
    }

    
}
