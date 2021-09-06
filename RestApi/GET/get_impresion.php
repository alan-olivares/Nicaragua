<?php
include '../general_connection.php';
if(strpos($permisos,',5,') !== false){
  $configuracion ="select Val1 from CM_Config where IdConfig=5";
  $stmtConfiguracion = sqlsrv_query( $conn , $configuracion);
  $row = sqlsrv_fetch_array( $stmtConfiguracion, SQLSRV_FETCH_NUMERIC);
  echo $row[0];
}else{
  echo '..Error.. No tienes acceso a esta area';
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
