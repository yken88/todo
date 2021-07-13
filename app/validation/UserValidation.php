<?php
class UserValidation
{
    private $data = array();
    private $error_msgs = array();

    
    public function setData($data){
        $this->data = $data;
    }


   
    public function getErrorMessages(){
        return $this->error_msgs;
    }

    public function check()
    {
        if (empty($this->data["username"])) {
            $this->error_msgs[] = "ユーザネームは必須です。";
            return false;
        }

        if (empty($this->data["password"])) {
            $this->error_msgs[] = "パスワードは必須です。";

            return false;
        }

        return true;
    }
}
