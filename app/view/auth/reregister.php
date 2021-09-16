<?php
session_start();
require_once './../../controller/LoginController.php';

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $controller = new LoginController;
    $controller->reRegister();
}

$error_msgs = $_SESSION["error_msgs"];
unset($_SESSION["error_msgs"]);
?>

<?php require_once("../layouts/header.php"); ?>

<title>再登録</title>
</head>
<body class="text-center">
<h1>再登録</h1>
<div class="mx-auto" style="width:400px;">
<p>再登録用のメールを送信します。</p>
    <form class="form-group" action="" method="post">
        <label>メールアドレス</label>
        <input type="text" name="email" class="form-control" placeholder="メールアドレス">
        <br>
        <p>再登録用のメール送信</p>
        <input class="btn btn-secondary" type="submit" value="送信">
    </form>

<?php if ($error_msgs): ?>
    <?php foreach ($error_msgs as $error_msg): ?>
        <p><?php echo $error_msg; ?></p>
    <?php endforeach;?>
<?php endif;?>
</div>
</body>
</html>
