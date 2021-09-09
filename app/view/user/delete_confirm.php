<?php
require_once './../../controller/UserController.php';

session_start();
if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $controller = new UserController;
    $controller->destroy();
}


?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>退会画面</title>
</head>
<body>
    <form action="" method="post">
        <p>退会してもよろしいですか？</p>
        <button type="submit">退会</button>
    </form>
</body>
</html>
