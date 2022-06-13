<?php
session_start();

include_once('_private/config.inc.php');

$tabObj = new $_POST['t'];

$tmp = $tabObj->listarPorChave($_POST['chave']);

print_r($tmp);