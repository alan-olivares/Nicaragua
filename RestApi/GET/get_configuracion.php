<?php
include '../general_connection.php';
if(strpos($permisos,',4,') !== false){
  if(ISSET($_GET['configuracion'])){
    $configuracion ="SELECT IdConfig,Descripcion,Val1,Val2 from CM_Config";
    imprimir($configuracion,$conn);
  }else if(ISSET($_GET['motivo'])){
    $configuracion ="SELECT IdRazon,Descripcion from ADM_Razones where IdCaso=".$_GET['motivo'];
    imprimir($configuracion,$conn);
  }else if(ISSET($_GET['proveedores'])){
    $configuracion ="SELECT IdProveedor,Codigo,Descripcion from CM_Proveedor";
    imprimir($configuracion,$conn);
  }
}else{
  echo '..Error.. No tienes acceso a esta area';
}

sqlsrv_close($conn); //Close the connnectiokn first
?>
