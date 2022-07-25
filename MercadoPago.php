<?php
include_once('_private/config.inc.php');


  $json = file_get_contents('php://input');
  $data = json_decode($json);

//echo $json;

  //echo $data->payment->id; 
  //echo $data->payment->state; 
  
  $chave = $data->additional_info->external_reference;

if ($data->payment->state == "approved")
{
  $ObjBd = new User();
  //retorna uma questão não respondida de uma lista de questões, ou nenhuma se todas já estão respondidas
  $sql = "UPDATE User set License = 'JORNADA_1' where id = $chave";
  echo $sql;
  $ok = ($ObjBd->query($sql) > 0)? 'Dados atualizados com sucesso!' : 'erro ao atualizar.';

  echo $ok;
}

?>