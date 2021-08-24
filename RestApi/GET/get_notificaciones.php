<?php
//$usuario=ISSET($_GET['usuario'])?$_GET['usuario']:"null";
//$pass=ISSET($_GET['pass'])?$_GET['pass']:"null";
include '../general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmtIncio = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmtIncio, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){
  include '../revisar_permisos.php';
  if(strpos($permisos,',1,') !== false){
    $cantidad = "SELECT (select COUNT(*) from ADM_Ajustes where Estado='1')+(select COUNT(*) from ADM_AjustesTanques where Estado='1')";
    $stmtNumero = sqlsrv_query( $conn , $cantidad);
    $row2 = sqlsrv_fetch_array( $stmtNumero, SQLSRV_FETCH_NUMERIC);
    echo $row2[0];
  }else{
    echo '0';
  }
}else{
  echo '..Error.. Acceso no autorizado';
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
