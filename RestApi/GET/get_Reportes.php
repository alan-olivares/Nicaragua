<?php
include '../general_connection.php';
if(strpos($permisos,',10,') !== false){
  if(ISSET($_GET['gerencia'])){// Reporte de Gerencia
    $datos = "exec sp_RepGerencia";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['inventarioDeta'])){
    $bodegas=$_GET['bodega'];
    $Alcohol=$_GET['alcohol'];
    $Allenada=$_GET['llenada'];
    $Uso=$_GET['uso'];
    $datos = "exec sp_InvParamDetalle_v2 '$bodegas','$Alcohol','$Allenada','$Uso'";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['inventario'])){
    $bodegas=$_GET['bodega'];
    $Alcohol=$_GET['alcohol'];
    $Allenada=$_GET['llenada'];
    $Uso=$_GET['uso'];
    $datos = "exec sp_InvParam_v2 '$bodegas','$Alcohol','$Allenada','$Uso'";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['barriles_plantel'])){//Reporte de barriles de plantel
    $datos = "exec sp_BarrilPlantel";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['tanques_plantel'])){//Reporte de tanques de plantel
    $datos = "SELECT ROW_NUMBER() OVER(ORDER BY AA.AreaId asc) as 'Num', A.AlmacenID,AA.AreaId,  AA.Nombre as Plantel,
    count(ta.IdTanque) as Tanques from AA_Almacen A inner Join AA_Area AA on AA.AlmacenId = A.AlmacenID
				  inner Join AA_Seccion S on S.AreaId = AA.AreaId inner join AA_Posicion P on P.SeccionID = S.SeccionID
				  inner Join AA_Nivel N on N.PosicionId = P.PosicionID inner Join WM_RackLoc RL on RL.NivelID = n.NivelID
				  inner Join WM_Pallet Pa on Pa.RackLocId = RL.RackLocID
				  left join WM_Tanques Ta on Pa.IdPallet=Ta.IdPallet where A.AlmacenID = 13 and AA.AreaId <> 45 group by A.AlmacenID,AA.AreaId,  AA.Nombre";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['llenados'])){//Reporte de
    $fecha1=$_GET['fecha1'];
    $fecha2=$_GET['fecha2'];
    $datos = "exec sp_RepOPDetalleLlenEncWeb_v2 '$fecha1' , '$fecha2'";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['llenadosT2'])){//Reporte de
    $fecha1=$_GET['fecha1'];
    $fecha2=$_GET['fecha2'];
    $datos = "exec sp_RepOPDetalleLlenWeb_v2_v2 '$fecha1' , '$fecha2'";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['rellenados'])){//Reporte de rellenados
    $fecha1=$_GET['fecha1'];
    $fecha2=$_GET['fecha2'];
    $datos = "exec sp_RepOPRellenoEncWeb_v2 '$fecha1' , '$fecha2'";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['rellenadosT2'])){//Reporte de rellenados tabla detalles
    $fecha1=$_GET['fecha1'];
    $fecha2=$_GET['fecha2'];
    $datos = "exec sp_RepOPRellenoWeb_v2 '$fecha1' , '$fecha2'";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['trasiego'])){//Reporte de trasiego
    $fecha1=$_GET['fecha1'];
    $fecha2=$_GET['fecha2'];
    $datos = "exec sp_RepOPDetalleEncWeb_v2 5, '$fecha1' , '$fecha2'";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['trasiegoT2'])){//Reporte de trasiego tabla detalles
    $fecha1=$_GET['fecha1'];
    $fecha2=$_GET['fecha2'];
    $datos = "exec sp_RepOPDetalleWeb_v2_v2 5, '$fecha1' , '$fecha2'";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['trasiegoHoover'])){//Reporte de trasiego hoover
    $fecha1=$_GET['fecha1'];
    $fecha2=$_GET['fecha2'];
    $datos = "SELECT  convert(varchar,Op.fecha,105) as Fecha,	OpDe.NoSerie as Tanque,	Al.Descripcion as Alcohol,	Datepart(YYYY,L.Recepcion) as Año
    ,	C.Codigo as Uso,	case OpHis.tipoLl when 1 then 'Completo' else 'Parcial' end as Tipo,	CONVERT(varchar(25), cast(( COUNT(C.Codigo) ) as numeric), 1) as Barriles
    ,	CONVERT(varchar(25), cast(( Sum(OpHis.Capacidad) ) as money), 1) as Litros from WM_OperacionTQH Op
    left join WM_OperacionTQHDetalle OpDe on Op.IdOperacion = OpDe.IdOperacion left join WM_OperacionTQHBarrilHis OpHis on OpHis.IdOperacion=Op.IdOperacion
    left Join CM_CodEdad CE on CE.IdCodEdad = OpHis.IdCodificacion left Join CM_Codificacion C on C.IdCodificacion = CE.IdCodificicacion
    inner Join WM_LoteBarrica LB on LB.IdLoteBarica = OpHis.IdLoteBarrica inner Join PR_Lote L on L.Idlote = LB.IdLote
    inner join PR_Orden O on OpHis.IdOrden=O.IdOrden inner Join CM_Alcohol Al on Al.IdAlcohol = L.IdAlcohol
    where Op.fecha between '$fecha1 00:00' and '$fecha2 23:59'
    Group by convert(varchar,Op.fecha,105),Datepart(YYYY,L.Recepcion),Al.Descripcion,OpDe.NoSerie,C.Codigo,case OpHis.tipoLl when 1 then 'Completo' else 'Parcial' end
    order by convert(varchar,Op.fecha,105),OpDe.NoSerie";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['trasiegoHooverDet'])){//Reporte de trasiego hoover
    $fecha1=$_GET['fecha1'];
    $fecha2=$_GET['fecha2'];
    $datos = "SELECT Op.IdOperacion,isnull((('01' + right('00' + convert(varChar(2),2),2) + right('000000' + convert(varChar(6),OpDe.NoSerie),6))),'Sin Asignar') as Etiqueta,
    OpDe.Litros,convert(varchar,OpDe.FechaLLenado,105) as FechaLLenado,OpHis.IdOrden,
    isnull((('01' + right('00' + convert(varChar(2),1),2) + right('000000' + convert(varChar(6),OpHis.Consecutivo),6))),'Sin Asignar') as EtiquetaBarr,convert(varchar(10),O.Fecha,105) as Vaciado,
    Al.Descripcion,Datepart(YYYY,L.Recepcion) as Recepcion, C.Codigo as Uso,OpHis.NoTapa,OpHis.Capacidad,case OpHis.tipoLl when 1 then 'Completo' else 'Parcial' end as Tipo
    from WM_OperacionTQH Op left join WM_OperacionTQHDetalle OpDe on Op.IdOperacion = OpDe.IdOperacion
    left join WM_OperacionTQHBarrilHis OpHis on OpHis.IdOperacion=Op.IdOperacion left Join CM_CodEdad CE on CE.IdCodEdad = OpHis.IdCodificacion
	  left Join CM_Codificacion C on C.IdCodificacion = CE.IdCodificicacion inner Join WM_LoteBarrica LB on LB.IdLoteBarica = OpHis.IdLoteBarrica
    inner Join PR_Lote L on L.Idlote = LB.IdLote inner join PR_Orden O on OpHis.IdOrden=O.IdOrden
    inner Join CM_Alcohol Al on Al.IdAlcohol = L.IdAlcohol  where Op.fecha between '$fecha1 00:00' and '$fecha2 23:59' order by Op.IdOperacion,OpHis.IdOrden,OpHis.NoTapa";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['detallesXBarril'])){//Reporte de descripcion
    $almacen=$_GET['almacen'];
    $area=$_GET['area'];
    $seccion=$_GET['seccion'];
    $alcohol=$_GET['alcohol'];
    $codificacion=$_GET['codificacion'];
    $fecha=$_GET['fecha'];
    $datos = "exec sp_InfoBarrilDetalle3_v2 '$almacen','$area','$seccion','$alcohol','$codificacion','$fecha'";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['detallesTanquesPlantel'])){//Reporte de descripcion_tanques
    $almacen=$_GET['almacen'];
    $area=$_GET['area'];
    $datos = "exec sp_TanquePlantelDetalle '$almacen','$area'";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['detallesBarrilesPlantel'])){//Reporte de descripcion_bodegas
    $almacen=$_GET['almacen'];
    $area=$_GET['area'];
    $codificacion=$_GET['codificacion'];
    $datos = "exec sp_BarrilPlantelDetalle_v2 '$almacen','$area','$codificacion'";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['almacenes'])){
    $datos = "SELECT AlmacenId,Nombre from AA_Almacen ".($_GET['almacenes']!=="true"?"where AlmacenId in(".$_GET['almacenes'].")":"")." order by Consecutivo";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['alcohol'])){
    $datos = "SELECT IdAlcohol,Descripcion from CM_Alcohol Where IdAlcohol <> 4 ".($_GET['alcohol']!=="true"?"and IdAlcohol in(".$_GET['alcohol'].")":"")." order by Descripcion";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['Allenada'])){
    $datos = "SELECT Distinct YEAR(Recepcion) as Anio from PR_Lote order by YEAR(Recepcion)";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['uso'])){
    $datos = "SELECT IdCodificacion,Codigo from CM_Codificacion ".($_GET['uso']!=="true"?"where IdCodificacion in(".$_GET['uso'].")":"")." order by Codigo";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['edades'])){
    $datos = "exec sp_ListaEdad_v2";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['almacenesTod'])){
    $datos = "DECLARE @csv VARCHAR(1000) SELECT @csv = COALESCE(@csv + ',', '') + Nombre FROM AA_Almacen A ".($_GET['almacenesTod']!=="true"?"where A.AlmacenId in(".$_GET['almacenesTod'].")":"")."  SELECT @csv as Nombres";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['alcoholTod'])){
    $datos = "DECLARE @csv VARCHAR(1000) SELECT @csv = COALESCE(@csv + ',', '') + Descripcion FROM CM_Alcohol A Where A.IdAlcohol <> 4 ".($_GET['alcoholTod']!=="true"?"and A.IdAlcohol in(".$_GET['alcoholTod'].")":"")." SELECT @csv as Nombres";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['usoTod'])){
    $datos = "DECLARE @csv VARCHAR(1000) SELECT @csv = COALESCE(@csv + ',', '') + Codigo FROM CM_Codificacion A ".($_GET['usoTod']!=="true"?"where A.IdCodificacion in(".$_GET['usoTod'].")":"")." SELECT @csv as Nombres";
    imprimir($datos,$conn);
  }

}else{
  echo '..Error.. No tienes acceso a esta area';
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
