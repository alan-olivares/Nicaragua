<?php
//$usuario=ISSET($_GET['usuario'])?$_GET['usuario']:"null";
//$pass=ISSET($_GET['pass'])?$_GET['pass']:"null";
include '../general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){
  include '../revisar_permisos.php';
  if(strpos($permisos,',6,') !== false){
    if(ISSET($_GET['FSI82493']) && ISSET($_GET['tanque']) && ISSET($_GET['fecha'])){// Reporte Hoja de Análisis de Trasiego
      $tanque=$_GET['tanque'];
      $fecha=$_GET['fecha'];
      $datos = "exec sp_RepFSI82493 '$fecha',$tanque";
      imprimir($datos,$conn);

    }else if(ISSET($_GET['FSI61194']) && ISSET($_GET['tanque']) && ISSET($_GET['fecha'])){// Reporte Remisión Alcoholes de Entrega Blending
      $tanque=$_GET['tanque'];
      $fecha=$_GET['fecha'];
      $datos = "exec sp_RepFSI61194 '$fecha',$tanque";
      imprimir($datos,$conn);

    }else if(ISSET($_GET['FSI82498']) && ISSET($_GET['tanque']) && ISSET($_GET['fecha'])){//
      $tanque=$_GET['tanque'];
      $fecha=$_GET['fecha'];
      $datos = "exec sp_RepFSI82498 '$fecha',$tanque";
      imprimir($datos,$conn);

    }else if(ISSET($_GET['RepOPDetalle']) && ISSET($_GET['fecha']) && ISSET($_GET['ope'])){//
      $ope=$_GET['ope'];
      $fecha=$_GET['fecha'];
      $datos = "exec sp_RepOPDetalle '$ope','$fecha'";
      imprimir($datos,$conn);

    }else if(ISSET($_GET['tanques'])){
      $tanques = "SELECT * from CM_Tanque";
      imprimir($tanques,$conn);
    }else if(ISSET($_GET['tanque'])){
      $tanque=$_GET['tanque'];
      $usuarios = "SELECT * from CM_Tanque where IDTanque=$tanque";
      imprimir($usuarios,$conn);
    }
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
    echo '..Error.. Hubo un error al obtener los datos, intenta de nuevo más tarde';
  }
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
