<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);


include_once('_private/config.inc.php');

$codigoAutenticacao = $_GET['codigoAutenticacao'];
$email = $_GET['email'];


$sql = "SELECT * FROM User WHERE email = '$email'";
//echo $sql;

$ObjBd = new User();
$retorno = $ObjBd->query($sql);
$count = $retorno->rowCount();

if (($count == 1) && ($codigoAutenticacao == "MikeWazowski")) {

    //echo $retorno->fetchObject()->id;
    $idUser = $retorno->fetchObject()->id;
    //echo $idUser;

    $sql = "INSERT INTO LogsGeneral (content,type,idUser) values ('APROVADO_MANUALMENTE','JSON_MERCADO_PAGO',$idUser)";
    //echo $sql;
    $ObjBd->query($sql);
    
     //retorna uma questão não respondida de uma lista de questões, ou nenhuma se todas já estão respondidas
        $sql = "UPDATE User set License = 'JORNADA_1' where id = $idUser";
        $ObjBd->query($sql);
        //echo $sql;
     
    echo 'Dados atualizados com sucesso!';
 }
 else {
     echo 'Usuário não encontrado ou código de autenticação inválido';
 }
 echo 'fim';




