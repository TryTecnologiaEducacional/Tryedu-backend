<?php
include_once('_private/config.inc.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['a'] = 'emailSenha' && isset($_POST['Email'])) {

  $cabecalho = '<html>
    <head>
      <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
      <title>TryEdu</title>
    </head>
    <body>';
  $rodape = '</body></html>';

  $ObjUser = new User();
  $tokenSenha = bin2hex(openssl_random_pseudo_bytes(32));//token para recuperar senha
  $filtro = "Email = '".$_POST['Email']."'";
  $qt = $ObjUser->listarQuantidade($filtro);
  if ($qt >0 ) {
    $reg = $ObjUser->listar($filtro);
    $rs = $reg->fetchObject();

    //grava TokenPass no registro do usuário
    $ok = ($ObjUser->atualizar($rs->id,array('TokenPass' => $tokenSenha))) ? true : false;

    $conteudo = "<p>Olá $rs->NickName</p>
    <p>Você, ou outra pessoa, solicitou a recuperação de senha do seu acesso ao aplicativo TryEdu.</p>
    <p>Pedimos a gentileza de acssar o link a seguir para cadastrar uma nova senha:</p>
    <p><a href='" . UrlAPI . "NovaSenha.php?u=" . $rs->id . "&ts=" . $tokenSenha . "' target='_blank'>Cadastar nova senha</a></p>
    <p>Esperamos tê-lo ajudado.</p>
    <p></p>
    <p>---<br>
    Equipe TryEdu</p>";

    $ql = (PHP_OS == 'WINNT')? "\r\n" : "\n";//definindo quebra de linha conforme SO do servidor
    $subject = "Recuperação de senha TryEdu";
    $headers = 'From: TyEdu <suporte@teentok.com.br>'. $ql;//cc, cco....
    //enviando no formato HTML
    $headers  .= 'MIME-Version: 1.0' . $ql;
    $headers .= 'Content-type: text/html; charset=UTF-8' . $ql;

    $to = "$rs->NickName <$rs->Email>";
    $message = $cabecalho.$conteudo.$rodape;

    //$enviado = mail($to, $subject, $message, $headers);
    $resp['mensage'] = (mail($to, $subject, $message, $headers))? "E-mail enviado com sucesso.\nOlhe sua caixa de e-mail." : "Erro ao tentar recuperar senha.";
    
  }else{
    $resp['mensage'] = "Nenhum usuário cadastrado com e-mail: " . $_POST['Email'];
  }
  echo json_encode($resp, JSON_UNESCAPED_UNICODE);
}
?>