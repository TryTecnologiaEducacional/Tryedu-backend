<?php
class User extends Tabela {
  protected $tabela = 'User';
  protected $chavePrimaria = 'id';
  //protected $campos = array('id','ClassCode','idAccessLevel','UserName','Surname','SchoolYear','Email','Password','Verification','Logged','Avatar','Meta', 'SchoolCode');
  protected $legendas = array();
														 
 public function listarAtual($idLoginAtual){
 	$resultado = $this->consultar("`$this->tabela`.`id` = $idLoginAtual");
   $tmp = [];
   foreach ($resultado->fetchObject() as $key => $value) {
     $tmp[$key] = ($key == 'Password')? '***' : $value;
   }
	return $tmp;
 }

 public function logged($id,$dados){
  //$dados['Logged'] = $ZeroUm;
  //$dados['Token'] = $token;
  $ok = ($this->atualizar($id,$dados)? 1 : 0);
  return $ok;
 }

 public function ScoreUpdate($id,$score){//retorna Score atual
  $rs = $this->listarPorChave($id);
  $dados['Score'] = ((int)$rs->Score + (int)$score);
  if($score !== 0) $ok = $this->atualizar($id,$dados);//se for =0 retorna score sem atualizar
  return $dados['Score'];
 }

   //gera código da família, impedindo de gerar duplicidade.
   public function GetFamilyCode(){
    $i = 1;
    while ($i > 0) {
      $FamilyCode = substr(bin2hex(random_bytes(5)), 1);
      $i = $this->listarQuantidade("FamilyCode = '$FamilyCode'");
    }
    return $FamilyCode;
  }

}
?>