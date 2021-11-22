<?php
include '../general_connection.php';
if(strpos($permisos,',6,') !== false){
  $lotes = "exec sp_LoteRevision_v2";
  imprimir($lotes,$conn);
}else{
  echo '..Error.. No tienes acceso a esta area';
}
sqlsrv_close($conn);
?>
