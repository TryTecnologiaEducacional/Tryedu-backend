<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


class UserImages extends Tabela
{
  protected $tabela = 'UserImages';
  protected $chavePrimaria = 'id';
  protected $legendas = array(); 



public function ComprarImages()
  {

    $email = $_POST['email'];
    //unset($_POST['email']);
    $codigo = rand(1000, 9999);
    $minutes_to_add = 5;
    $data = new DateTime();
    $data->add(new DateInterval('PT' . $minutes_to_add . 'M'));
    $dataStamp = $data->format("Y-m-d H:i:s");


    $sql = "INSERT INTO `teentok_teste`.`UserCodigoValidacao` ( `email`, `codigo`, `dataValidade`, `ativado`, `metodoValidacao`) VALUES ('$email', '$codigo', '$dataStamp', false, 'email');";
    $this->query($sql);
    $arr = array('true', 'Código gerado e enviado com sucesso.' . $codigo);
  }



  public function ConsultarImagesUser(int $idUser)
  {

    $sql = "SELECT * FROM `UserImages` WHERE idUser = $idUser";
  
    $retorno = $this->query($sql);
    $count = $retorno->rowCount();
            
    $arrayRegistros = array();
    $tmp = array();
    while ($rs = $retorno->fetchObject()) {
      foreach ($rs as $key => $value) {
        $tmp[$key] = $value;
      }
     array_push($arrayRegistros, $tmp);
    }

    return $arrayRegistros;
  }

}

?>