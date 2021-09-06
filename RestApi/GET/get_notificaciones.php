<?php
include '../general_connection.php';
if(strpos($permisos,',1,') !== false){
  $cantidad = "SELECT (select COUNT(*) from ADM_Ajustes where Estado='1')+(select COUNT(*) from ADM_AjustesTanques where Estado='1')";
  $stmtNumero = sqlsrv_query( $conn , $cantidad);
  $row2 = sqlsrv_fetch_array( $stmtNumero, SQLSRV_FETCH_NUMERIC);
  echo $row2[0];
}else{
  echo '0';
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
