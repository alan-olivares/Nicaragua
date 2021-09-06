<?php
include '../general_connection.php';
if(strpos($permisos,',6,') !== false){
  if(ISSET($_GET['lotes'])){
    $lotes = "exec sp_LoteTrasiego";
    imprimir($lotes,$conn);

  }else if(ISSET($_GET['fechasLotes'])){//Obtenemos informacion de las ordenes por la fecha
    $fecha = "SELECT DISTINCT(convert(varchar, Fecha, 23)) as Fecha from PR_Orden where IdTipoOp=7 order by Fecha";
    imprimir($fecha,$conn);
  }else if(ISSET($_GET['fechaOrdenes'])){//Obtenemos las ordenes dadas por una fecha en especifico
    $fecha=$_GET['fechaOrdenes'];
    $ordenes = "exec sp_Orden 7,'$fecha'";
    imprimir($ordenes,$conn);

  }else if(ISSET($_GET['operador'])){//Operadores dado a un grupo
    $operador=$_GET['operador'];
    $usuarios = "SELECT Nombre,IdUsuario as Id from CM_Usuario where IdGrupo=$operador";
    imprimir($usuarios,$conn);

  }else if(ISSET($_GET['tanques'])){//Operadores dado a un grupo
    $usuarios = "SELECT * from WM_Tanques";
    imprimir($usuarios,$conn);
  }
}else{
  echo '..Error.. No tienes acceso a esta area';
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
