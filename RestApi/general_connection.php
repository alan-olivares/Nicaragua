<?php
//Conexión a la base de datos, modificar con los datos del servidor sql server
$serverName = "DESKTOP-VDMBV97";
$uid = "";
$pwd = "";
$databaseName = "AAB_CLNSA";
$connectionInfo = array( "UID"=>$uid,
                         "PWD"=>$pwd,
                         "Database"=>$databaseName,
                         "CharacterSet" => "UTF-8");

$conn = sqlsrv_connect( $serverName, $connectionInfo);
//Autenticación de usuario por los headers (No modificar)
$headers = apache_request_headers();
include 'encriptacion.php';
$usuario="";
$pass="";
if(isset($headers["Authorization"])){
  $credenciales = explode(":", base64_decode(substr($headers["Authorization"],6)));
  $usuario=PHP_AES_Cipher::decrypt("Pims.2021",$credenciales[0].":".$credenciales[1]);
  $pass=PHP_AES_Cipher::decrypt("Pims.2021",$credenciales[2].":".$credenciales[3]);
}


 ?>
