<?php
include 'credenciales.php';
const ENCONTRASENA="Pims.2021";//la contraseña para desencriptar las contraseñas de los usuarios
$connectionInfo = array( "UID"=>base64_decode(UID),
                         "PWD"=>base64_decode(PWD),
                         "Database"=>base64_decode(DBNOMBRE),
                         "CharacterSet" => "UTF-8");
$conn = sqlsrv_connect( base64_decode(SERVIDOR), $connectionInfo);
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
  terminarScript($conn,"..Desautorizado.. Acceso no autorizado");//El Usuario no está autenticado, por lo tanto terminamos el script
}
include 'revisar_permisos.php';
 ?>
