<?php
include_once('_private/config.inc.php');

session_start();

$ObjBd = new User();

/*
for ($i = 1; $i <= 1500; $i++) {
  $dados['Email'] = str_pad($i, 4, "0", STR_PAD_LEFT)."@tryedu.com.br";
  $dados['Password'] = "tryedu123";
  $dados['idClass'] = 1;
  $dados['idAccessLevel'] = 1;

  echo ($ObjBd->inserir($dados) > 0)? 'Inserido User: '.$dados['Email'].'| ' : 'Erro ao inserir: '.$dados['Email'].'| ';
  
}*/

//criptografar as senhas
/*
$reg = $ObjBd->listar('`Password` = "tryedu123"');
while ($rs = $reg->fetchObject()) {
  $dados['Password'] = md5($rs->Password);
  echo ($ObjBd->atualizar($rs->id,$dados) > 0)? 'Ok!' : 'Erro';
  echo " $rs->id; ";
}*/

?>