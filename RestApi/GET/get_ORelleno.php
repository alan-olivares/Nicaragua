<?php
include '../general_connection.php';
if(strpos($permisos,',6,') !== false){
  if(ISSET($_GET['lotes'])){
    $lotes = "exec sp_LoteRelleno";
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
    $usuarios = "SELECT Nombre,IdUsuario as Id from CM_Usuario_WEB where IdGrupo=$operador";
    imprimir($usuarios,$conn);
  }else if(ISSET($_GET['avance'])){//Operadores dado a un grupo
    $orden=$_GET['avance'];
    $usuarios = "SELECT Distinct OP.Fecha as Fecha_LL,AL.Descripcion as Alcohol  ,C.Codigo as Uso,AA.Nombre as Bodega,REPLACE(A.Nombre, 'COSTADO', '') as Costado,
    s.Nombre as Fila,OP.Cantidad,COUNT(V.IdBarrica) as 'Barriles disponibles',
    case when(O.IdTipoOP=7) then (select top 1 CONVERT(varchar,TanH.NoSerie) from WM_Tanques TanH where TanH.IdTanque=isnull((Select top 1 IdTanque from PR_Op Where IdOrden = O.IdOrden),0)) when (O.IdTipoOP=5) then (select top 1 Tan.Descripcion from CM_Tanque Tan where Tan.IDTanque=isnull((Select top 1 IdTanque from PR_Op Where IdOrden = O.IdOrden),0)) else 'N/A' end as Tanque
      from PR_Orden O inner Join PR_Op OP on OP.IdOrden = O.IdOrden
    Inner Join CM_Alcohol AL on Al.IdAlcohol = OP.IdAlcohol inner Join CM_Codificacion C on C.IdCodificacion = OP.IdCodificacion
    inner Join AA_Almacen AA On AA.AlmacenID = O.Idalmacen inner Join AA_Area A on A.AlmacenId = AA.AlmacenID and A.AreaId = OP.AreaID
    inner Join AA_Seccion S on S.AreaId = A.AreaId and S.SeccionID = OP.SeccionID
    left join V_Barriles V on V.SeccionID=s.SeccionID and v.AreaId=S.AreaId and V.AlmacenID=A.AlmacenId and V.IdAlcohol = OP.IdAlcohol and DatePArt(YYYY,V.Fecha_Li ) = OP.Fecha and V.IdCodificacion=OP.IdCodificacion
    where  O.Estatus in(0,1,2) and O.IdOrden = $orden group by AL.Descripcion,OP.Fecha,C.Codigo,s.Nombre,Op.Cantidad,A.Nombre,AA.Nombre,O.IdOrden,O.IdTipoOP";
    imprimir($usuarios,$conn);
  }else if(ISSET($_GET['avanceDeta'])){//Operadores dado a un grupo
    $orden=$_GET['avanceDeta'];
    $usuarios = "SELECT Distinct isnull((('01' + right('00' + convert(varChar(2),1),2) + right('000000' + convert(varChar(6),B.Consecutivo),6))),'0') as Etiqueta,
    case when R.TipoReg=2 then 'Relleno' when R.TipoReg=4 then 'Donador' when R.TipoReg=5 then 'Sobrante' else 'Trasiego' end as Operación,B.Capacidad as Litros,
    isnull((select Bod + ' - ' + Costado + ' - ' + FilaN + ' - T' + Convert(varchar(2),Torre) + ' - N' + Convert(varchar(2),Nivel) from V_Barriles Where IdBarrica = B.IdBarrica ),'Sin Ubicación') as Ubicacion
    ,convert(varchar,B.FechaRelleno,105)	as Relleno,B.NoTapa,C.Codigo as Uso,E.Codigo as Edad,Al.Descripcion as Alcohol,es.Descripcion as Estado
    from PR_RegBarril R inner join PR_Orden O on R.IdOrden=O.IdOrden inner join WM_Barrica B on R.IdBarrica=B.IdBarrica
    inner Join WM_Pallet P on P.Idpallet = B.IdPallet left Join CM_CodEdad CE on CE.IdCodEdad = B.IdCodificacion
    left Join CM_Codificacion C on C.IdCodificacion = CE.IdCodificicacion left Join CM_Edad E on E.IdEdad = CE.IdEdad
    left Join WM_LoteBarrica LB on Lb.IdLoteBarica = B.IdLoteBarrica left Join PR_Lote L on L.IdLote = LB.IdLote
    left Join CM_Alcohol Al on Al.IdAlcohol = L.IdAlcohol left Join CM_Estado ES on Es.IdEstado = B.IdEstado
    where O.IdOrden=$orden and O.Estatus in(0,1,2)";
    imprimir($usuarios,$conn);
  }
}else{
  echo '..Error.. No tienes acceso a esta area';
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
