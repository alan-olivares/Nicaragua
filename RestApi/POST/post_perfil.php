<?php
include '../general_connection.php';
$nombre=$_POST["nombre"];
$npass=$_POST["npass"];
$tema=$_POST["tema"];
if($nombre===""){
  echo "..Error.. El nombre es incorrecto";
}else{
  if($npass===""){
    $queryPer="UPDATE CM_Usuario set Nombre='$nombre',Tema='$tema' where Clave='$usuario'";
  }else{
    $npass=base64_decode($npass);
    $queryPer="UPDATE CM_Usuario set Nombre='$nombre',Pass=ENCRYPTBYPASSPHRASE('Pims.2011','$npass'),Tema='$tema' where Clave='$usuario'";
  }
  $resultPer = sqlsrv_query( $conn , $queryPer);
  if($resultPer){
    echo PHP_AES_Cipher::encrypt(ENCONTRASENA,"fedcba9876543210",$npass);
  }else{
    echo "..Error.. Hubó un problema al hacer los cambios, por favor intenta de nuevo más tarde";
  }
}
sqlsrv_close($conn);
?>
