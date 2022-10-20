<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

require 'awsSdk/aws-autoloader.php';

use Aws\Ses\SesClient;
use Aws\Exception\AwsException;


class UserCodigoValidacao extends Tabela
{
  protected $tabela = 'UserCodigoValidacao';
  protected $chavePrimaria = 'id';

  protected $legendas = array();



  public function validarEmail()
  {
    $email = $_POST['email'];
    $nick = $_POST['nick'];
    $sql = "SELECT * FROM `teentok_teste`.`User` WHERE Email = '$email' OR  Nickname = '$nick'";
    $retorno = $this->query($sql);
    $count = $retorno->rowCount();

    if ($count > 0) {
      $arr = array('false', 'Nick ou e-mail já existente');
    } else {
      $arr = array('true', 'Cadastro possível');
    }
    return $arr;
  }

  public function CriarCodigoValidacao()
  {

    $email = $_POST['email'];
    //unset($_POST['email']);
    $codigo = rand(1000, 9999);
    $minutes_to_add = 5;
    $data = new DateTime();
    $data->add(new DateInterval('PT' . $minutes_to_add . 'M'));
    $dataStamp = $data->format("Y-m-d H:i:s");


    $sql = "INSERT INTO `UserCodigoValidacao` ( `email`, `codigo`, `dataValidade`, `ativado`, `metodoValidacao`) VALUES ('$email', '$codigo', '$dataStamp', false, 'email');";
    $this->query($sql);
    $arr = array('true', 'Código gerado e enviado com sucesso.' . $codigo);


    $SesClient = new SesClient([
      'profile' => 'default',
      'version' => '2010-12-01',
      'region'  => 'us-east-1'
    ]);

    $char_set = 'UTF-8';

    $sender = 'app@tryedu.app.br';
    $senderName = 'Contato Tryedu';

    $cabecalho = '<html><head><meta content="text/html; charset=utf-8" http-equiv="Content-Type"><title>Validação de e-mail</title>
</head><body>';
    $rodape = '</body></html>';

    $to =  [$email, $email];
    $subject = 'Cadastro TryEdu';
    $conteudo = '<p>Ol&#225;, seja bem-vindo a Tryedu. Este &#233; seu c&#243;digo de verifica&#231;&#227;o de email: ' . $codigo . ' Nós protegemos você e seus dados, caso tenha alguma dúvida, pode acessar nossa política de privacidade: <a href="https://tryedu.com.br/politica-de-privacidade">https://tryedu.com.br/politica-de-privacidade</a></p>';
    // $conteudo .= '<p>Para mais informações sobre o jogo basta acessar nosso site: <a href="http://umaaventuranaescola.com.br/">http://umaaventuranaescola.com.br/</a></p>';
    // $conteudo .= '<p>Nos não enviamos anúncios, você não receberá e-mail de publicidade.</p>';
    $message = $cabecalho . $conteudo . $rodape;
    $plaintext_body = 'Olá, seja bem-vindo a Tryedu. Este é seu código de verificação de email: ' . $codigo . ' Nós protegemos você e seus dados, caso tenha alguma dúvida, pode acessar nossa política de privacidade: https://tryedu.com.br/politica-de-privacidade"';

    try {
      $result = $SesClient->sendEmail([
          'Destination' => [
              'ToAddresses' => $to,
          ],
          'ReplyToAddresses' => [$sender],
          'Source' => $sender,
          'Message' => [
            'Body' => [
                'Html' => [
                    'Charset' => $char_set,
                    'Data' => $message,
                ],              
                'Text' => [
                  'Charset' => $char_set,
                  'Data' => $plaintext_body,
              ],
            ],
            'Subject' => [
                'Charset' => $char_set,
                'Data' => $subject,
            ],
          ],
          // If you aren't using a configuration set, comment or delete the
          // following line
          //'ConfigurationSetName' => $configuration_set,
      ]);
      
      $messageId = $result['MessageId'];
      //echo("Email sent! Message ID: $messageId"."\n");

  } catch (AwsException $e) {
      // output error message if fails
      echo $e->getMessage();
      echo("The email was not sent. Error message: ".$e->getAwsErrorMessage()."\n");
      echo "\n";
      $arr = array('false', 'Erro no envio do email.');
  }

    

    return $arr;
    //DISPARAR EMAIL
    // Retornar via JSON codigo criado: "true" ou "false".

  }


  public function ValidarCodigo()
  {

    $email = $_POST['email'];
    unset($_POST['email']);
    $codigo = $_POST['codigo'];
    unset($_POST['codigo']);
    $data = date("Y-m-d H:i:s");

    //TOD0: VAlidar o código. Se estiver certo, faz o update
    $sql = "SELECT * FROM `UserCodigoValidacao` WHERE `email` = '$email' AND codigo = '$codigo' AND dataValidade >= '$data' ";
    $retorno = $this->query($sql);
    $count = $retorno->rowCount();
    $arr = array('false', 'Código Inválido ou Expirado.');

    if ($count == 1) {
      $sql = "UPDATE `UserCodigoValidacao` SET `ativado` = true WHERE `email` = '$email'";
      $this->query($sql);
      $arr = array('true', 'Código Validado com Sucesso!');
    }
    return $arr;
  }
}
