<?php
include_once('_private/config.inc.php');


$json = file_get_contents('php://input');
$data = json_decode($json);


//echo $json;
//echo $data;
//echo $data->id; 
//echo $data->status; 
$ok ='{ddd}';
$chave = 0;

$jsonInsert = "json vazio";
if ($data != ''){
  $jsonInsert = str_replace("\"","\\\"",$json);
  if  ($data->external_reference != '')
  {
    $chave = $data->external_reference;
  }
}

$ObjBd = new User();
$sql = "INSERT INTO LogsGeneral (content,type,idUser) values ('$jsonInsert','JSON_MERCADO_PAGO',$chave)";
//echo $sql;
$ObjBd->query($sql);

if ($data->status == "approved") {

  //retorna uma questão não respondida de uma lista de questões, ou nenhuma se todas já estão respondidas
  $sql = "UPDATE User set License = 'JORNADA_1' where id = $chave";
  //echo $sql;
  $ok = ($ObjBd->query($sql) > 0) ? '{"msg": Dados atualizados com sucesso!}' : '{"msg": erro ao atualizar.}';
}
else
{
  $ok ='{"msg": erro ao atualizar.}';
}

echo $ok;
