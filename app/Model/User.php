<?php
require_once './../../config/db.php';

class User
{
    // ユーザの登録状態
    const UNREGISTERED = 0;
    const REGISTERED = 1;

    private $user_id;
    private $user_name;
    private $password;

    protected $email;
    protected $token;

    private $error_msgs = [];


    public function setUsername($user_name){
        $this->user_name = $user_name;
    }

    public function setPassword($password){
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
        $query = sprintf("SELECT `id` FROM `users` WHERE `username` = '%s' AND `password` = '%s';", $this->user_name, $this->password);
        $stmh = $pdo->query($query);

        $user_id = $stmh->fetch(PDO::FETCH_ASSOC);

        $this->user_id = $user_id["id"];
    }

    public function getUserId(){
        return $this->user_id;
    }

    // ユーザがログインできるのであれば、そのユーザの$user_idをsetする。
    public function login()
    {
        $pdo = new PDO(DSN, USERNAME, PASSWORD);
        $query = sprintf("SELECT `id`, `register_status` FROM `users` WHERE `username` = '%s' AND `password` = '%s';", $this->user_name, $this->password);
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

    public function register()
    {
        try{
            $pdo = new PDO(DSN, USERNAME, PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

            $query = sprintf("INSERT INTO `users` (`username`, `email`, `password`, `token`, `register_status`) VALUES ('%s', '%s', '%s', '%s', %d);", 
                $this->user_name, 
                $this->email, 
                $this->password, 
                $this->token, 
                self::UNREGISTERED
                );
            $stmh = $pdo->query($query);

        }catch(PDOException $e){
            error_log("仮登録に失敗しました");
            error_log($e->getMessage());
            // スタックトレイスを残す方法
            error_log($e->getTraceAsString());
            return false;
        }
            

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

        try{
            $pdo = new PDO(DSN, USERNAME, PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $pdo->beginTransaction();

            $query = sprintf("UPDATE users SET `register_status` = %d WHERE `token` = '%s'", self::REGISTERED, $token);
            $stmh = $pdo->query($query);

            if($stmh === false){
                return false;
            }

            $pdo->commit();

        }catch(PDOException $e){
            error_log("本登録に失敗しました");
            error_log($e->getMessage());
            // スタックトレイスを残す方法
            error_log($e->getTraceAsString());

            $pdo->rollback();
            return false;
        }

        $user_id = self::findByToken($token);
       
        return $user_id;
    }

    public function update($user_id){
        try{
            $pdo = new PDO(DSN, USERNAME, PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $pdo->beginTransaction();

            $query = sprintf("UPDATE `users` SET `username` = '%s', `password` = '%s' WHERE id = '%s'",
                $this->user_name,
                $this->password,
                $user_id
            );
            $stmh = $pdo->query($query);

            $pdo->commit();

        }catch(PDOException $e){
            error_log("ユーザー更新に失敗しました");
            error_log($e->getMessage()); 
            error_log($e->getTraceAsString());

            $pdo->rollback();
            return false;
        }
    }

    public function updateEmail($user_id){
        try{
            $pdo = new PDO(DSN, USERNAME, PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $pdo->beginTransaction();

            $query = sprintf("UPDATE `users` SET `email` = '%s' WHERE `id` = '%s';",
                $this->email,
                $user_id
            );
            $stmh = $pdo->query($query);

            $pdo->commit();
        }catch(PDOException $e){
            error_log("更新に失敗しました");
            error_log($e->getMessage()); 
            error_log($e->getTraceAsString());

            $pdo->rollback();
            return false;
        }
    }

    // メールアドレスが存在すれば、falseを返す。
    public static function checkEmailExists($email)
    {
        $pdo = new PDO(DSN, USERNAME, PASSWORD);
        $query = sprintf("SELECT EXISTS (SELECT * FROM `users` WHERE `email` = '%s') AS `user_exists`;", $email);
        $stmh = $pdo->query($query);

        $user_exists = $stmh->fetch(PDO::FETCH_ASSOC);

        //存在すればfalse
        if ($user_exists["user_exists"] === "1") {
            return false;
        }
        
        return true;
    }


    public static function getUserById($user_id)
    {
        try{
            $pdo = new PDO(DSN, USERNAME, PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

            $query = sprintf("SELECT * FROM `users` WHERE `id` = '%d'", $user_id);
            $stmh = $pdo->query($query);

            $user = $stmh->fetch(PDO::FETCH_ASSOC);

            return $user;
        }catch(PDOException $e){
            echo $e->getMessage() . PHP_EOL;
        }
        
    }

    
}