<?php
include_once('_private/config.inc.php');
session_start();
$Obj = new PlanosDeAula();
$rs = $Obj->listarPorChave(1);
$filename = $rs->LinkPDF;
header("Content-type: application/pdf");
header("Content-Length: " . filesize($filename));
readfile($filename);
?> 