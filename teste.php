<?php
session_start();

include_once('_private/config.inc.php');

$resp = [];
array_push($resp, ['mensage' => "Teste session<hr>"]);

if ($_SESSION['Token'] == $_POST['Token']){
    array_push($resp, ['Teste' => 'Token Ok']);
} else {
    array_push($resp, ['Tk Session:' => $_SESSION['Token']]);
    array_push($resp, ['Tk POST:' => $_POST['Token']]);
    $_SESSION['Token'] = $_POST['Token'];
}

echo json_encode($resp, JSON_UNESCAPED_UNICODE);