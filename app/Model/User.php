<?php
require_once './../../config/db.php';

class User
{
    // ユーザの登録状態
    const UNREGISTERED = 0;
    const REGISTERED = 1;

    private $user_id;
    private $username;
    private $email;
    private $token;
    private $password;
    private $error_msgs = [];

    public function __construct($username, $password){
        $this->username = $username;
        $this->password = $password;
    }

    public function setEmail($email){
        $this->email = $email;
    }

    public function setToken($token){
        $this->token = $token;
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


    // ユーザがログインできるのであれば、そのユーザの$user_idをsetする。
    public function login()
    {
        $pdo = new PDO(DSN, USERNAME, PASSWORD);
        $query = sprintf("SELECT `id`, `register_status` FROM `users` WHERE `username` = '%s' AND `password` = '%s';", $this->username, $this->password);
        $stmh = $pdo->query($query);

        if ($stmh) {
            $user = $stmh->fetch(PDO::FETCH_ASSOC);
        } else {
            $user = array();
        }

        if ($user === false) {
            $this->error_msgs[] = "ユーザが存在しません";
            return false;
        }

        // 仮登録のみのユーザはログインできない。
        if($user['register_status'] === self::UNREGISTERED){
            $this->error_msgs[] = "本登録が完了していません。";
            return false;
        }

        $this->user_id = $user["user_id"];
    }

    //新規ユーザ登録。ユーザの情報を登録し、新しくできた$user_idを返す。
    public function register()
    {
        $pdo = new PDO(DSN, USERNAME, PASSWORD);
        $query = sprintf("INSERT INTO `users` (`username`, `email`, `password`, `token`, `register_status`) VALUES ('%s', '%s', '%s', '%s', %d);", 
            $this->username, 
            $this->email, 
            $this->password, 
            $this->token, 
            self::UNREGISTERED
        );

        $stmh = $pdo->query($query);
    }

    // tokenからuser_idを取得。
    public static function findByToken($token){
        $pdo = new PDO(DSN, USERNAME, PASSWORD);
        $query = sprintf("SELECT `id` FROM `users` WHERE `token` = '%s';", $token);
        $stmh = $pdo->query($query);

        $user_id = $stmh->fetch(PDO::FETCH_ASSOC);
        return $user_id;
    }
 
    // 本登録 トークンが一致するuserのregister_status を更新する。
    public static function mainRegister($token){
        $pdo = new PDO(DSN, USERNAME, PASSWORD);
        $query = sprintf("UPDATE users SET `register_status` = %d WHERE `token` = '%s'", self::REGISTERED, $token);
        $stmh = $pdo->query($query);

        if($stmh === false){
            return false;
        }

        $user_id = self::findByToken($token);
       
        return $user_id;
    }

    // メールアドレスが存在すれば、falseを返す。
    public static function checkEmailExists($email)
    {
        $pdo = new PDO(DSN, USERNAME, PASSWORD);
        $query = sprintf("SELECT `id` FROM `users` WHERE `email` = '%s';", $email);
        $stmh = $pdo->query($query);

        $user = $stmh->fetch(PDO::FETCH_ASSOC);

        //存在すればfalse
        if ($user !== false) {
            return false;
        } 
        
        return true;
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
