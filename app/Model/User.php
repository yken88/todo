<?php
require_once './../../config/db.php';

class User
{
    // ユーザの登録状態
    const UNREGISTERED = 0;
    const REGISTERED = 1;

    const DEFAULT_DELETED_AT = NULL;

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

    public function getEmail(){
        return $email;
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

        // 退会チェック
        if($user["deleted_at"] !== null){
            $this->error_msgs[] = "このアカウントは退会されています。";
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

    // 再登録
    public function reRegister($token){
        try{
            $pdo = new PDO(DSN, USERNAME, PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $pdo->beginTransaction();

            $sth = $pdo->prepare("UPDATE `users` SET `deleted_at` = ? WHERE `token` = ?");
            $sth->bindValue(1, self::DEFAULT_DELETED_AT);
            $sth->bindValue(2, $token);
            $sth->execute();

            $pdo->commit();

        }catch(PDOException $e){
            error_log("ユーザー更新に失敗しました");
            error_log($e->getMessage()); 
            error_log($e->getTraceAsString());

            $pdo->rollback();
            return false;
        }
    }

    // tokenからuser_idを取得。
    public static function findByToken($token){
        $pdo = new PDO(DSN, USERNAME, PASSWORD);
        $query = sprintf("SELECT * FROM `users` WHERE `token` = '%s';", $token);
        $stmh = $pdo->query($query);

        $user = $stmh->fetch(PDO::FETCH_ASSOC);
        return $user;
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

        $user = self::findByToken($token);
        
        return $user["id"];
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
            $pdo->query($query);
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
    public function checkEmailExists($email)
    {
        $pdo = new PDO(DSN, USERNAME, PASSWORD);
        $query = sprintf("SELECT EXISTS (SELECT * FROM `users` WHERE `email` = '%s') AS `user_exists`;", $email);
        $stmh = $pdo->query($query);

        $user_exists = $stmh->fetch(PDO::FETCH_ASSOC);

        //存在すればfalse
        if ($user_exists["user_exists"] === "1") {
            $this->error_msgs[] = "メールアドレスは存在します。再登録するか別のメールアドレスで登録してください。";
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
            error_log("ユーザの取得に失敗しました");
            error_log($e->getMessage()); 
            error_log($e->getTraceAsString());

            $pdo->rollback();
            return false;
        }
        
    }

    public function destroy($user_id){
        try{
            $pdo = new PDO(DSN, USERNAME, PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $pdo->beginTransaction();

            // 論理削除
            $query = sprintf("UPDATE `users` SET `deleted_at` = NOW() WHERE `id` = '%s';",
                $user_id
            );
            $pdo->query($query);
            $pdo->commit();

        }catch(PDOException $e){
            error_log("更新に失敗しました");
            error_log($e->getMessage()); 
            error_log($e->getTraceAsString());

            $pdo->rollback();
            return false;
        }
    }    

    public function updateToken(){
        try{
            var_dump($this->email);
            $pdo = new PDO(DSN, USERNAME, PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $pdo->beginTransaction();

            $stmh = $pdo->prepare("UPDATE users SET `token` = ? WHERE `email` = ?;");
            $stmh->bindValue(1, $this->token);
            $stmh->bindValue(2, $this->email);
            $stmh->execute();

            $pdo->commit();

        }catch(PDOException $e){
            $this->error_msgs[] = "トークンの発行に失敗";
            echo $e->getMessage();
            error_log("トークン更新に失敗。");
            error_log($e->getMessage());
            // スタックトレイスを残す方法
            error_log($e->getTraceAsString());

            $pdo->rollback();
            return false;
        }

    }

// 退会チェック
    public function isSoftDeleted(){
        $pdo = new PDO(DSN, USERNAME, PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

        $query = sprintf("SELECT `deleted_at` FROM `users` WHERE `email` = '%s'", $this->email);
        $stmh = $pdo->query($query);

        $deleted_at = $stmh->fetch(PDO::FETCH_ASSOC);

        if(is_null($deleted_at["deleted_at"])){
            $this->error_msgs[] = "このアカウントは退会していません。";
            return false;
        }

        return true;
    }

    public function updateDeletedAt(){
        try{
            $pdo = new PDO(DSN, USERNAME, PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $pdo->beginTransaction();

            $sth = $pdo->prepare("UPDATE users SET `deleted_at` = ? WHERE `token` = ?");
            $sth->bindValue(1, self::DEFAULT_DELETED_AT);
            $sth->bindValue(2, $this->token);
            $sth->execute();

            $pdo->commit();

        }catch(PDOException $e){
            $this->error_msgs[] = "復元失敗";
            error_log("トークン更新に失敗。");
            error_log($e->getMessage());
            // スタックトレイスを残す方法
            error_log($e->getTraceAsString());

            $pdo->rollback();
            return false;
        }
    }
}