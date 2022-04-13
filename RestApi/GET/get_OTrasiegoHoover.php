<?php
include '../general_connection.php';
if(strpos($permisos,',6,') !== false){
  if(ISSET($_GET['lotes'])){
    $lotes = "SELECT '01' + '02' +(right('000000' + convert(varChar(6),T.NoSerie ),6)) as Etiqueta
    , CASE WHEN A.Nombre is null THEN 'Tanque sin ubicación' ELSE A.Nombre + ', ' + REPLACE(AA.Nombre, 'COSTADO', 'Cos: ') + ', ' +
    REPLACE(S.Nombre, 'FILA', 'F: ') + ',' + REPLACE(P.Nombre, 'TORRE', 'T: ') + ',' + REPLACE(N.Nombre, 'NIVEL', 'N: ') END AS Ubicación,
    convert(varchar(10),T.FechaLLenado,105) as Llenado,T.Litros
              from AA_Almacen A inner Join AA_Area AA on AA.AlmacenId = A.AlmacenID inner Join AA_Seccion S on S.AreaId = AA.AreaId
              inner join AA_Posicion P on P.SeccionID = S.SeccionID inner Join AA_Nivel N on N.PosicionId = P.PosicionID
              inner Join WM_RackLoc RL on RL.NivelID = n.NivelID inner Join WM_Pallet Pa on Pa.RackLocId = RL.RackLocID
              inner Join WM_Tanques T on T.IdPallet = Pa.IdPallet where T.IdEstado<>2
              order by T.NoSerie,A.Consecutivo";
    imprimir($lotes,$conn);
  }else if(ISSET($_GET['fechasLotes'])){//Obtenemos informacion de las ordenes por la fecha
    $fecha = "SELECT DISTINCT(convert(varchar, Fecha, 23)) as Fecha from PR_Orden where IdTipoOp=8 order by Fecha";
    imprimir($fecha,$conn);
  }else if(ISSET($_GET['fechaOrdenes'])){//Obtenemos las ordenes dadas por una fecha en especifico
    $fecha=$_GET['fechaOrdenes'];
    $ordenes = "exec sp_Orden_v2 8,'$fecha'";
    imprimir($ordenes,$conn);
  }else if(ISSET($_GET['todasOrdenes'])){//Obtenemos las ordenes dadas por una fecha en especifico
    $ordenes = "exec sp_Orden_Pendientes 8";
    imprimir($ordenes,$conn);
  }else if(ISSET($_GET['operador'])){//Operadores dado a un grupo
    $operador=$_GET['operador'];
    $usuarios = "SELECT Nombre,IdUsuario as Id from CM_Usuario_WEB where IdGrupo=$operador and Estatus=1";
    imprimir($usuarios,$conn);
  }else if(ISSET($_GET['tanques'])){//Tanques disponibles
    $usuarios = "SELECT IdTanque,Descripcion from CM_Tanque order by case IsNumeric(Codigo) when 1 then Replicate('0', 100 - Len(Codigo)) + Codigo else Codigo end";
    imprimir($usuarios,$conn);
  }else if(ISSET($_GET['barrHist'])){//Tanques disponibles
    $fecha=$_GET['fecha'];
    $tanque=$_GET['tanque'];
    $usuarios = "SELECT OpHis.IdOrden as Orden ,convert(varchar(10),O.Fecha,105) as Vaciado,
    isnull((('01' + right('00' + convert(varChar(2),1),2) + right('000000' + convert(varChar(6),OpHis.Consecutivo),6))),'Sin Asignar') as Etiqueta,
    Al.Descripcion as Alcohol,Datepart(YYYY,L.Recepcion) as Año,cast(OpHis.Capacidad as decimal(16,3)) as Litros,case OpHis.tipoLl when 1 then 'Completo' else 'Parcial' end as Tipo
    from WM_OperacionTQH Op
    left join WM_OperacionTQHDetalle OpDe on Op.IdOperacion = OpDe.IdOperacion
    left join WM_OperacionTQHBarrilHis OpHis on OpHis.IdOperacion=Op.IdOperacion
    inner Join WM_LoteBarrica LB on LB.IdLoteBarica = OpHis.IdLoteBarrica
    inner Join PR_Lote L on L.Idlote = LB.IdLote
    inner join PR_Orden O on OpHis.IdOrden=O.IdOrden
    inner Join CM_Alcohol Al on Al.IdAlcohol = L.IdAlcohol  where OpDe.NoSerie=$tanque and Op.fecha between '$fecha 00:00' and '$fecha 23:59' order by Op.IdOperacion,OpHis.IdOrden";
    imprimir($usuarios,$conn);
  }
}else{
  echo '..Error.. No tienes acceso a esta area';
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
