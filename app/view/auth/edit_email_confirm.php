<?php
require_once './../../controller/UserController.php';

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $controller = new UserController;
    $controller->updateEmail();
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
    <form action="" method="post">
        <p>メールアドレスを変更しますか？</p>
        <button type="submit">変更</button>
    </form>
</body>
</html>
