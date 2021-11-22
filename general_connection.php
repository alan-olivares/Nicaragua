<?php
include 'RestApi/credenciales.php';
$connectionInfo = array( "UID"=>base64_decode(UID),
                         "PWD"=>base64_decode(PWD),
                         "Database"=>base64_decode(DBNOMBRE),
                         "CharacterSet" => "UTF-8");
$conn = sqlsrv_connect( base64_decode(SERVIDOR), $connectionInfo);
include 'RestApi/encriptacion.php';
//Autenticación de usuario por parametros (No modificar)
$usuario="";
$pass="";
if(ISSET($_GET['aut'])){
  $credenciales = explode(":", base64_decode($_GET['aut']));
  $usuario=PHP_AES_Cipher::decrypt("Pims.2021",$credenciales[0].":".$credenciales[1]);
  $pass=PHP_AES_Cipher::decrypt("Pims.2021",$credenciales[2].":".$credenciales[3]);
}
 ?>
