<?php
//$usuario=ISSET($_POST['usuario'])?$_POST['usuario']:"null";
//$pass=ISSET($_POST['pass'])?$_POST['pass']:"null";
include '../general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){
  include '../revisar_permisos.php';
  if(strpos($permisos,',4,') !== false){
    if(isJson($_POST["data"])){
      $data = json_decode($_POST["data"], true);
      foreach ($data as $campo) {
        $query="UPDATE CM_Config set Val1=N'".$campo['valor']."' where IdConfig=".$campo['id'];
        sqlsrv_query( $conn , $query);
      }
      echo 'Configuración actualizada correctamiente';
    }else{
      echo '..Error.. Insertaste algún caracter no valido';
    }
  }else{
    echo '..Error.. No tienes permisos para procesar cambios';
  }

}else{
  echo '..Error.. Acceso no autorizado';
}
sqlsrv_close($conn); //Close the connnectiokn first

function isJson($string) {
 json_decode($string);
 return (json_last_error() == JSON_ERROR_NONE);
}
?>
