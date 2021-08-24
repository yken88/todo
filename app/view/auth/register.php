<?php
session_start();
require_once './../../controller/UserController.php';

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $user_controller = new UserController;
    $user_controller->register();
}

$error_msgs = $_SESSION["error_msgs"];
unset($_SESSION["error_msgs"]);
?>

<?php require_once("../layouts/header.php"); ?>

    <title>新規ユーザ登録</title>
</head>
<body class="text-center">
<h1>新規ユーザ登録</h1>
<div class="mx-auto" style="width:400px;">
    <form class="form-group" action="" method="post">
        <label>User Name</label>
        <input type="text" name="username" class="form-control" value="<?php echo $_GET["username"];?>">
        <br>
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="<?php echo $_GET["email"];?>">
        <br>
        <label>パスワード</label>
        <input type="password" name="password" class="form-control">
        <br>
        <div>
        <p>新規登録用のメールを送信します。送信ボタンを押して、メールをご確認ください。</p>
        <input class="btn btn-secondary" type="submit" value="送信">
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
