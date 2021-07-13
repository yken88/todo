<?php
require_once './../../Controller/TodoController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new TodoController();
    $controller->new();
}

$title = '';
$detail = '';
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['title'])) {
        $title = $_GET['title'];
    }

    if (isset($_GET['detail'])) {
        $detail = $_GET['detail'];
    }
}

if (!session_start()) {
    echo "sesssionスタートできてないよ";
}

$user = $_SESSION['user_id'];
$error_msgs = $_SESSION['error_msgs'];

?>
<?php require_once("../layouts/header.php"); ?>

    <title>新規作成</title>
</head>
<body class="text-center">
    <h1>新規作成</h1>
    <form action="./new.php" method="post">
        <div>
            <div>タイトル</div>
            <input type="text" name="title" value="<?php echo $title; ?>">
        </div>

        <div>
            <div>詳細</div>
            <textarea name="detail"><?php echo $detail; ?></textarea>
        </div>

        <input type="hidden" name="user_id" value="<?php echo $user["id"]; ?>">
        <button type="submit">登録</button>
    </form>

    <?php if ($error_msgs): ?>
        <div>
            <ul>
                <?php foreach ($error_msgs as $error_msg): ?>
                    <li><?php echo $error_msg; ?></li>
                <?php endforeach;?>
            </ul>
        </div>

    <?php endif;?>
</body>
</html>
