<?php

session_start();

include_once('_private/config.inc.php');

/*
  confirma se o endereço de email pertence ao usuário
*/

if(!isset($_SESSION['token']) || $_SESSION['token'] == ''){
  session_destroy();
  session_start();
  $_SESSION['token'] = md5(uniqid(rand(), TRUE));
}
$acao = $_GET['a']; unset($_GET['a']);

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['email']) && $_GET['email'] != "" && $acao == 'create'){
/*
  url https://trigame.juanamaral.com.br/emailVerificar.php?email=usiel@usiel.net&password=f5bb0c8de146c67b44babbf4e6584cc5&a=create
  campos obrigatórios: Email, idClass e idAccessLevel
*/
  $ObjBd = new User();
  $reg = $ObjBd->listar("email ='".$_GET['email']."'");
  $rs = ($reg->rowCount() > 0)? $reg->fetchObject() : NULL;

  $ObjScool = new Schools();
  $regS = $ObjScool->listar("schoolCode ='".$_GET['schoolCode']."'");
  $rsS = ($regS->rowCount() > 0)? $regS->fetchObject() : NULL;

  if(is_null($rs)){
    if(is_null($rsS)){
      $id = 'schoolErro';
    }else{
      $id = ($ObjBd->inserir($_GET) > 0)? $ObjBd->ultimoIdInserido() : 'erro';
    }
  }else{
    $id = $rs->id;
  }
  /* desativada verificação de e-mail
  if($rs->verification != 1){
    $texto = '<p><a href="https://trigame.juanamaral.com.br/?id='.$id.'&t=User&a=verificaEmail">Verificar endereço de e-mail.</a></p>';
  }*/ 
}

if($id == 'schoolErro'){
  $retorno = 'O código da escola é inválido.';
}elseif($id=='erro'){
  $retorno = 'Erro ao inserir registro no banco.';
}elseif($rs->verification == 1){
  $retorno = 'Já existe um usuário com esse endereço de e-mail cadastrado.';
}else{
  $cabecalho = '<html>
    <head>
      <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
      <title>Validação de e-mail</title>
    </head>
    <body>';
  $rodape = '</body></html>';

  $ql = (PHP_OS == 'WINNT')? "\r\n" : "\n";//definindo quebra de linha conforme SO do servidor
  $headers = 'From: TRYGAME <tryeducacao@gmail.com>'. $ql;//cc, cco....
  $headers .= 'MIME-Version: 1.0' . $ql;
  $headers .= 'Content-type: text/html; charset=UTF-8' . $ql;
  $to = $_GET['email'];
  $subject = 'Cadastro TryEdu';
  $conteudo = '<p>Obrigado por se cadastrar em nosso jogo, nós protegemos você e seus dados, caso tenha alguma dúvida, pode acessar nossa política de privacidade: <a href="https://tryedu.com.br/politica-de-privacidade">https://tryedu.com.br/politica-de-privacidade</a></p>';
  $conteudo .= '<p>Para mais informações sobre o jogo basta acessar nosso site: <a href="http://umaaventuranaescola.com.br/">http://umaaventuranaescola.com.br/</a></p>';
  $conteudo .= '<p>Nos não enviamos anúncios, você não receberá e-mail de publicidade.</p>';

  $message = $cabecalho . $conteudo . $rodape;
  $retorno = (mail($to, $subject, $message, $headers))? 'Conta criada com sucesso!' : "<p>Erro ao tentar enviar mensagem de e-mail para validação.";
}
$ret['email'] = $to;
$ret['mensage'] = $retorno;
echo json_encode($ret);
?>