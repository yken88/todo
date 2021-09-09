<?php
session_start();
require_once './../../controller/UserController.php';

if ($_SERVER["REQUEST_METHOD"] === 'GET') {
    $controller = new UserController;
    $user = $controller->edit();
}

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $controller = new UserController;
    $user = $controller->editEmail();
}


$error_msgs = $_SESSION["error_msgs"];
unset($_SESSION["error_msgs"]);
?>
<?php require_once("../layouts/header.php"); ?>

    <title>ユーザ編集画面</title>
</head>
<body class="text-center">
<h1>メールアドレス変更</h1>
<div class="mx-auto" style="width:400px;">
    <form class="form-group" action="" method="post">
        <label>メールアドレス</label>
        <input type="text" name="email" class="form-control" value="<?php echo $user["email"];?>">
        <br>

        <div>
            <p>新しいメールアドレスにメールアドレス変更用のURLを送信します</p>
            <input class="btn btn-secondary" type="submit" value="メールを送信">
        </div>
    </form>

<?php if ($error_msgs): ?>
    <?php foreach ($error_msgs as $error_msg): ?>
        <p><?php echo $error_msg; ?></p>
    <?php endforeach;?>
<?php endif;?>
</div>
</body>
</html>
