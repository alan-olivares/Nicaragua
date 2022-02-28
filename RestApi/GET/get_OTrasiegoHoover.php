<?php
include '../general_connection.php';
if(strpos($permisos,',6,') !== false){
  if(ISSET($_GET['lotes'])){
    $lotes = "SELECT  Distinct A.AlmacenID,A.Consecutivo,A.Nombre as Bod ,year(L.Recepcion) as Fecha_Li , Al.IdAlcohol ,Al.Descripcion as Alcohol,
              C.IdCodificacion, C.Codigo as Barril,count(B.Consecutivo) as Cantidad, sum(B.Capacidad) as Lts
              from AA_Almacen A inner Join AA_Area AA on AA.AlmacenId = A.AlmacenID inner Join AA_Seccion S on S.AreaId = AA.AreaId
              inner join AA_Posicion P on P.SeccionID = S.SeccionID inner Join AA_Nivel N on N.PosicionId = P.PosicionID
              inner Join WM_RackLoc RL on RL.NivelID = n.NivelID inner Join WM_Pallet Pa on Pa.RackLocId = RL.RackLocID
              inner Join WM_Barrica B on B.IdPallet = Pa.IdPallet inner join WM_LoteBarrica LB on LB.IdLoteBarica = B.IdLoteBarrica
              inner join PR_Lote L on L.IdLote = LB.IdLote inner Join CM_Alcohol Al on Al.IdAlcohol = l.IdAlcohol
              inner join CM_CodEdad CE on CE.IdCodEdad= B.IdCodificacion inner join CM_Codificacion C on C.IdCodificacion = CE.IdCodificicacion
              inner Join CM_Edad E on E.IdEdad = CE.IdEdad
              Group by A.AlmacenID,A.Nombre,year(L.Recepcion),Al.IdAlcohol,Al.Descripcion,C.IdCodificacion,C.Codigo,A.Consecutivo
              order by A.Consecutivo,year(L.Recepcion),al.IdAlcohol,c.IdCodificacion";
    imprimir($lotes,$conn);
  }else if(ISSET($_GET['fechasLotes'])){//Obtenemos informacion de las ordenes por la fecha
    $fecha = "SELECT DISTINCT(convert(varchar, Fecha, 23)) as Fecha from PR_Orden where IdTipoOp=7 order by Fecha";
    imprimir($fecha,$conn);
  }else if(ISSET($_GET['fechaOrdenes'])){//Obtenemos las ordenes dadas por una fecha en especifico
    $fecha=$_GET['fechaOrdenes'];
    $ordenes = "exec sp_Orden_v2 7,'$fecha'";
    imprimir($ordenes,$conn);
  }else if(ISSET($_GET['todasOrdenes'])){//Obtenemos las ordenes dadas por una fecha en especifico
    $ordenes = "exec sp_Orden_Pendientes 7";
    imprimir($ordenes,$conn);
  }else if(ISSET($_GET['operador'])){//Operadores dado a un grupo
    $operador=$_GET['operador'];
    $usuarios = "SELECT Nombre,IdUsuario as Id from CM_Usuario_WEB where IdGrupo=$operador";
    imprimir($usuarios,$conn);
  }else if(ISSET($_GET['tanques'])){//Operadores dado a un grupo
    $usuarios = "SELECT IdTanque,NoSerie from WM_Tanques";
    imprimir($usuarios,$conn);
  }
}else{
  echo '..Error.. No tienes acceso a esta area';
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
