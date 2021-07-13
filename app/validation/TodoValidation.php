<?php
class TodoValidation
{
    public $data = array();
    public $error_msgs = array();

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getErrorMessages()
    {
        return $this->error_msgs;
    }

    public function check()
    {
        if (isset($this->data['title']) && empty($this->data['title'])) {
            $this->error_msgs[] = "タイトルが空です。";
        }

        if (isset($this->data['detail']) && empty($this->data['detail'])) {
            $this->error_msgs[] = "詳細が空です。";
        }

        if (count($this->error_msgs) > 0) {
            return false;
        }

        return true;
    }

}
