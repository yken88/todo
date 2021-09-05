<?php
require_once 'User.php';

class ChangeEmail extends User{
    public function insertEmail($user_id){
        try{
            $pdo = new PDO(DSN, USERNAME, PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    
            $query = sprintf("INSERT INTO `change_email` (`user_id`, `token`, `email`) VALUES ('%s', '%s', '%s');", 
                $user_id,
                $this->token, 
                $this->email
            );
            $stmh = $pdo->query($query);

        }catch(PDOException $e){
            error_log("仮登録に失敗しました");
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
            return false;
        }
    }

    public static function deleteEmail($user_id){
        try{
            $pdo = new PDO(DSN, USERNAME, PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $query = sprintf("DELETE FROM `change_email` WHERE `user_id` = %s;", $user_id);

            $stmh = $pdo->query($query);
        }catch(PDOException $e){
            echo $e->getMessage();
            return;

            error_log("削除に失敗しました");
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
            return false;
        }
    }

    public static function getEmailByUserId($user_id){
        try{
            $pdo = new PDO(DSN, USERNAME, PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $query = sprintf("SELECT `email` FROM `change_email` WHERE `user_id` = '%s'", $user_id);
            $stmh = $pdo->query($query);

        }catch(PDOException $e){
            echo $e->getMessage();
            error_log("仮登録に失敗しました");
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
            return false;
        }
        $email = $stmh->fetch(PDO::FETCH_ASSOC);

        return $email;
    }
}