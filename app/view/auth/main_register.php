<?php
require_once './../../controller/LoginController.php';

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $controller = new LoginController;
    $controller->mainRegister();
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>本登録</title>
</head>
<body>
    <p>User Name: <?php echo $_GET["user_name"];?></p>
    <p>Email: <?php echo $_GET["email"];?></p>

    <form action="" method="post">
        こちらの内容で登録してもよろしいですか？
        <a href="register.php">キャンセル</a>
        <button type="submit">登録</button>
    </form>
</body>
</html>