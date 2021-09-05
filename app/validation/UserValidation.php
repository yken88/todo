<?php
class UserValidation
{
    const EMAIL_PATTERN = "/^([a-zA-Z0-9])+([a-zA-Z0-9._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9._-]+)+$/";

    private $data = array();
    private $email;
    private $error_msgs = array();

    
    public function setData($data){
        $this->data = $data;
    }

    public function getData(){
        return $this->data;
    }

    public function setEmail($email){
        $this->email = $email;
    }

    public function getEmail(){
        return $this->email;
    }

    public function getErrorMessages(){
        return $this->error_msgs;
    }

    public function check()
    {
        if (empty($this->data["user_name"])) {
            $this->error_msgs[] = "ユーザネームは必須です。";
            return false;
        }

        if (empty($this->data["password"])) {
            $this->error_msgs[] = "パスワードは必須です。";
            return false;
        }

        // password_confirmに値が入っていれば、確認を行う。
        if(!empty($this->data["password_confirm"])){
            if($this->data["password"] !== $this->data["password_confirm"]){
                $this->error_msgs[] = "確認パスワードと一致しません。";
                return false;
            }    
        }
        
        return true;
    }

    // メールアドレスのみをバリデーション
    public function checkEmail(){
        if(empty($this->email)){
            $this->error_msgs[] = "メールアドレスは必須です。";
            return false;
        }

        // メールアドレス形式チェック
        if(preg_match(self::EMAIL_PATTERN, $this->email) === 0){
            $this->error_msgs[] = "メールアドレスを正しい形式で入力してください。";
            return false;
        }

        return true;
    }
}
