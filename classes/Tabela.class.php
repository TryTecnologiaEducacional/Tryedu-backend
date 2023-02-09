<?php
/*
Classte Abstrata Tabela
*/
abstract Class Tabela {

  // conexao sera compartilhada entre todas as instâncias
  private static $conexao;
  
  //private static $conexaoFilial;

  // nome da tabela
  protected $tabela;

  // chave primária da tabela
  protected $chavePrimaria;

  // Array com lista de campos da tabela
  protected $campos = array();

  // Array com lista de campo=>legenda
  protected $legendas = array();
  
  protected $dsn = null;

  /*
   * Método construtor
   * Se conexão não existir, cria, senão, nada a fazer
   * (reutiliza a criada por outra instância da Classte Tabela )
   */
  public function __construct() {

      // Montando Data Source Name no formato: "mysql:host=localhost;dbname=banco";
      $dsn = DRIVER.':host='.SERVIDOR.';port='.PORTABD.';dbname='.BANCO;

      try{
        // Realizando conexão e armazenando em constante
        self::$conexao = new PDO($dsn, USUARIO, SENHA, OPCOES);
      } catch (PDOException $e) {
        $erro = '<h1>Erro:</h1><pre>' . $e->getMessage() . "\n" .
                $e->getTraceAsString() . '</pre>' . "\n";
        error_log(date('Y-m-d H:i:s') . " - " . $erro, 3, ArqLog);

        // Exibindo erro para o usuário e encerrando script ** cuidado
        //die("DSN: $dsn<br> ".USUARIO ."<br>". SENHA ."<BR>". $erro);
      }
    //}

  } // término function __construct

  /*
   * Método mágico __get
   * Para recuperar atributos
   * Note que não interessa implementar o __set!!
   */
  public function __get($var){

    return $this->$var;

  } // function __get

  public function __close(){
    $dsn = NULL;
  }
  
  /*
   * Método ultimoId
   * Retorna o ultimo Id inserido
   */
  public function ultimoIdInserido(){
    return self::$conexao->lastInsertId(); //pega último id só imediatamente após inserido.
  }

  public function ultimoId(){   
    $sql = "SELECT id FROM " . $this->tabela . " ORDER BY id DESC LIMIT 1";
    return $this->query($sql)->fetchObject()->id;
  }
  /*
   * Método query
   * Executa uma consulta através da conexão estabelecida
   * Verifica se o debug está ativo
   * (variável $debugBanco=true em config.inc.php)
   * Retorna um objeto com o resultado da consulta
   */
  public function query($sql){
    global $debugBanco;

    // Se debug ativado, exibe SQL na tela
    if ($debugBanco) $GLOBALS['DEBUG']['debugBanco'] .= "<pre>$sql</pre><hr>";

    // Usando tratamento de exceção para capturar possível erro
    try{
      // Define o método pelos primeiros caracteres = SELECT ou select
      $type = substr($sql, 0, 4);

      // Se for SELECT utiliza o método query do PDO
      if (strtolower($type) == 'sele' || strtolower($type) == 'show') {

        $resultado = self::$conexao->query($sql);

      // Senão utiliza o método exec do PDO
      } else {

        $resultado = self::$conexao->exec($sql);

      }

      return $resultado;

    } catch (PDOException $e) {

        // Montando mensagem de erro
        $erro = '<h1>Erro:</h1><pre>' . $e->getMessage() . "\n" .
                $e->getTraceAsString() . '</pre>' . "\n";

        // Gravando erro no log
        error_log(date('Y-m-d H:i:s') . " - " . $erro, 3, ArqLog);

        // Opção de erro fatal
        die($erro);

        // Opção de erro silencioso
        // echo $erro;
        // return false;

    }

  } // function query


  /*
   * Método listar
   * Executa uma consulta na tabela, podendo:
   * - Recebe (opcionalmente) filtro em formato SQL
   * - Recebe (opcionalmente) ordem em formato SQL
   * - Recebe (opcionalmente) limite em formato MySQL
   * Retorna um objeto com o resultado da consulta
   */
  
  public function listar($filtro=null, $ordem=null, $limite=null,$grupo=null,$active=null){ //listar só tabela

    $sql = "SELECT ".$this->tabela.".* FROM " . $this->tabela;

    //testar se existe campo "Active", caso positivo retornar somente true
    $busca = 'Active';
    if (in_array('Active',$this->campos()) && !is_null($active) && !preg_match("/{$busca}/",$filtro)) $active = " $this->tabela.Active = $active";

    // Se foi enviado filtro, concatena filtro
    if(is_null($filtro) && !is_null($active)) $sql .= " WHERE $active";
    if(is_null($active) && !is_null($filtro)) $sql .= " WHERE $filtro";
    if(!is_null($filtro) && !is_null($active)) $sql .= " WHERE $filtro AND $active";

    // Se foi enviado ordem, concatena ordem
    if (!is_null($grupo)){$sql .= " GROUP BY $grupo";}
    
    // Se foi enviado ordem, concatena ordem
    if (!is_null($ordem)){$sql .= " ORDER BY $ordem";}

    // Se foi enviado limite, concatena limite
    if (!is_null($limite)){$sql .= " LIMIT $limite";}

    // Retorna objeto com o resultado encontrado
    return $this->query($sql);

  } // function listar só tabela

//consultar consulta - início
public function consultar($filtro=null, $ordem=null, $limite=null,$grupo=null,$active=null){

  // Monta consulta concatenando tabela
  switch ($this->tabela) {
    case 'User':
      $sql = "SELECT `User`.*,`Schools`.`SchoolName`,`Classt`.`ClassName`,`AccessLevel`.`AccessName`,`AccessLevel`.`AccessTitle` FROM `User`
      LEFT JOIN `Classt` ON `User`.`ClassCode` = `Classt`.`ClassCode`
      LEFT JOIN `Schools` ON `Classt`.`SchoolCode` = `Schools`.`SchoolCode`
      LEFT JOIN `AccessLevel` ON `User`.`idAccessLevel` = `AccessLevel`.`id`";
      break;
    case 'Questions':
      $sql = "SELECT `Questions`.*,`Schools`.`SchoolName`, `Adventure`.`id` as idAdventure,`Adventure`.`Name` as `Adventure` FROM `Questions`
      LEFT JOIN `Adventure` ON `Questions`.`idAdventure` = `Adventure`.`id`
      LEFT JOIN `Classt` ON `Questions`.`ClassCode` = `Classt`.`ClassCode`
      LEFT JOIN `Schools`ON `Classt`.`SchoolCode` = `Schools`.`SchoolCode`";
      break;
    case 'Classt':
      //$sql = "SELECT `Classt`.*,`Schools`.`SchoolName` FROM `Classt` LEFT JOIN `Schools` ON `Schools`.`SchoolCode` = `Classt`.`SchoolCode`";
      $sql = "SELECT `Classt`.*,`Schools`.`SchoolName`, SUM(`User`.`Score`) as SomaScore, AVG(`User`.`Score`) as MediaScore FROM `Classt`
      LEFT JOIN `Schools` ON `Schools`.`SchoolCode` = `Classt`.`SchoolCode`
      LEFT JOIN `User` ON JSON_CONTAINS(`User`.`ClassCode`, concat('[',`Classt`.`ClassCode`,']'))
      GROUP BY `Classt`.`ClassCode`";
      if(!is_null($filtro)){
        $sql .= " HAVING $filtro";
        $filtro = null;
      }
      break;
    case 'Answers':
      $sql = "SELECT Answers.*, `Questions`.`QuestionTitle`, `User`.`NickName` FROM `Answers`
      INNER JOIN `Questions` ON `Questions`.`id` = `Answers`.`idQuestions`
      INNER JOIN `User` ON `User`.`id` = `Answers`.`idUser`";
      break;
    case 'JourneysCategories':
      /* $sql = "SELECT `JourneysCategories`.*,`Journeys`.`JourneyName` FROM `JourneysCategories`
      INNER JOIN `Journeys` ON `JourneysCategories`.`idJourneys` = `Journeys`.`id`"; */

      //exibir soma de Scores das respostas por categoria
      $sql = "SELECT `JourneysCategories`.*, if(SUM(Scores.Score),SUM(Scores.Score),0) as Score FROM `JourneysCategories`
      LEFT JOIN JourneysQuestions ON JourneysCategories.id = JourneysQuestions.idCategoryPlano 
      LEFT JOIN JourneysAnswers ON JourneysAnswers.idQuestions = JourneysQuestions.id
      LEFT JOIN User ON User.id = JourneysAnswers.idUser
      LEFT JOIN Scores ON Scores.idUser = User.id AND Scores.ScoreType = 'Score' 
      GROUP BY JourneysCategories.CategoryName";
      if ($grupo){
        $sql .= ", $grupo";
        $grupo = null;
      }
      if ($filtro) {
        $sql .= " HAVING $filtro";
        $filtro = null;
      }
      break;
    case 'JourneysQuestions':
      $sql = "SELECT `JourneysQuestions`.*, `JourneysCategories`.`CategoryName`, `Journeys`.`JourneyName` FROM `JourneysQuestions` INNER JOIN `JourneysCategories` ON `JourneysQuestions`.`idCategoryPlano` = `JourneysCategories`.`id` INNER JOIN `Journeys` ON `JourneysCategories`.`idJourneys` = `Journeys`.`id`";
      break;
    case 'PlanosDeAulaCategories':
      $sql = "SELECT `PlanosDeAulaCategories`.*, `PlanosDeAulaJourneys`.`JourneyName` FROM `PlanosDeAulaCategories`
      INNER JOIN `PlanosDeAulaJourneys` ON `PlanosDeAulaCategories`.idJourneys = `PlanosDeAulaJourneys`.`id`";
      break;
    case 'PlanosDeAula':
      $sql = "SELECT `PlanosDeAula`.*, PAQ.`id` AS idQuestions, `PlanosDeAulaCategories`.`CategoryName` FROM `PlanosDeAula`
      LEFT JOIN (SELECT * FROM `PlanosDeAulaQuestions` LIMIT 1) AS PAQ ON PAQ.`idCategoryPlano` = `PlanosDeAula`.`id`
      INNER JOIN `PlanosDeAulaCategories` ON `PlanosDeAula`.`idCategories` = `PlanosDeAulaCategories`.`id`";
      break;
    case 'PlanosDeAulaQuestions':
      $sql = "SELECT `PlanosDeAulaQuestions`.*, `PlanosDeAula`.`Name` FROM `PlanosDeAulaQuestions`
      INNER JOIN `PlanosDeAula` ON `PlanosDeAulaQuestions`.`idCategoryPlano` = `PlanosDeAula`.`id`";
      break;
    case 'PlanosDeAulaAnswers':
      $sql = "SELECT `PlanosDeAulaAnswers`.*, `PlanosDeAulaAnswersOptions`.`Text`, `PlanosDeAulaAnswersOptions`.`Emotion`, `PlanosDeAulaQuestions`.`QuestionTitle`, `User`.`NickName`, `PlanosDeAulaQuestions`.`idCategoryPlano` FROM `PlanosDeAulaAnswers` LEFT JOIN `PlanosDeAulaQuestions` ON `PlanosDeAulaQuestions`.`id` = `PlanosDeAulaAnswers`.`idQuestions` LEFT JOIN `User` ON `User`.`id` = `PlanosDeAulaAnswers`.`idUser` LEFT JOIN `PlanosDeAulaAnswersOptions` ON `PlanosDeAulaAnswers`.`idAnswersOptions` = `PlanosDeAulaAnswersOptions`.`id` ";
      break;
    case 'HistoryDialoguesAnswers':
      $sql = "SELECT `HistoryDialoguesAnswers`.*, `HistoryDialoguesOptions`.`idDialogues`, `HistoryDialoguesOptions`.`NameTags`, `HistoryDialoguesOptions`.`Expression`, `HistoryDialoguesOptions`.`Order` FROM `HistoryDialoguesAnswers` LEFT JOIN `HistoryDialoguesOptions` ON `HistoryDialoguesOptions`.`id` = `HistoryDialoguesAnswers`.`idOptions`";
      break;
    case 'HistoryEpisodes':
      $sql = "SELECT `HistoryEpisodes`.*, `HistoryStatus`.`id`, `HistoryStatus`.`idUser`,`HistoryStatus`.`Score`, `HistoryStatus`.`EpisodeStatus`
        FROM `HistoryEpisodes` INNER JOIN `HistoryStatus` ON `HistoryEpisodes`.`id` = `HistoryStatus`.`idEpisodes`";
      break;
    default:
      $sql = "SELECT ".$this->tabela.".* FROM " . $this->tabela;
    break;
  }


    //testar se existe campo "Active", caso positivo retornar somente true
    $busca = 'Active';
    if (in_array('Active',$this->campos()) && !is_null($active) && !preg_match("/{$busca}/",$filtro)) $active = " $this->tabela.Active = $active";
        
    // Se foi enviado filtro, concatena filtro
    if(is_null($filtro) && !is_null($active)) $sql .= " WHERE $active";
    if(is_null($active) && !is_null($filtro)) $sql .= " WHERE $filtro";
    if(!is_null($filtro) && !is_null($active)) $sql .= " WHERE $filtro AND $active";

    // Se foi enviado ordem, concatena ordem
    if (!is_null($grupo)){$sql .= " GROUP BY $grupo";}
    
    // Se foi enviado ordem, concatena ordem
    if (!is_null($ordem)){$sql .= " ORDER BY $ordem";}

    // Se foi enviado limite, concatena limite
    if (!is_null($limite)){$sql .= " LIMIT $limite";}

    // Retorna objeto com o resultado encontrado
    return $this->query($sql);

}//consultar consulta - término

public function percentRespondidas($tabResposta, $filtro, $idUser){ // usar Obj tabela de perguntas
  $sql = "SELECT COUNT(*) as Qt FROM `$this->tabela`
  LEFT JOIN `$tabResposta` ON `$tabResposta`.`idQuestions` = `$this->tabela`.`id` AND $filtro AND `$this->tabela`.`idUser` = $idUser WHERE `idQuestions` is null";
  $QtAbertas = (int)$this->query($sql)->fetchObject()->Qt;
  if(!$QtAbertas) $QtAbertas = 0;
  
  $sql = "SELECT COUNT(*) as Qt FROM `$this->tabela`
  LEFT JOIN `$tabResposta` ON `$tabResposta`.`idQuestions` = `$this->tabela`.`id` AND $filtro AND `$this->tabela`.`idUser` = $idUser WHERE `idQuestions` >0";
  $QtRespondidas = (int)$this->query($sql)->fetchObject()->Qt;
  if(!$QtRespondidas) $QtRespondidas = 0;
  
  return (int)(($QtRespondidas * 100) / ($QtAbertas + $QtRespondidas));
}

  /*
   * Método listarPorChave
   * Executa uma consulta filtrando pela chave primária da tabela
   * Recebe um valor que é um id de um registro
   * Retorna um objeto com o registro
   */
  public function listarPorChave($chave){
    if(!strpos(strtolower($this->tipoCampo($this->chavePrimaria)),'int') && !strpos($chave,"'")) $chave = "'$chave'";
    
    $filtro = "$this->tabela.$this->chavePrimaria = $chave";

    //if (in_array('Active',$this->campos())) $filtro .= " AND (".$this->tabela.".Active = 1 OR ".$this->tabela.".Active = 0)";
    
    $resultado = $this->listar($filtro);

    return $resultado->fetchObject();

  } // function listarPorChave
  
/*
 * lista 1º registro da tabela
 */
  public function listarPrimeiro() {

    $resultado = $this->listar();

    return $resultado->fetchObject();

  }

/*
 * lista quantidde de reegistros da tabela
 */
  public function listarQuantidade($filtro=NULL) {

    $resultado = $this->listar($filtro);

    return ($resultado)? $resultado->rowCount() : 0;

  }

/*
   * Método excluir
   * Exclui um registro filtrando pela chave primária da tabela
   * Recebe um id de um registro
   * Retorna um objeto com o resultado
   */
  public function excluir ($chave){
    $sql = "Nenhum registro encontrado para exclusão!";
  //ver se pode excluir
  if ((strtolower($this->tabela) == 'usuarios' && ($this->listarQuantidade() == 1)) || (strtolower($this->tabela) == 'empresa' && $chave == 1)){
    //return $this->query('Exclusão não permitida pra este registro!');
    $sql = 'Exclusão não permitida pra este registro!';
  }else{
      // Monta consulta concatenando tabela, campo que é chave primária e o valor enviado
    $sql = "DELETE FROM " . $this->tabela . " WHERE " . $this->chavePrimaria . " = '$chave'";

      // Retorna objeto com o resultado encontrado
    //return $this->query($sql);
    }
    return $this->query($sql);
  } // function excluir

  public function excluirIntervalo($filtro){
    $sql = "DELETE FROM " . $this->tabela . " WHERE " . $filtro;
    return $this->query($sql);
  }

  /*
   * Método inserir
   * Insere um registro
   * Recebe um array que contém itens no formato $campo => $valor
   * (um $_POST de formulário é uma boa opção)
   * Retorna um objeto com o resultado
   */
  public function inserir ($dados){
    $valores = ''; $i = '';$nInicial = '';$campos = '';

    // Usando tratamento de exceção para capturar possível erro
    try{
      // Testando se é um array, ou abortando;
      if(!is_array($dados)) {
        throw new Exception('Esperado um Array para Inserir Registro');
      };

    } catch (Exception $e) {

        // Montando mensagem de erro
        $erro = '<h1>Erro:</h1><pre>' . $e->getMessage() . "\n" .
                $e->getTraceAsString() . '</pre>' . "\n";

        // Gravando erro no log
        error_log(date('Y-m-d H:i:s') . " - " . $erro, 3, ArqLog);

        // Exibindo erro para o usuário e encerrando script
        die($erro);

    }

    // Monta consulta concatenando tabela
    $sql = "INSERT INTO " . $this->tabela . " (";

    $valores .= "(";
    // Varre array $dados e atribui $key => $value
    // com campo e valor
    foreach ($dados as $campo => $valor){
        
      // concatena o campo para montar a primeira parte do INSERT
      if($i==$nInicial){$campos  .= "$campo,";}

      //testar se existe campo boolean e converter true para 1 ou false para 0
      if($this->tipoCampo($campo) == 'tinyint(1)'){
        $valor = (strtolower($valor) == 'true')? 1 : 0;
      }

      // concatena o valor para montar a segunda parte do INSERT
      $valor = filter_var($valor,FILTER_SANITIZE_STRING); //retira tags do campo, proteção contra injection
      $valores .= (strpos(strtolower($this->tipoCampo($campo)),'int'))? "$valor," : "'$valor',";
    }
    
    $valores = substr($valores,0,-1)."),";

    // Elimindo última vírgula e finalizando
    $sql .= substr($campos,0,-1) . ") VALUES ";
    $sql .= substr($valores,0,-1);

    //echo $sql;
    // Total de registros inseridos (0 ou quantidade ok)
    return $this->query($sql);

  } // function inserir
    
  /*
   * Método atualizar
   * Atualiza um registro filtrando pela chave primária
   * Recebe um array que contém itens no formato $campo => $valor
   * (um $_POST de formulário é uma boa opção)
   * Recebe um id de um registro
   * Retorna um objeto com o resultado
   */
  public function atualizar($chave, $dados){

    // Usando tratamento de exceção para capturar possível erro
    try{
      // Testando se é um array, ou abortando;
      if(!is_array($dados)) {
        throw new Exception('Esperado um Array para Inserir Registro');
      };

    } catch (Exception $e) {

        // Montando mensagem de erro
        $erro = '<h1>Erro:</h1><pre>' . $e->getMessage() . "\n" .
                $e->getTraceAsString() . '</pre>' . "\n";

        // Gravando erro no log
        error_log(date('Y-m-d H:i:s') . " - " . $erro, 3, ArqLog);

        // Exibindo erro para o usuário e encerrando script
        die($erro);

    }

    // Monta consulta concatenando tabela
    $sql = "UPDATE `" . $this->tabela . "` SET ";

    // Varre array $dados e atribui $key => $value
    // com campo e valor
    foreach ($dados as $campo => $valor){

      // concatena o campo e valor para montar atualização do campo

      if(is_null($valor)){
        $sql .=  "$campo = NULL,";
      }else{
        $valor = filter_var($valor);
        $sql .= (strpos(strtolower($this->tipoCampo($campo)),'int'))? "`$campo` = $valor," : "`$campo` = '$valor',";
      }
            
    }

    // Eliminando última vírgula
    $sql  = substr($sql,0,-1);

    // Monta restante da condição concatenando campo que é chave primária e o valor enviado
    $sql .= (strtolower($this->tipoCampo($this->chavePrimaria)) == 'bigint')? " WHERE (`$this->chavePrimaria` = $chave);" : " WHERE (`$this->chavePrimaria` = '$chave');";

    // Total de registros atualizados (0 ou 1)
    return $this->query($sql);

  } // function atualizar
  
  //listar dados de campos enum
  public function listarEnum($campo){

  $sql = "SHOW COLUMNS FROM `".$this->tabela."` LIKE '".$campo."'";

  return $this->query($sql);

  } //fim listar enum

  public function selectEnum($objeto){
    $conteudo = '';
    while($min = $objeto->fetchObject()) {
      foreach (explode("','",substr($min->Type,6,-2)) as $nome){
        $conteudo .= $nome.'<br>';
      }
    }
    return $conteudo;
  }  

  public function gravaValorNr($getValor,$decimais=1){
    //str_pad($getValor, $decimais, 0) //preencher à direita com zeros
    $res = str_replace('.', '', $getValor);
    if(strstr($res, ",")){
      $resposta = explode(',',$res);
      $resposta[1] = str_pad($resposta[1], $decimais, 0);
      $resposta = $resposta[0] . '.' . $resposta[1];
    }else{
      $resposta = $res . "." . str_pad(0,$decimais,0);
    }
    return $resposta;
  }
  
  public static function gravaSoNr($getValor){
   /*$retira = array(".", " ", "(", ")", "-", "/",",");
   $resposta = str_replace($retira, "", $getValor);*/
   $resposta = preg_replace("/[^0-9]/","",$getValor);
   if($resposta==''){$resposta = 0;}
   return $resposta;
  }
  
  public function temDuplicidade($filtro){
    $resposta = $this->listarQuantidade($filtro);
    $resposta = ($resposta > 0)? TRUE : FALSE;
    return $resposta;
  }

  public function tipoCampo($campo){
    $campos = $this->listarEnum($campo);
    while($reg = $campos->fetchObject()){
      return $reg->Type;
    }
  }

  public function campos(){
    $sql = "SHOW COLUMNS FROM `$this->tabela`;";
    $campos = '';
    foreach ($this->query($sql) as $key => $value) {
      $campos .= $value['Field'].',';
    }
    return explode(",",substr($campos,0,-1));
  }

} // abstract Classt Tabela
?>
