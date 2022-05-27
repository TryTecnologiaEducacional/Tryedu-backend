<?php
session_start();

include_once('_private/config.inc.php');

echo "Testse apiteste teentok.<br>Raiz site: ".raiz_site."<hr>";

$Obj = new User();
$reg = $Obj->listar();

echo "Obj ok.<br>";