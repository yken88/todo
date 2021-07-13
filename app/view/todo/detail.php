<?php
require_once './../../Controller/TodoController.php';

$controller = new TodoController;
$todo = $controller->detail();
?>

<!DOCTYPE html>
<html lang="en">
<?php require_once("../layouts/header.php"); ?>

    <title>詳細</title>
</head>
<body class="text-center">
    <h1>詳細</h1>
    <div>
        <div>タイトル</div>
        <div><?php echo $todo['title']; ?></div>
    </div>
    <div>
        <div>詳細</div>
        <div><?php echo $todo['detail']; ?></div>
    </div>
    <div>
        <div>ステータス</div>
        <div><?php echo $todo['display_status']; ?></div>
    </div>
    <div>
        <button>
        <a href="./edit.php?todo_id=<?php echo $todo['id']; ?>">編集</a>
        </button>
    </div>
</body>
</html>
