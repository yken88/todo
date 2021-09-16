<?php
require_once './../../controller/LoginController.php';

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $controller = new LoginController;
    $controller->mainReRegister();
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>再登録</title>
</head>
<body>
    <form action="" method="post">
        <p>再登録しますか?</p>
        <input class="btn btn-secondary" type="submit" value="再登録">
    </form>
</body>
</html>