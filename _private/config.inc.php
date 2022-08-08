<?php
/*
 * Configurações de acesso ao banco de dados
 */
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if (strstr($_SERVER['HTTP_HOST'], 'teste') ) {
  $bancoUser = 'teentok_teste';
  $raiz_site = '/usr/share/nginx/html/apiteste/';
  $UrlAPI = 'https://apiteste.teentok.com.br/';
} else {
  $bancoUser = 'teentok_producao';
  $raiz_site = '/usr/share/nginx/html/api/';
  $UrlAPI = 'https://api.teentok.com.br/';
}
define('DRIVER', 'mysql'); // Servidor de Banco de Dados
define('SERVIDOR', 'bduser.czpyv0z0unpu.sa-east-1.rds.amazonaws.com'); // Servidor de Banco de Dados
define('BANCO', $bancoUser); // Banco
define('USUARIO', 'bduser');  // Usuário de acesso
define('SENHA', 'TryGame[2022');  // Senha de acesso
define('PORTABD', '3306');  // Porta de acesso
//define('SSLCA','/root/.ssh/teentokKey.pem'); //key SSL amazon
define('OPCOES', array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => TRUE, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4")); // Opções do Driver

define('ArqLog', '_private/arquivo.log');

//define('raiz_site','/var/www/html/apiteste/');
define('raiz_site', $raiz_site);

//url da imagem deve ser: ImageUpload/NomeTabela/NomeArquivoComId.xxx
define('UrlUpload', raiz_site . 'ImageUpload');

//variável "pimenta" para criptografar senhas
define('pimenta', md5('TryEdu2021'));

//url da api usada
define('UrlAPI', $UrlAPI);

// Função autoload para carga automática de Classes
spl_autoload_register(function ($classe) {
  include_once("classes/{$classe}.class.php");
});
