<?php
include '../general_connection.php';
if(strpos($permisos,',6,') !== false){
  if(ISSET($_GET['lotes'])){
    $lotes = "exec sp_LoteRelleno_v2";
    imprimir($lotes,$conn);
  }else if(ISSET($_GET['fechasLotes'])){//Obtenemos informacion de las ordenes por la fecha
    $fecha = "SELECT DISTINCT(convert(varchar, Fecha, 23)) as Fecha from PR_Orden where IdTipoOp=3 order by Fecha";
    imprimir($fecha,$conn);
  }else if(ISSET($_GET['fechaOrdenes'])){//Obtenemos las ordenes dadas por una fecha en especifico
    $fecha=$_GET['fechaOrdenes'];
    $ordenes = "exec sp_Orden_v2 3, '$fecha'";
    imprimir($ordenes,$conn);
  }else if(ISSET($_GET['todasOrdenes'])){//Obtenemos las ordenes dadas por una fecha en especifico
    $ordenes = "exec sp_Orden_Pendientes 3";
    imprimir($ordenes,$conn);
  }else if(ISSET($_GET['operador'])){//Operadores dado a un grupo
    $operador=$_GET['operador'];
    $usuarios = "SELECT Nombre,IdUsuario as Id from CM_Usuario_WEB where IdGrupo=$operador and Estatus=1";
    imprimir($usuarios,$conn);
  }else if(ISSET($_GET['avance'])){//Detalles de la orden
    $orden=$_GET['avance'];
    $barriles = "exec sp_BarriDisp '$orden'";
    imprimir($barriles,$conn);
  }else if(ISSET($_GET['avanceDeta'])){//Detalle del proceso de la orden
    $orden=$_GET['avanceDeta'];
    $barriles = "exec sp_avanceOrden '$orden'";
    imprimir($barriles,$conn);
  }else if(ISSET($_GET['barrDispo'])){//Operadores dado a un grupo
    $fila=$_GET['fila'];
    $annio=$_GET['annio'];
    $cod=$_GET['cod'];
    $alcohol=$_GET['alcohol'];
    $tipo=$_GET['tipo'];
    $barriles = "exec sp_BarriDispDeta '$fila','$annio','$cod','$alcohol','$tipo'";
    imprimir($barriles,$conn);
  }
}else{
  echo '..Error.. No tienes acceso a esta area';
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
