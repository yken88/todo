<?php
session_start();
require_once './../../controller/UserController.php';

if ($_SERVER["REQUEST_METHOD"] === 'GET') {
    $controller = new UserController;
    $user = $controller->delete();
}

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $controller = new UserController;
    $user = $controller->deleteConfirm();
}

$error_msgs = $_SESSION["error_msgs"];
unset($_SESSION["error_msgs"]);
?>
<?php require_once("../layouts/header.php"); ?>

    <title>退会</title>
</head>
<body class="text-center">
<h1>退会</h1>

<p>退会用のURLを送信します。確認パスワードの入力をお願いします。</p>

<div class="mx-auto" style="width:400px;">
    <form class="form-group" action="" method="post">
        <label>ユーザネーム</label>
        <input type="text" name="user_name" class="form-control" value="<?php echo $user["username"]; ?>">
        <br>
        <label>メールアドレス</label>
        <input type="email" name="email" class="form-control" value="<?php echo $user["email"];?>">
        <br>

        <label>パスワード</label>
        <input type="password" name="password" class="form-control" value="<?php echo $user["password"];?>">
        <br>

        <label>確認パスワード</label>
        <input type="password" name="password_confirm" class="form-control" value="">
        <br>

        <input class="btn btn-secondary" type="submit" value="メールを送信">
    </form>

<?php if ($error_msgs): ?>
    <?php foreach ($error_msgs as $error_msg): ?>
        <p><?php echo $error_msg; ?></p>
    <?php endforeach;?>
<?php endif;?>
</div>
</body>
</html>
