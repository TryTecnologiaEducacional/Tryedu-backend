<?php

class Seguranca {

  /*
    * método autenticar
    * Recebe usuário e password e realiza autenticação
    * Retorna true ou false
    * Se for true, inicia sessão com dados do usuário
    */
  static public function autenticar($campoLogin, $login, $password) {

    // Iniciando instânca da tabela usuário
    $tabUser = new User;
    $filtro = "User.$campoLogin = '".$login. "'";
    $User = $tabUser->consultar($filtro);

    // Recupera o registro
    $rs = $User->fetchObject();

    $pwd_peppered = hash_hmac("sha256", $password, pimenta);
    
    $dados['Logged'] = 0; 
    $dados['Token'] = null; 

    // Verifica se retornou conteúdo e quantas linhas (0 ou 1) e compara senha
    if($User->rowCount() && password_verify($pwd_peppered, $rs->Password)) {

      //setcookie("User[$idUser]", NULL);//limpa variável
      //session_cache_limiter('public');//tipo de cache

      // Inicia sessão
      session_start();

      $idUser = $rs->id;

      // Alimenta variável de sessão
      foreach ($rs as $key => $value) {
        $_SESSION[$key] = ($key == 'Logged')? 1 : $value;
        $_SESSION[$key] = ($key == 'Password')? '***' : $value;
      }

      $_SESSION['Scores'] = '';
      $ObjScores = new Scores();
      //$sql = "SELECT * FROM `Scores` WHERE `idUser` = $idUser";
      $rScore = $ObjScores->listar("`idUser` = $idUser");//$ObjScores->query($sql);
      while ($rSS = $rScore->fetchObject()) {
        if ($_SESSION['Scores'] != '') $_SESSION['Scores'] .= ",";
        $_SESSION['Scores'] .= "[$rSS->ScoreType,$rSS->Score]";
      }

      $_SESSION['Scores'] = json_encode($_SESSION['Scores']);

      $_SESSION['horaemail'] = date('d/m/Y H:i');
      $_SESSION['Token'] = bin2hex(openssl_random_pseudo_bytes(32));

      setcookie("User[$idUser]", $_SESSION);

      // Retorna verdadeiro
      $dados['Logged'] = 1; 
      $dados['Token'] = $_SESSION['Token']; 
    } else {

      // Não confere usuário e password,
      // inicia e destrói sessão para limpá-la
      session_start();
      session_destroy();
      //setcookie("User[$idUser]", NULL);
    }

    if($idUser) {
      $ok = $tabUser->logged($idUser, $dados);

      $Log['DateTime'] = date('Y-m-d H:i:s');
      $Log['Action'] = "Login";
      $Log['idUser'] = $idUser;
      $tabObj = new LogsUser();
      $ok = $tabObj->inserir($Log);
    }

    return $dados['Logged'];

  }

  /*
   * método estaConectado
   * Testa se o usuário está conectado
   * Se não estiver, destrói sessão
   */
  static public function estaConectado($idUser) {

    // inicia ou conecta-se a uma sessão
    session_start();

    $tabUser = new User();
    $rs = $tabUser->listarPorChave($idUser);
    $User = $_SESSION;//(is_null($_COOKIE['User'][$rs->id]))? null : json_decode($_COOKIE['User'][$rs->id], true);

    if($rs->Logged && !is_null($rs->Token)){
    //if(!is_null($User) && $User['id'] == $idUser){
      return true;
    }else{
      // se não houver, destrói a sessão e redireciona
      session_destroy();
      setcookie("User[$idUser]", NULL);
      $dados['Logged'] = 0;
      $dados['Token'] = null;
      $ok = $tabUser->logged($idUser, $dados);
      return false;
    }

  }

}
?>