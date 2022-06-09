<?php
session_start();

include_once('_private/config.inc.php');

$tabResposta = 'HistoryDialoguesAnswers';
$ObjAnswers = new $tabResposta;


$regA = $ObjAnswers->consultar("idDialogues = 4 AND idUser = 115");
if($regA->rowCount() >0){
  $tmp['Respondida'] = true;
  $rsA = $regA->fetchObject();
  $tmp['idOptions'] = (int)$rsA->idOptions;
  $tmp['idUser'] = (int)$rsA->idUser;
  $tmp['Score'] = (int)$rsA->Score;
  $tmp['Answer'] = $rsA->Answer;
} else {
  $tmp['Respondida'] = false;
  $tmp['idOptions'] = null;
  $tmp['idUser'] = null;
  $tmp['Score'] = null;
  $tmp['Answer'] = null;
}

print_r($tmp);