<?php
/*
 * Configurações de acesso ao banco de dados
 */
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

$banco = (strstr($_SERVER['HTTP_HOST'], 'teste'))? 'teentokapp_teste' : 'teentokapp_producao';
$usuario = (strstr($_SERVER['HTTP_HOST'], 'teste'))? 'teentokapp_teste' : 'teentokapp_producao';

define('DRIVER','mysql'); // Servidor de Banco de Dados
define('SERVIDOR','localhost'); // Servidor de Banco de Dados
define('BANCO',$banco); // Banco
define('USUARIO',$usuario);  // Usuário de acesso
define('SENHA','7?#RI9fy-5_t');  // Senha de acesso
define('OPCOES',array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => TRUE,PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")); // Opções do Driver

define('ArqLog','_private/arquivo.log');

//define('raiz_site','/var/www/html/apiteste/');
define('raiz_site','/home/teentokapp/www/apiteste/');

//url da imagem deve ser: ImageUpload/NomeTabela/NomeArquivoComId.xxx
define('UrlUpload',raiz_site . 'ImageUpload');

//variável "pimenta" para criptografar senhas
define('pimenta',md5('TryEdu2021'));

//url da api usada
//define('UrlAPI', 'https://api.teentok.com.br/'); //para produção
define('UrlAPI', 'https://apiteste.teentokapp.com.br/'); //para testes

// Função autoload para carga automática de Classes
spl_autoload_register(function($classe) {
  include_once("classes/{$classe}.class.php");
});
?>
