<?php
//Conexión a la base de datos, modificar con los datos del servidor sql server
const SERVIDOR="DESKTOP-VDMBV97";//Nombre o IP del servidor
const UID="";//Usuario para conectar a la base de datos
const PWD="";//Contraseña para conectar a la base de datos
const DBNOMBRE="AAB_CLNSA";//Nombre de la base de datos
const ENCONTRASENA="Pims.2021";//la contraseña para desencriptar las contraseñas de los usuarios
$connectionInfo = array( "UID"=>UID,
                         "PWD"=>PWD,
                         "Database"=>DBNOMBRE,
                         "CharacterSet" => "UTF-8");
$conn = sqlsrv_connect( SERVIDOR, $connectionInfo);
//Autenticación de usuario por los headers (No modificar)
$headers = apache_request_headers();
include 'encriptacion.php';
include 'funciones_generales.php';
$usuario="";
$pass="";
if(isset($headers["Authorization"])){
  $credenciales = explode(":", base64_decode(substr($headers["Authorization"],6)));
  $usuario=PHP_AES_Cipher::decrypt(ENCONTRASENA,$credenciales[0].":".$credenciales[1]);
  $pass=PHP_AES_Cipher::decrypt(ENCONTRASENA,$credenciales[2].":".$credenciales[3]);
}
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
if(ObtenerCantidad($tsql,$conn)!=1){
  terminarScript($conn,"..Error.. Acceso no autorizado");//El Usuario no está autenticado, por lo tanto terminamos el script
}
include 'revisar_permisos.php';
 ?>
