<?php
require_once './../../Controller/TodoController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new TodoController();
    $controller->update();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $controller = new TodoController();
    $data = $controller->edit();
}

$todo = $data["todo"];
$params = $data["params"];

session_start();
setcookie(session_name(), session_id(), time() + $lifetime);

$error_msgs = $_SESSION['error_msgs'];

?>
<!DOCTYPE html>
<?php require_once("../layouts/header.php"); ?>

    <title>編集</title>
</head>
<body class="text-center">
    <h1>編集</h1>
    <form action="./edit.php" method="POST">
        <div>
            <div>タイトル</div>
            <input type="text" name="title" value="
<?php if (isset($params['title'])): ?><?php echo $params['title']; ?><?php else: ?><?php echo $todo['title']; ?>"<?php endif;?>">
        </div>

        <div>
        <div>詳細</div>
            <textarea name="detail">
<?php if (isset($params['detail'])): ?><?php echo $params['detail']; ?><?php else: ?><?php echo $todo['detail']; ?>
<?php endif;?></textarea>
        </div>
        <input type="hidden" name="todo_id" value="<?php echo $todo['id']; ?>">
        <button type="submit">更新</button>
    </form>

    <?php if ($error_msgs): ?>
        <div>
            <ul>
                <?php foreach ($error_msgs as $error_msg): ?>
                    <li><?php echo $error_msg; ?></li>
                <?php endforeach;?>
            </ul>
        </div>

    <?php endif;?>
</body>
</html>
