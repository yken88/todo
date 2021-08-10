<?php
require_once __DIR__.'/BaseController.php';
require_once __DIR__.'/../Model/Todo.php';
require_once __DIR__.'/../validation/TodoValidation.php';

class TodoController extends BaseController
{
    private const DEFAULT_OFFSET = 0;
    public $max_page;


    public function __construct(){
        $this->setMaxPage();
    }

    public function setMaxPage(){
        $this->max_page = Todo::getMaxPage();
    }

    public function getMaxPage(){
        return $this->max_page;
    }

    public function index()
    {
        $user_id = $_SESSION['user_id'];
        $page = $_GET["page"];
        $sort = $_GET["sort"];

        $offset = self::DEFAULT_OFFSET;
        if($page){
            $offset = ($page - 1) * 5;
        }

        //検索用GETパラメータがあればTodo::search()
        if($_GET["title"] != "" || isset($_GET["status"])){
            $title = $_GET["title"];
            $status = $_GET["status"];

            $params = array(
                'title' => $title,
                'status' => $status
            );

            // search()に$sortを渡す。
            if(is_null($sort)){
                $todo_list = Todo::search($user_id, $params, $offset);
            }else{
                $todo_list = Todo::search($user_id, $params, $offset, $sort);
            }

        }

        // 検索なし
        if($_GET == array() || $_GET["title"] == ""){
            $query = sprintf("SELECT * FROM todos WHERE `user_id` = %d LIMIT %d, %d;", $user_id, $offset, Todo::LIMIT);

            if($sort){
                $query = sprintf("SELECT * FROM todos WHERE `user_id` = %d ORDER BY `created_at` %s LIMIT %d, %d;", $user_id, $sort, $offset, Todo::LIMIT);
            }

            $todo_list = Todo::findByQuery($query);
        }

        return $todo_list;
    }

    public function detail()
    {
        $todo_id = $_GET['todo_id'];

        if (!$todo_id) {
            header("Location: ./../error/404.php");
            return;
        }

        if (Todo::isExistById($todo_id) === false) {
            header("Location: ./../error/404.php");
            return;
        }

        $todo = Todo::findById($todo_id);
        return $todo;
    }


    public function new () {
        $data = array();
      
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($_GET['title'])) {
                $data["title"] = $_GET['title'];
            }

            if (isset($_GET['detail'])) {
                $data["detail"] = $_GET['detail'];
            }
        }

        $data["user_id"] = $_SESSION['user_id'];
        $data["error_msgs"] = $_SESSION['error_msgs'];

        return $data;
    }

    public function store(){
        $data = array(
            'title' => $_POST['title'],
            'detail' => $_POST['detail'],
        );

        $validation = new TodoValidation;
        $validation->setData($data);
        if ($validation->check() === false) {
            $error_msgs = $validation->getErrorMessages();

            session_start();
            $_SESSION['error_msgs'] = $error_msgs;

            $params = sprintf("?title=%s&detail=%s", $_POST['title'], $_POST['detail']);
            header(sprintf("Location: ./new.php%s", $params));
            return;
        }

        $valid_data = $validation->getData();
        $todo = new Todo;
        $todo->setTitle($valid_data['title']);
        $todo->setDetail($valid_data['detail']);

        $user_id = $_SESSION['user_id'];
        $todo->setUserId($user_id);
        $result = $todo->save();

        if ($result === false) {
            $params = sprintf("?title=%s&detail=%s", $valid_data['title'], $valid_data['detail']);
            header(sprintf("Location: ./new.php%s", $params));
            return;
        }

        header("Location: ./index.php");
    }

    public function edit()
    {
        $todo_id = '';
        if (isset($_GET['todo_id'])) {
            $todo_id = $_GET['todo_id'];
        }
        // 404への遷移処理を$todo_id取得した次に。
        if (!$todo_id) {
            header("Location: ./../error/404.php");
            return;
        }

        $params = array();
        if (isset($_GET['title'])) {
            $params["title"] = $_GET["title"];
        }

        if (isset($_GET['detail'])) {
            $params["detail"] = $_GET["detail"];
        }

        if (Todo::isExistById($todo_id) === false) {
            header("Location: ./../error/404.php");
            return;
        }

        $todo = Todo::findById($todo_id);

        if (!$todo) {
            header("Location: ./../error/404.php");
            return;
        }

        $data = array(
            "todo" => $todo,
            "params" => $params,
        );

        return $data;

    }

    public function update()
    {
        if (!$_POST["todo_id"]) {
            session_start();
            $_SESSION['error_msgs'] = "指定したIDに該当するデータがありません。";

            header("Location: ./index.php");
            return;
        }

        if (Todo::isExistById($_POST["todo_id"]) === false) {
            $params = sprintf("?todo_id=%stitle=%s&detail=%s", $_POST['todo_id'], $_POST['title'], $_POST['detail']);
            header(sprintf("Location: ./edit.php%s", $params));
            return;
        }

        $data = array(
            "todo_id" => $_POST['todo_id'],
            "title" => $_POST['title'],
            "detail" => $_POST['detail'],
        );

        $validation = new TodoValidation;

        $validation->setData($data);

        if ($validation->check() === false) {
            $error_msgs = $validation->getErrorMessages();

            session_start();
            $_SESSION['error_msgs'] = $error_msgs;

            $params = sprintf("?todo_id=%s&title=%s&detail=%s", $_POST['todo_id'], $_POST['title'], $_POST['detail']);
            header(sprintf("Location: ./edit.php%s", $params));

            return;
        }

        $valid_data = $validation->getData();

        $todo = new Todo;
        $todo->setId($valid_data['todo_id']);
        $todo->setTitle($valid_data['title']);
        $todo->setDetail($valid_data['detail']);
        $result = $todo->update();

        if ($result === false) {
            $params = sprintf("?todo_id=%stitle=%s&detail=%s", $_POST['todo_id'], $_POST['title'], $_POST['detail']);
            header(sprintf("Location: ./edit.php%s", $params));
            return;
        }

        // うまくいけばindex.phpに遷移
        header("Location: ./index.php");
    }

    public function delete()
    {
        $todo_id = $_POST['todo_id'];
        if (!$todo_id) {
            error_log(sprintf("[TodoController][delete]todo_id id not found. todo_id: %s", $todo_id));
            return false;
        }

        if (Todo::isExistById($todo_id) === false) {
            error_log(sprintf("[TodoController][delete]record is not found. todo_id: %s", $todo_id));
            return false;
        }

        $todo = new Todo;
        $todo->setId($todo_id);
        $result = $todo->delete();

        return $result;
    }

    public function updateStatus()
    {
        $todo_id = $_POST['todo_id'];

        if (!$todo_id) {
            error_log(sprintf("[TodoController][updateStatus]todo_id is not found. todo_id: %s", $todo_id));
            return false;
        }

        if (Todo::isExistById($todo_id) === false) {
            error_log(sprintf("[TodoController][updateStatus]record is not found. todo_id: %s", $todo_id));
            return false;
        }

        $todo = Todo::findById($todo_id);
        if (!$todo) {
            error_log(sprintf("[TodoController][updateStatus]recode id not found. todo_id: %s", $todo_id));
            return false;
        }

        $status = $todo['status'];

        if ($status == Todo::STATUS_INCOMPLETE) {
            $status = Todo::STATUS_COMPLETED;
        } elseif ($status == Todo::STATUS_COMPLETED) {
            $status = Todo::STATUS_INCOMPLETE;
        }

        $todo = new Todo;
        $todo->setId($todo_id);
        $todo->setStatus($status);
        $result = $todo->updateStatus();

        return $result;
    }

    
}
