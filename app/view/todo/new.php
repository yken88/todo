<?php
session_start();
require_once './../../Controller/TodoController.php';

// リクエストメソッドがPOSTであれば、store()メソッド。
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new TodoController();
    $controller->store();
}

// GETであればnew()メソッド
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new TodoController();
    $data = $controller->new();
}

?>
<?php require_once("../layouts/header.php"); ?>

    <title>新規作成</title>
</head>
<body class="text-center">
    <h1>新規作成</h1>
    <form action="./new.php" method="post">
        <div>
            <div>タイトル</div>
            <input type="text" name="title" value="<?php echo $data["title"]; ?>">
        </div>

        <div>
            <div>詳細</div>
            <textarea name="detail"><?php echo $data["detail"]; ?></textarea>
        </div>

        <input type="hidden" name="user_id" value="<?php echo $data["user_id"]; ?>">
        <button type="submit">登録</button>
    </form>

    <?php if ($data["error_msgs"]): ?>
        <div>
            <ul>
                <?php foreach ($data["error_msgs"] as $error_msg): ?>
                    <li><?php echo $error_msg; ?></li>
                <?php endforeach;?>
            </ul>
        </div>

    <?php endif;?>
</body>
</html>

