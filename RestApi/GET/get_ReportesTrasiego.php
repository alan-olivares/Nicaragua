<?php
include '../general_connection.php';
if(strpos($permisos,',6,') !== false){
  if(ISSET($_GET['FSI82493']) && ISSET($_GET['tanque']) && ISSET($_GET['fecha'])){// Reporte Hoja de Análisis de Trasiego
    $tanque=$_GET['tanque'];
    $fecha=$_GET['fecha'];
    $datos = "exec sp_RepFSI82493 '$fecha',$tanque";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['FSI61194']) && ISSET($_GET['tanque']) && ISSET($_GET['fecha'])){// Reporte Remisión Alcoholes de Entrega Blending
    $tanque=$_GET['tanque'];
    $fecha=$_GET['fecha'];
    $datos = "exec sp_RepFSI61194_v2 '$fecha',$tanque";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['FSI82498']) && ISSET($_GET['tanque']) && ISSET($_GET['fecha'])){//
    $tanque=$_GET['tanque'];
    $fecha=$_GET['fecha'];
    $datos = "exec sp_RepFSI82498_v2 '$fecha',$tanque";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['RepOPDetalle']) && ISSET($_GET['fecha']) && ISSET($_GET['ope'])){//
    $ope=$_GET['ope'];
    $fecha=$_GET['fecha'];
    $datos = "exec sp_RepOPDetalle_v2 '$ope','$fecha'";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['tanques'])){
    $tanques = "SELECT IDTanque,Descripcion from CM_Tanque";
    imprimir($tanques,$conn);
  }else if(ISSET($_GET['tanque'])){
    $tanque=$_GET['tanque'];
    $usuarios = "SELECT IDTanque,Codigo,Descripcion,Capacidad,Tipo from CM_Tanque where IDTanque=$tanque";
    imprimir($usuarios,$conn);
  }
}else{
  echo '..Error.. No tienes acceso a esta area';
}
sqlsrv_close($conn);
?>
