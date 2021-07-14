<?php
//$usuario=ISSET($_POST['usuario'])?$_POST['usuario']:"null";
//$pass=ISSET($_POST['pass'])?$_POST['pass']:"null";
include '../general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){
  $nombre=$_POST["nombre"];
  $npass=$_POST["npass"];
  if($nombre===""){
    echo "..Error.. El nombre es incorrecto";
  }else{
    if($npass===""){
      $queryPer="UPDATE CM_Usuario set Nombre='$nombre' where Clave='$usuario'";
    }else{
      $npass=base64_decode($npass);
      $queryPer="UPDATE CM_Usuario set Nombre='$nombre',Pass=ENCRYPTBYPASSPHRASE('Pims.2011','$npass')
                 where Clave='$usuario'";
    }
    $resultPer = sqlsrv_query( $conn , $queryPer);
    if($resultPer){
      echo PHP_AES_Cipher::encrypt("Pims.2021","fedcba9876543210",$npass);
    }else{
      echo "..Error.. Hubó un problema al hacer los cambios, por favor intenta de nuevo más tarde";
    }
  }

}else{
  echo '..Error.. Acceso no autorizado';
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
