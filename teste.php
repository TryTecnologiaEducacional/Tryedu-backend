<?php
session_start();

include_once('_private/config.inc.php');

$Obj = new User();
$f = null;//$_POST['filtro'];
$sql = "SELECT `HistoryDialoguesAnswers`.`idUser`, `HistoryDialoguesOptions`.`NameTags` FROM `HistoryDialoguesAnswers` INNER JOIN `HistoryDialoguesOptions` on `HistoryDialoguesOptions`.`id` = `HistoryDialoguesAnswers`.`idOptions` GROUP BY idUser, NameTags";
$reg = $Obj->query($sql);
while ($rs = $reg->fetchObject()) {
  foreach ($rs as $key => $value) {
    echo "$key: $value<br>";
  }
}
