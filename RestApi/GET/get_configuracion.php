<?php
//$usuario=ISSET($_GET['usuario'])?$_GET['usuario']:"null";
//$pass=ISSET($_GET['pass'])?$_GET['pass']:"null";
include '../general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){
  include '../revisar_permisos.php';
  if(strpos($permisos,',4,') !== false){
    $configuracion ="select IdConfig,Descripcion,Val1 from CM_Config";
    imprimir($configuracion,$conn);
  }else{
    echo '..Error.. No tienes acceso a esta area';
  }
}else{
  echo '..Error.. Acceso no autorizado';
}

function imprimir($query,$conn){
  $stmt = sqlsrv_query( $conn , $query);
  if($stmt){
    $result = array();
    do {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
           $result[] = $row;
        }
    } while (sqlsrv_next_result($stmt));
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    sqlsrv_free_stmt($stmt);
  }else{
    echo $GeneralError;
  }
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
