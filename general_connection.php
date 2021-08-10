<?php
$serverName = "DESKTOP-VDMBV97";
$uid = "";
$pwd = "";
$databaseName = "AAB_CLNSA";
$connectionInfo = array( "UID"=>$uid,
                         "PWD"=>$pwd,
                         "Database"=>$databaseName);

$conn = sqlsrv_connect( $serverName, $connectionInfo);
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
