<?php
$usuario=ISSET($_GET['usuario'])?$_GET['usuario']:"null";
$pass=ISSET($_GET['pass'])?$_GET['pass']:"null";
include'general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){
  include'revisar_permisos.php';
  if(strpos($permisos,',5,') !== false){
    $configuracion ="select Val1 from CM_Config where IdConfig=5";
    $stmtConfiguracion = sqlsrv_query( $conn , $configuracion);
    $row = sqlsrv_fetch_array( $stmtConfiguracion, SQLSRV_FETCH_NUMERIC);
    echo $row[0];
  }else{
    echo '..Error.. No tienes acceso a esta area';
  }
}else{
  echo '..Error.. Acceso no autorizado';
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
