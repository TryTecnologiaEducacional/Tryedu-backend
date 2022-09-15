<?php
session_start();

include_once('_private/config.inc.php');


$resp = [];

$chave = 0;
if (isset($_POST['chave'])) {
  if ($_POST['chave'] > 0) $chave = $_POST['chave'];
  unset($_POST['chave']);
}

$tabela = $_POST['t'];
unset($_POST['t']);

$acao = $_POST['a'];
unset($_POST['a']);
$grupo = (isset($_POST['grupo'])) ? $_POST['grupo'] : null;

if (isset($_POST['filtro'])) {
  $filtro = $_POST['filtro'];
  unset($_POST['filtro']);
} else {
  $filtro = null;
}

if (isset($_POST['filtroNaResposta'])) {
  $filtroNaResposta = $_POST['filtroNaResposta'];
  unset($_POST['filtroNaResposta']);
} else {
  $filtroNaResposta = null;
}

if (isset($_POST['ordem'])) {
  $ordem = $_POST['ordem'];
  unset($_POST['ordem']);
} else {
  $ordem = null;
}

$ObjBd = new $tabela;

$campoLogin = null;
if (isset($_POST['Email'])) $campoLogin = 'Email';
if (isset($_POST['NickName'])) $campoLogin = 'NickName';

$idUser = (isset($_POST['idUser']) && (int)$_POST['idUser'] > 0) ? (int)$_POST['idUser'] : 0;
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

if (($idUser > 0 && $acao <> 'login' && $acao <> 'register' && Seguranca::estaConectado($idUser)) || ($tabela == 'AccessLevel' && $acao == 'consulta')) {
  //conectado/logado
  $ObjBd = new User();
  $rs = $ObjBd->listarPorChave($idUser);
  if ($_SERVER['REQUEST_METHOD'] == 'POST' && !is_null($rs->Token) && $rs->Token == $Token || ($tabela == 'AccessLevel' && $acao == 'consulta')) {

    //&& $_SESSION['login']['Token'] == $_POST['Token']
    /**  && !is_null($UserSession['Token']) && $UserSession['Token'] == $_POST['Token']
     * 
     * $_SESSION e cookies, nenhum funciona no react
     */
    switch ($acao) {
      case 'consultaNaoRespondida':
        //retorna uma questão não respondida de uma lista de questões, ou nenhuma se todas já estão respondidas
        $resp = [];
        $tmp = [];
        $sql = null;
        switch ($tabela) {
          case 'Questions': //questões das missões do aluno
            $tabResposta = 'Answers';
            break;

          case 'JourneysQuestions': //questões das jornadas do aluno
            $tabResposta = 'JourneysAnswers';
            break;

          case 'PlanosDeAulaQuestions': //questões das jornadas do professor responder
            $tabResposta = 'PlanosDeAulaAnswers';
            $sql = "SELECT * FROM `PlanosDeAulaQuestions` WHERE `PlanosDeAulaQuestions`.`Active` = TRUE AND `id` NOT IN (SELECT `idQuestions` as id FROM `PlanosDeAulaAnswers` WHERE `PlanosDeAulaAnswers`.`idUser` = $idUser) AND $filtro LIMIT 1";
            break;

          default:
            $tabResposta = null;
            break;
        }
        $ObjAnswers = new $tabResposta;
        if (!$filtro) $filtro = "idUser = $idUser";
        if (!strpos($filtro, 'Active')) $filtro .= " AND $tabela.Active = TRUE ";
        $reg = ($sql) ? $ObjAnswers->query($sql) : $ObjAnswers->consultar($filtro);
        if ($reg->rowCount() > 0) {
          $rs = $reg->fetchObject();
          foreach ($rs as $key => $value) {
            $tmp[$key] = $value;
          }
          $ObjQuestions = new $tabela;
          $tmp['Percent'] = $ObjQuestions->percentRespondidas($tabResposta, $filtro, $idUser);
          array_push($resp, $tmp);
          $resp['mensage'] = "returno com sucesso";
        } else {
          array_push($resp, $tmp);
          $resp['mensage'] = 'Nenhuma questão disponível ou todas já respondidas.';
        }
        break;
      case 'consultaComResposta':
        //usado com tabela Questions pra retornar um campo contendo se já foi respondida
        $resp = [];
        $tmp = [];
        $ObjBd = new $tabela;
        //$sql = '';
        switch ($tabela) {
          case 'Questions': //questões das missões do aluno
            $tabResposta = 'Answers';
            break;

          case 'JourneysQuestions': //questões das jornadas do aluno
            $tabResposta = 'JourneysAnswers';
            break;

          case 'PlanosDeAulaQuestions' || 'PlanosDeAulaCategories': //questões das jornadas do professor responder
            $tabResposta = 'PlanosDeAulaAnswers';
            break;

          case 'PlanosDeAula':
            $tabResposta = 'PlanosDeAulaAnswers';
            break;

          case 'HistoryDialogues':
            $tabOptions = 'HistoryDialoguesOptions';
            $tabResposta = 'HistoryDialoguesAnswers';
            break;

          default:
            $tabResposta = null;
            break;
        }
        $f = ($chave > 0) ? " `$ObjBd->tabela`.`$ObjBd->chavePrimaria` = $chave "  : null;
        if ($f && $filtro) $filtro .= $f;
        if ($f && !$filtro) $filtro = $f;
        if (in_array('Active', $ObjBd->campos())) {
          if ($filtro) $filtro .= " AND ";
          $filtro .= "$tabela.Active = true";
        }
        $reg = $ObjBd->consultar($filtro, $ordem);
        $ObjAnswers = new $tabResposta;
        $busca = 'int';
        while ($rs = $reg->fetchObject()) {
          foreach ($rs as $key => $value) {
            $tmp[$key] = preg_match("/{$busca}/", $ObjBd->tipoCampo($key)) ? (int)$value : $value;
            if ($key == 'DateRelease') {
              $tmp['Bloqueada'] = ($value > date('Y-m-d')) ? true : false;
            }
          }
          if ($tabela == 'PlanosDeAula') {
            $ObjPerguntas = new PlanosDeAulaQuestions();
            $f = "`PlanosDeAulaQuestions`.`idCategoryPlano` = $rs->id";
            $tmp['QtQuestions'] = (int)$ObjPerguntas->listarQuantidade($f);
            $sql = "SELECT COUNT(`PlanosDeAulaAnswers`.`id`) AS QtAnswers FROM `PlanosDeAulaAnswers` LEFT JOIN `PlanosDeAulaQuestions` ON `PlanosDeAulaQuestions`.`id` = `PlanosDeAulaAnswers`.`idQuestions` LEFT JOIN `User` ON `User`.`id` = `PlanosDeAulaAnswers`.`idUser` LEFT JOIN `PlanosDeAulaAnswersOptions` ON `PlanosDeAulaAnswers`.`idAnswersOptions` = `PlanosDeAulaAnswersOptions`.`id` WHERE `PlanosDeAulaQuestions`.`idCategoryPlano` = $rs->id AND PlanosDeAulaAnswers.idUser = $idUser;";
            $regA = $ObjPerguntas->query($sql);
            $tmp['QtAnswers'] = (int)$regA->fetchObject()->QtAnswers;
          } elseif ($tabela == 'PlanosDeAulaCategories') {
            $ObjTmp = new $tabela;
            $sql = "SELECT count(SUBSTRING(`Answers`, 1, INSTR(`Answers`, ';') - 1)) as acertos FROM `PlanosDeAulaQuestions` INNER JOIN `PlanosDeAula` ON `PlanosDeAula`.id = `PlanosDeAulaQuestions`.`idCategoryPlano` AND `PlanosDeAulaQuestions`.`Active`=true INNER JOIN `PlanosDeAulaCategories` ON `PlanosDeAulaCategories`.`id` = `PlanosDeAula`.`idCategories` WHERE SUBSTRING(`Answers`, 1, INSTR(`Answers`, ';') - 1) IN (SELECT `Answer` FROM `PlanosDeAulaAnswers`) AND `PlanosDeAulaCategories`.`id` = $rs->id AND $filtro;";
            $regQ = $ObjTmp->query($sql);
            $tmp['acertos'] = (int)$regQ->fetchObject()->acertos;

            $sql = "SELECT count(SUBSTRING(`Answers`, 1, INSTR(`Answers`, ';') - 1)) as erros FROM `PlanosDeAulaQuestions` INNER JOIN `PlanosDeAula` ON `PlanosDeAula`.id = `PlanosDeAulaQuestions`.`idCategoryPlano` AND `PlanosDeAulaQuestions`.`Active`=true INNER JOIN `PlanosDeAulaCategories` ON `PlanosDeAulaCategories`.`id` = `PlanosDeAula`.`idCategories` WHERE SUBSTRING(`Answers`, 1, INSTR(`Answers`, ';') - 1) NOT IN (SELECT `Answer` FROM `PlanosDeAulaAnswers` WHERE `Answer` IS NOT NULL) AND `PlanosDeAulaCategories`.`id` = $rs->id AND $filtro;";
            $regA = $ObjAnswers->query($sql);
            $tmp['erros'] = (int)$regA->fetchObject()->erros;
            // ainda falta contemplar a tabela Options
          } elseif ($tabela == 'HistoryDialogues') {
            $fA = "idDialogues = " . $rs->id . " AND idUser = $idUser";
            $sql = "SELECT count(`HistoryDialoguesAnswers`.id) as QtAnswers FROM `HistoryDialoguesAnswers` LEFT JOIN `HistoryDialoguesOptions` ON `HistoryDialoguesOptions`.`id` = `HistoryDialoguesAnswers`.`idOptions` ";
            $qt = (int)$ObjAnswers->query("$sql WHERE $fA")->fetchObject()->QtAnswers;
            $sql = "SELECT `HistoryDialoguesAnswers`.*,`HistoryDialoguesOptions`.`idDialogues`,`HistoryDialoguesOptions`.`Expression` as ExpressionAnswer,`HistoryDialoguesAnswers`.`NameTags` as NameTagsAnswer FROM `HistoryDialoguesAnswers` LEFT JOIN `HistoryDialoguesOptions` ON `HistoryDialoguesOptions`.`id` = `HistoryDialoguesAnswers`.`idOptions` ";
            if ($qt > 0) {
              $regA = $ObjAnswers->query("$sql WHERE $fA");
              $rsA = $regA->fetchObject();
              $tmp['Respondida'] = true;
              $tmp['idOptions'] = (int)$rsA->idOptions;
              $tmp['idUser'] = (int)$rsA->idUser;
              $tmp['Score'] = (int)$rsA->Score;
              $tmp['ExpressionAnswer'] = $rsA->ExpressionAnswer;
              $tmp['NameTagsAnswer'] = $rsA->NameTagsAnswer;
              $tmp['Answer'] = $rsA->Answer;
            } else {
              $tmp['Respondida'] = false;
              $tmp['idOptions'] = null;
              $tmp['idUser'] = null;
              $tmp['Score'] = null;
              $tmp['ExpressionAnswer'] = null;
              $tmp['NameTagsAnswer'] = null;
              $tmp['Answer'] = null;
            }
          } else {
            $f = " $tabResposta.idQuestions = $rs->id";
            $f .= ($filtroNaResposta) ? " AND $filtroNaResposta" : " AND $tabResposta.idUser = $idUser";
            $ra = $ObjAnswers->listarQuantidade($f) > 0 ? true : false;
            $tmp['Respondida'] = $ra;
          }
          array_push($resp, $tmp);
        }
        if ($resp) array_push($resp, ['mensage' => 'sucesso']);
        else array_push($resp, ['mensage' => 'erro']);
        break;
      case 'consulta':
        $resp = array();
        $ObjBd = new $tabela;
        //if(isset($_POST['idUser']) && !in_array('idUser', $ObjBd->campos())) unset($_POST['idUser']);
        unset($_POST['idUser']);
        if ($chave) {
          $f = "`$ObjBd->tabela`.`$ObjBd->chavePrimaria` = $chave";
        } else {
          if ($filtro) {
            $f = $filtro;
          } elseif (count($_POST) > 0) {
            foreach ($_POST as $key => $value) {
              $v = (is_string($value)) ? "'$value'" : $value;
              $f .= "$tabela.$key = $v";
              $f .= " AND ";
            }
            $f .= '0=0';
          } else {
            $f = NULL;
          }
        }
        if ($f == 'UserTags') {
          $sql = "SELECT `idUser`,`NameTags` FROM `HistoryDialoguesAnswers` WHERE idUser = $idUser GROUP BY `idUser`,`NameTags`";
          $reg = $ObjBd->query($sql);
        } else $reg = $ObjBd->consultar($f, $ordem, null, $grupo);

        $tmp = array();
        while ($rs = $reg->fetchObject()) {
          foreach ($rs as $key => $value) {
            $tmp[$key] = $value;
            if ($key == 'DateRelease') {
              $tmp['Bloqueada'] = ($value > date('Y-m-d')) ? true : false;
            }
          }
          array_push($resp, $tmp);
        }
        $msg = ($resp) ? 'retorno sucesso' : "Nenhum registro encontrado";
        array_push($resp, ['mensage' => $msg]);
        break;
      case 'ScoreSum': //retorna consulta com soma de Score por usuário
        $sql = "SELECT JourneysCategories.CategoryName, `JourneysAnswers`.`idUser`,SUM(`JourneysAnswers`.`Score`) as Score FROM `JourneysAnswers`
          INNER JOIN JourneysQuestions ON JourneysQuestions.id = JourneysAnswers.idQuestions
          INNER JOIN JourneysCategories ON JourneysCategories.id= JourneysQuestions.idCategoryPlano
          GROUP BY JourneysCategories.CategoryName,JourneysAnswers.idUser";
        if ($filtro) $sql .= " HAVING $filtro";
        $resp = array();
        $ObjBd = new $tabela;
        $reg = $ObjBd->query($sql);
        $tmp = array();
        while ($rs = $reg->fetchObject()) {
          foreach ($rs as $key => $value) {
            $tmp[$key] = $value;
          }
          array_push($resp, $tmp);
        }
        $msg = ($resp) ? 'retorno sucesso' : "Nenhum registro encontrado";
        array_push($resp, ['mensage' => $msg]);
        break;
      case 'delete':
        $msg = ($ObjBd->excluir($chave) > 0) ? 'Registro exluído com sucesso!' : 'erro ao excluir registro';
        array_push($resp, ['mensage' => $msg]);
        break;
      case 'create':
        $ObjBd = new $tabela;
        $qtd = 0;
        if (isset($_POST['idUser']) && !in_array('idUser', $ObjBd->campos())) unset($_POST['idUser']);
        if (stristr($tabela, 'Answers') && !stristr($tabela, 'Options')) {
          $filtro = " (`$tabela`.`idQuestions` = " . $_POST['idQuestions'] . ") AND `$tabela`.`idUser` = $idUser";
          $qtd = $ObjBd->listarQuantidade($filtro);
        }
        if ($tabela == 'PlanosDeAula') {
          $filtro = " (Name = '" . $_POST['Name'] . "') ";
          $qtd = $ObjBd->listarQuantidade($filtro);
        }
        if ($qtd == 0) {

          if ($ObjBd->inserir($_POST) > 0) {
            if ($_FILES['image']) {
              // Montando o nome da imagem
              $filename = "{" . UrlUpload . "/" . $tabela . "/" . str_pad(($ObjBd->ultimoIdInserido()), 11, "0", STR_PAD_LEFT);
              // Movendo arquivo temporário para destino
              move_uploaded_file($_FILES['image']['tmp_name'], $filename . "." . end(explode(".", $_FILES['image']['tmp_name'])));
            }
            if (isset($_POST['Score'])) { // atualiza Score na tabele do usuário e retorna atualizado
              $ObjUser = new User();
              $resp['Score'] = $ObjUser->ScoreUpdate($idUser, $_POST['Score']);
            }
            $resp['mensage'] = 'dados criados com sucesso';
          } else {
            $resp['mensage'] = 'erro ao inserir dados';
          }
        } else {
          /* if(stristr($tabela, 'Answers') && !stristr($tabela, 'Options') && isset($_POST['Score'])){
              $reg = $ObjBd->consultar($filtro);
              $resp['resposta'] = json_encode($reg->fetchObject());
            } */
          $resp['mensage'] = 'Registro duplicado!';
        }
        break;
      case 'deslogar':
        session_destroy();
        setcookie("User[$idUser]", NULL);
        $dados['Logged'] = 0;
        $dados['Token'] = null;
        $ObjBd = new User();
        if ($ObjBd->logged($idUser, $dados)) {
          $Log['DateTime'] = date('Y-m-d H:i:s');
          $Log['Action'] = "Logout";
          $Log['idUser'] = $idUser;
          $tabObj = new LogsUser();
          $ok = $tabObj->inserir($Log);
          $resp['mensage'] = 'Logoff realizado com sucesso!';
        } else {
          $resp['mensage'] = 'erro ao gravar logoff no banco.';
        }
        break;

      case 'update':
        // 
        //popula variável com dados da SESSION
        $resp = []; //$UserSession;
        $ObjBd = new $tabela;
        if (isset($_POST['idUser']) && !in_array('idUser', $ObjBd->campos())) unset($_POST['idUser']);
        if ($_POST) {
          if ($tabela == 'User') {
            if (isset($_POST['SchoolName']) & $_POST['SchoolName'] == '' & $_POST['SchoolName'] <> NULL) unset($_POST['SchoolName']);
            if (isset($_POST['SchoolYear']) & $_POST['SchoolYear'] == '' & $_POST['SchoolYear'] <> NULL) unset($_POST['SchoolYear']);
            if ($_POST['FamilyCode'] == 'NovoCodigo') {
              $_POST['FamilyCode'] = $ObjBd->GetFamilyCode();
              $resp['FamilyCode'] = $_POST['FamilyCode'];
            }
          }
          /*
            ao enviar url com a=update com os campos SchoolName e SchoolYear:
            - se o valor for vazio, não grava nada no banco de dados
            - se o valor for = NULL, limpa os dados do campo na tabela
            - se for diferente disso, grava os dados no banco.
            */
          if (isset($_POST['isUser']) && $tabela == 'User') unset($_POST['isUser']);

          $ok = ($ObjBd->atualizar($chave, $_POST) > 0) ? 'Dados atualizados com sucesso!' : 'erro ao atualizar.';
          if ($ok == 'Dados atualizados com sucesso!' && $tabela == 'User' && $chave) {
            $_SESSION = $ObjBd->listarAtual($chave);
            array_push($resp, $_SESSION);
          }
          $resp['mensage'] = $ok;
        } else {
          $resp['mensage'] = "Erro ao atualizar.";
        }
        if ($_FILES['image']) {

          // Montando o nome da imagem
          $filename = "{" . UrlUpload . "/" . $tabela . "/" . str_pad(($ObjBd->ultimoIdInserido()), 11, "0", STR_PAD_LEFT);

          // Movendo arquivo temporário para destino
          //move_uploaded_file($_FILES['image']['tmp_name'], $filename . "." . end(explode(".",$_FILES['image']['tmp_name'])));
          $resp['mensage'] = 'Imagem enviada com sucesso!\n' . $filename . "." . end(explode(".", $_FILES['image']['tmp_name']));
        }

        break;
      default:
        $resp['mensage'] = 'Dados inválidos.';
        break;
    }
  } else {
    if ($idUser) {
      $tabUser = new User();
      $rs = $tabUser->listarPorChave($idUser);
      $resp['mensage'] = 'erro - Token incorreto.';
      $dados['Logged'] = 0;
      $dados['Token'] = null;
      $ok = $tabUser->logged($idUser, $dados);
    } else $resp['mensage'] = 'erro - Token incorreto.';
    session_destroy();
    setcookie("User[$idUser]", NULL);
  }
} else { //não logado/conectado
  switch ($acao) {
    case 'login':
      if (isset($_POST[$campoLogin]) && isset($_POST['Password'])) {
        $ObjUser = new User();
        $filtro = "$tabela.$campoLogin = '" . $_POST[$campoLogin] . "'";
        $reg = $ObjUser->consultar($filtro);
        $rs = $reg->fetchObject();
        if (Seguranca::autenticar($campoLogin, $_POST[$campoLogin], $_POST['Password'])) {
          // Se foi autenticado
          setcookie("User[$idUser]", NULL); //limpa variável

          setcookie("User[$idUser]", json_encode($_SESSION), time() + 3600); // expira em 1 hora

          $resp = $_SESSION;
          //$resp = json_decode($_COOKIE['User'][$rs->id], true);
          $resp['mensage'] = 'Login realizado com sucesso.';
        } else {
          $resp['mensage'] = 'Login e/ou senha incorreto(s).';
        }
      } else {
        $resp['mensage'] = 'Login e/ou senha não recebido(s).';
      }
      break;

    case 'verificaEmail':
      // valchavea o endereço de email
      $rs = $ObjBd->listar("$ObjBd->chavePrimaria = $chave");
      if ($rs->rowcount()) {
        $dados['verification'] = 1;
        $resp['mensage'] = ($ObjBd->atualizar($chave, $dados) > 0) ? 'Conta criada com sucesso, acesse seu e-mail e clique no link de confirmação. A mensagem pode estar na caixa de spam.' : 'erro';
      } else {
        $resp['mensage'] = 'Usuário inexistente.';
      }
      break;


    case 'criarCodigoUsuario':
      $usuarioExistente = $ObjBd->validarEmail();
      if ($usuarioExistente[0] == 'true') {
        $sucesso = $ObjBd->CriarCodigoValidacao();
        $arrRetorno = array('criado' => $sucesso[0], 'mensagem' => $sucesso[1]);
        $resp = json_encode($arrRetorno);
      } else {
        $arrRetorno = array('criado' => $usuarioExistente[0], 'mensagem' => $usuarioExistente[1]);
        $resp = json_encode($arrRetorno);
      }
      break;

    case 'validarCodigoUsuario':

      $sucesso = $ObjBd->ValidarCodigo();
      $arrRetorno = array('ativado' => $sucesso[0], 'mensagem' => $sucesso[1]);
      $resp = json_encode($arrRetorno);
      break;


    case 'deslogar':
      array_push($resp, ['mensage' => 'Este usuário não está logado. Sucesso']);
      break;

    case 'register': ////register = para cadastros de User ou School ou outro
      // URL: https://juanamaral.com.br/tryedu/?t=User&a=create&Token=AAAAAA&Campo1=asdfasdfasd&Campo2=asdfasd
      $ObjBd = new Classt();
      $tmp = str_replace(array('[', ']', '"'), '', $_POST['ClassCode']);
      if ($ObjBd->listarQuantidade("ClassCode = $tmp") > 0) {
        $ObjBd = new $tabela;
        if (isset($_POST['FamilyCode']) && ($_POST['FamilyCode'] == 'undefined' || is_null($_POST['FamilyCode']))) unset($_POST['FamilyCode']);
        switch ($tabela) {
          case 'User':
            $filtro = "$tabela.NickName = '" . $_POST['NickName'] . "' OR $tabela.Email = '" . $_POST['Email'] . "'";
            $msgErro = "Usuário já cadastrado anteiormente.";
            break;

          case 'Schools':
            $filtro = 'SchoolCode ="' . $_POST['SchoolCode'] . '" AND SchoolName = "' . $_POST['SchoolName'] . '"';
            $msgErro = "Escola já cadastrada";
            break;

          default:
            $filtro = null;
            $resp['mensage'] = 'Dados inválidos. (User)';
            break;
        }

        if (!is_null($filtro)) {
          $qtd = $ObjBd->listarQuantidade($filtro); //ver se usuário ou escola já cadastrado

          //criptografando senha
          if (isset($_POST['Password'])) {
            $pwd_peppered = hash_hmac("sha256", $_POST['Password'], pimenta);
            $_POST['Password'] = password_hash($pwd_peppered, PASSWORD_ARGON2ID);
          }

          //gerando código da família
          $ObjAccess = new AccessLevel();
          $rsAccess = $ObjAccess->listarPorChave($_POST['idAccessLevel']);
          if ($tabela == 'User' && $rsAccess->AccessName == "Responsavel" || (isset($_POST['FamilyCode']) && $_POST['FamilyCode'] == 'true')) {
            $_POST['FamilyCode'] = $ObjBd->GetFamilyCode();
          }
          if ($qtd > 0) {
            $resp['mensage'] = $msgErro;
          } elseif ((int)$ObjBd->inserir($_POST) > 0) {
            $resp['mensage'] = 'Cadastro realizado com sucesso.';
            if (isset($_POST['FamilyCode'])) $resp['FamilyCode'] = $_POST['FamilyCode'];
          } else {
            $resp['mensage'] = "Erro ao cadastrar.";
          }
        }
      } else {
        $resp['mensage'] = "Código de turma inválido";
      }
      break;

    default:
      //session_destroy();
      setcookie("User[$idUser]", NULL);
      $tabUser = new User();
      $dados['Logged'] = 0;
      $dados['Token'] = null;
      $ok = $tabUser->logged($idUser, $dados);
      $resp['mensage'] = "erro - não logado, desconectado. ($ok)\n" . json_encode($_POST);
      break;
  }
}

$retorno = is_null($resp) ? $UserSession : $retorno = $resp;

if (is_null($resp) && is_null($UserSession)) {
  echo "<b>Cuidado!<\b><br><p>Você não tem permissão para estar aqui.<\p>";
} else {
  echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
}
