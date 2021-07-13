<?php
session_start();
require_once './../../Controller/TodoController.php';

$controller = new TodoController;
$todo_list = $controller->index();

$error_msgs = $_SESSION['error_msgs'];
unset($_SESSION['error_msgs']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <p>404 not found.</p>
    <?php if ($error_msgs): ?>
    <?php foreach ($error_msgs as $error_msg): ?>
        echo $error_msg;
    <?endforeach;?>
    <?endif;?>
</body>
</html>
