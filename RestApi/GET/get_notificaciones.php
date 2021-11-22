<?php
include '../general_connection.php';
if(strpos($permisos,',1,') !== false){
  $cantidad = "SELECT (select COUNT(*) from ADM_Ajustes where Estado='1')+(select COUNT(*) from ADM_AjustesTanques where Estado='1')";
  echo ObtenerCantidad($cantidad,$conn);
}else{
  echo '0';
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
