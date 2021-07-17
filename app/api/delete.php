<?php
require_once '../Controller/TodoController.php';

$controler = new TodoController;
$result = $controler->delete();

$response = array();
if ($result) {
    $response['result'] = 'success';
} else {
    $response['result'] = 'fail';
}

echo json_encode($response);
