<?php
class UserValidation
{
    const EMAIL_PATTERN = "/^([a-zA-Z0-9])+([a-zA-Z0-9._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9._-]+)+$/";
    private $data = array();
    private $error_msgs = array();

    
    public function setData($data){
        $this->data = $data;
    }

    public function getData(){
        return $this->data;
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

        if(empty($this->data["email"])){
            $this->error_msgs[] = "メールアドレスは必須です。";
            return false;
        }

        // メールアドレス形式チェック
        if(preg_match(self::EMAIL_PATTERN, $data["email"]) === 1){
            $this->error_msgs[] = "メールアドレスを正しい形式で入力してください。";
            return false;
        }

        return true;
    }
}
