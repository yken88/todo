<?php
session_start();
require_once './../../controller/UserController.php';
require_once './../../controller/TodoController.php';

// logout処理をUserControllerに記述
if($_GET["logout"]){
    UserController::logout();
}

// GETで一覧
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $todo_list = TodoController::index();
}

$error_msgs = $_SESSION['error_msgs'];
unset($_SESSION['error_msgs']);

?>

<?php require_once("../layouts/header.php"); ?>

</head>
<body class="text-center">
<form action="" method="get">
    <input type="submit" name="logout" value="logout">
</form>
<h1>TODO リスト</h1>
<div class="new"><a href="./new.php">新規作成</a></div>
<div class="text-center">
    <form action="" method="get">
        <input type="text" name="title" value="<?php echo $_GET["title"];?>">
        <input type="radio" name="status" value='0'>未完了
        <input type="radio" name="status" value='1'>完了
        <input type="radio" name="sort" value="0" class="btn">▼
        <input type="radio" name="sort" value="1" class="btn">▲
        <input type="submit" value="検索する">
    </form>

    <!-- <form action="" method="GET">
        
    </form> -->
<?php if ($todo_list): ?>
        <ul style="max-width: 400px;">
            <?php foreach ($todo_list as $todo): ?>
                <li class="list-group-item" style="display: flex;">
                <label>
                    <input type="checkbox" class="todo-checkbox" data-id="<?php echo $todo['id']; ?>" <?php if ($todo['status']): ?>checked<?php endif;?>>
                </label>
                    <a href="./detail.php?todo_id=<?php echo $todo['id'] ?>"><?php echo $todo['title']; ?></a> 
                    :<span class="status"><?php echo $todo['display_status']; ?></span>
                    <div class="delete-btn" data-id="<?php echo $todo['id']; ?>">
                        <div style="margin-left1"><button>削除</button></div>
                    </div>
                </li>
            <?php endforeach;?>
        </ul>
    </div>

<?php else: ?>
    <p>データなし</p>
<?php endif;?>

<?php if ($error_msgs): ?>
    <div>
        <ul>
            <?php foreach ($error_msgs as $error_msg): ?>
                <li><?php echo $error_msg; ?></li>
            <?php endforeach;?>
        </ul>
    </div>
<?endif;?>
<script src="./../../public/js/jquery-3.6.0.js"></script>
<script>
    $(".delete-btn").click(function () {
        let todo_id = $(this).data('id');
        if (confirm("削除しますがよろしいですか？ id:" + todo_id)) {
            $(".delete-btn").prop("disabled", true);
            let data = {};
            data.todo_id = todo_id;
            $.ajax({
                url: '../../../api/delete.php',
                type: 'post',
                data: data
            }).then(
                function (data) {
                    let json = JSON.parse(data);
                    console.log("success", json);
                    if (json.result == 'success') {
                        window.location.href = "./index.php";
                    } else {
                        console.log("failed to delete.");
                        alert("failed to delete.");
                        $(".delete-btn").prop("disabled", false);
                    }
                },
                function () {
                    console.log("fail");
                    alert("fail");
                    $(".delete-btn").prop("disabled", false);
                }
            );
        }
    });

    $('.todo-checkbox').change(function(){
        let todo_id = $(this).data('id');

        let data = {};
        data.todo_id = todo_id;
        $.ajax({
                url: '../../../api/update_status.php',
                type: 'post',
                data: data
            }).then(
                function (data) {
                    console.log(data);
                    return;
                    let json = JSON.parse(data);
                    if (json.result == 'success') {
                        console.log("success");

                        let text = $(this).parent().parent().find('.status').text();
                        console.log(text);

                        if(text == '完了') {
                            text = '未完了';
                        }else if(text == '未完了'){
                            text = '完了';
                        }
                        $(this).parent().parent().find('.status').text(text);

                    } else {
                        console.log("failed to update status.");
                        alert("failed to update status.");
                    }
                }.bind(this),
                function () {
                    alert("fail ajax");
                }
            );
    });


</script>
</body>
</html>