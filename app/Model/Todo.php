<?php
require_once './../../config/db.php';

class Todo
{

    const STATUS_INCOMPLETE = 0;
    const STATUS_COMPLETED = 1;

    const STATUS_INCOMPLETE_TXT = "未完了";
    const STATUS_COMPLETE_TXT = "完了";

    public $id;
    public $title;
    public $detail;
    public $status;
    public $user_id;

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getTitle()
    {
        return $this->title;
    }
    public function setTitle($title)
    {
        $this->title = $title;
    }
    public function getDetail()
    {
        return $this->detail;
    }

    public function setDetail($detail)
    {
        $this->detail = $detail;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    public static function findByQuery($query)
    {
        $pdo = new PDO(DSN, USERNAME, PASSWORD);
        $stmh = $pdo->query($query);

        if ($stmh) {
            $todo_list = $stmh->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $todo_list = array();
        }

        return $todo_list;
    }

    public static function findAll($user_id)
    {
        $pdo = new PDO(DSN, USERNAME, PASSWORD);
        $query = sprintf("SELECT * FROM todos WHERE `user_id` = '%s'", $user_id);
        $stmh = $pdo->query($query);

        if ($stmh) {
            $todo_list = $stmh->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $todo_list = array();
        }

        if ($todo_list && count($todo_list) > 0) {
            foreach ($todo_list as $index => $todo) {
                $todo_list[$index]['display_status'] = self::getDisplayStatus($todo['status']);
            }
        }

        return $todo_list;
    }

    public static function findById($todo_id)
    {
        $pdo = new PDO(DSN, USERNAME, PASSWORD);
        $stmh = $pdo->query(sprintf("select * from todos where id = %s;", $todo_id));

        if ($stmh) {
            $todo = $stmh->fetch(PDO::FETCH_ASSOC);
        } else {
            $todo = array();
        }

        if ($todo) {
            $todo['display_status'] = self::getDisplayStatus($todo['status']);
        }

        return $todo;
    }

    public static function getDisplayStatus($status)
    {
        if ($status == self::STATUS_INCOMPLETE) {
            return self::STATUS_INCOMPLETE_TXT;
        } elseif ($status == self::STATUS_COMPLETED) {
            return self::STATUS_COMPLETE_TXT;
        }

        return "";
    }

    public function save()
    {
        try {
            $query = sprintf("INSERT INTO `todos` (`title`, `detail`, `status`, `created_at`, `updated_at`, `user_id`) VALUES ('%s', '%s', 0, NOW(), NOW(), %d);",
                $this->title,
                $this->detail,
                $this->user_id
            );

            $pdo = new PDO(DSN, USERNAME, PASSWORD);
            $result = $pdo->query($query);

        } catch (Exception $e) {
            // エラーログを残すことで、エラーの原因を調査しやすく。
            error_log("新規作成に失敗しました");
            error_log($e->getMessage());
            // スタックトレイスを残す方法
            error_log($e->getTraceAsString());
            return false;
        }

        return $result;
    }

    public function update()
    {
        try {
            $query = sprintf("UPDATE `todos` SET `title` = '%s', `detail` = '%s',`updated_at` =  '%s' WHERE id = '%s'",
                $this->title,
                $this->detail,
                date("Y-m-d H:i:s"),
                $this->id
            );

            $pdo = new PDO(DSN, USERNAME, PASSWORD);
            $result = $pdo->query($query);

        } catch (Exception $e) {
            // エラーログを残すことで、エラーの原因を調査しやすく。
            error_log("更新に失敗しました");
            error_log($e->getMessage());
            // スタックトレイスを残す方法
            error_log($e->getTraceAsString());

            return false;
        }

        return $result;
    }

    public static function isExistById($todo_id)
    {
        $pdo = new PDO(DSN, USERNAME, PASSWORD);
        $stmh = $pdo->query(sprintf("select * from todos where id = %s;", $todo_id));

        if ($stmh) {
            $todo = $stmh->fetch(PDO::FETCH_ASSOC);
        } else {
            $todo = array();
        }

        if ($todo) {
            return true;
        }

        return false;
    }

    public function delete()
    {
        try {
            $query = sprintf("DELETE FROM todos WHERE id = %s",
                $this->id
            );

            $pdo = new PDO(DSN, USERNAME, PASSWORD);

            $result = $pdo->query($query);

        } catch (Exception $e) {
            error_log("削除に失敗しました。");
            error_log($e->getMessage());
            error_log($e->getTraceAsString());

            return false;
        }
        return $result;
    }

    public function updateStatus()
    {
        try {
            $query = sprintf("UPDATE `todos` SET `status` = '%s', `updated_at` = '%s' WHERE id = %s",
                $this->status,
                date("Y-m-d H:i:s"),
                $this->id
            );
            $pdo = new PDO(DSN, USERNAME, PASSWORD);
            $result = $pdo->query($query);

        } catch (Exception $e) {
            // エラーログを残すことで、エラーの原因を調査しやすく。
            error_log("更新に失敗しました");
            error_log($e->getMessage());
            // スタックトレイスを残す方法
            error_log($e->getTraceAsString());

            return false;
        }

        return $result;
    }
}