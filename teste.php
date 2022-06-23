<?php
session_start();

include_once('_private/config.inc.php');


$resp = [];

$chave = 0;
if(isset($_POST['chave'])){
  if($_POST['chave'] >0) $chave = $_POST['chave'];
  unset($_POST['chave']);
}

$tabela = $_POST['t']; unset($_POST['t']);

$acao = $_POST['a']; unset($_POST['a']);

if(isset($_POST['filtro'])){
  $filtro = $_POST['filtro'];
  unset($_POST['filtro']);
}else{
  $filtro = null;
}

if(isset($_POST['filtroNaResposta'])){
  $filtroNaResposta = $_POST['filtroNaResposta'];
  unset($_POST['filtroNaResposta']);
}else{
  $filtroNaResposta = null;
}

if(isset($_POST['ordem'])){
  $ordem = $_POST['ordem'];
  unset($_POST['ordem']);
}else{
  $ordem = null;
}

$ObjBd = new $tabela;

$campoLogin = null;
if (isset($_POST['Email'])) $campoLogin = 'Email';
if (isset($_POST['NickName'])) $campoLogin = 'NickName';

$idUser = (isset($_POST['idUser']) && (int)$_POST['idUser'] > 0)? (int)$_POST['idUser'] : 0;
//unset($_POST['idUser']);

$resp['chave'] = $chave;
$resp['tabela'] = $tabela;
$resp['ação'] = $acao;
$resp['idUser'] = $idUser;

//$UserSession = json_decode($_COOKIE['User'][$idUser], true);
  //não funcionou, em cada requisição vinda do app, vem como se fosse a 1ª requisição

$Token = null;
if (isset($_POST['Token'])) {
  $Token = $_POST['Token'];
  unset($_POST['Token']);
}
echo "teste<hr>";
$busca = 'int';
$ObjBd = new PlanosDeAulaAnswers;
$reg = $ObjBd->listar();
$rs = $reg->fetchObject();
foreach ($rs as $key => $value) {
  $tmp = preg_match("/{$busca}/",$ObjBd->tipoCampo($key))? (int)$value : "'".$value."'";
  echo "id: $key >>> $tmp<br>";
}
