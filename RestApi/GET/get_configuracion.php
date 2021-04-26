<?php
$usuario=ISSET($_GET['usuario'])?$_GET['usuario']:"null";
$pass=ISSET($_GET['pass'])?$_GET['pass']:"null";
include'general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){
  include'revisar_permisos.php';
  if(strpos($permisos,',4,') !== false){
    $configuracion ="select IdConfig,Descripcion,Val1 from CM_Config";
    $stmtConfiguracion = sqlsrv_query( $conn , $configuracion);
    if($stmtConfiguracion){
      $result = array();
      do {
        while ($row = sqlsrv_fetch_array($stmtConfiguracion, SQLSRV_FETCH_ASSOC)){
          $result[] = array_map("utf8_encode",$row);
        }
      } while (sqlsrv_next_result($stmtConfiguracion));

      sqlsrv_free_stmt($stmtConfiguracion);
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }
  }else{
    echo '..Error.. No tienes acceso a esta area';
  }
}else{
  echo '..Error.. Acceso no autorizado';
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
