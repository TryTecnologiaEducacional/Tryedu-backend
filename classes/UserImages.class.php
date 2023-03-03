<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


class UserImages extends Tabela
{
  protected $tabela = 'UserImages';
  protected $chavePrimaria = 'id';
  protected $legendas = array(); 



public function ComprarImagesUser(int $idUser, int $idImage)
  {
 
    $sql = "SELECT * FROM `UserImages` WHERE UserImages.idImage = $idImage and idUser = $idUser;";
    $retorno = $this->query($sql);
    $count = $retorno->rowCount();

    if ($count == 0) 
    {
      $sql = "INSERT INTO `UserImages` ( `idUser`, `idImage`) VALUES ($idUser, $idImage);";
      $this->query($sql);
      $arr = array('true', 'Imagem comprada com sucesso.');
    }
    else
    {
      $arr = array('false', 'Houve um problema ao comprar a imagem. Verifique se o usuário já não possui a imagem desejada.');
    }
      return $arr;
  }



  public function ConsultarImagesUser(int $idUser)
  {

    $sql = "SELECT * FROM UserImages inner join ImagePerfil on UserImages.idImage = ImagePerfil.id where idUser = $idUser";
  
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