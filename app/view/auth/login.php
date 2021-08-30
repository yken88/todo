<?php
session_start();
require_once './../../controller/UserController.php';

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $user_controller = new UserController;
    $user_controller->login();
}

$error_msgs = $_SESSION["error_msgs"];
unset($_SESSION["error_msgs"]);
?>

<?php require_once("../layouts/header.php"); ?>

    <title>Login</title>
</head>
<body class="text-center">
<h1>ログイン</h1>
<div class="mx-auto" style="width:400px;">
    <form class="form-group" action="" method="post">
        <label>User Name</label>
        <input type="text" name="user_name" class="form-control">
        <br>
        <label>Email</label>
        <input type="email" name="email" class="form-control">
        <br>
        <label>パスワード</label>
        <input type="password" name="password" class="form-control">
        <br>
        <div>
        <input class="btn btn-secondary" type="submit" value="ログイン">
        </div>
    </form>

<?php if ($error_msgs): ?>
    <?php foreach ($error_msgs as $error_msg): ?>
        <p><?php echo $error_msg; ?></p>
    <?php endforeach;?>
<?php endif;?>
</body>
</html>
