<?php
session_start();
require_once './../../controller/UserController.php';

if ($_SERVER["REQUEST_METHOD"] === 'GET') {
    $user_controller = new UserController;
    $user = $user_controller->edit();
}

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $user_controller = new UserController;
    $user_controller->update();
}

$error_msgs = $_SESSION["error_msgs"];
unset($_SESSION["error_msgs"]);
?>

<?php require_once("../layouts/header.php"); ?>

    <title>ユーザ編集画面</title>
</head>
<body class="text-center">
<h1>ユーザ編集画面</h1>
<div class="mx-auto" style="width:400px;">
    <form class="form-group" action="" method="post">
        <label>User Name</label>
        <input type="text" name="user_name" class="form-control" value="<?php echo $user["username"];?>">
        <br>
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="<?php echo $user["email"];?>">
        <br>
        <label>パスワード</label>
        <input type="password" name="password" class="form-control" value="<?php echo $user["password"];?>">
        <br>
        <div>
        <p>メールアドレスを変更する場合はメールを送信します。編集ボタンを押して、メールをご確認ください。</p>
        <input class="btn btn-secondary" type="submit" value="更新">
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
