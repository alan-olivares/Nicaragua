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
    $datos = "exec sp_InvParamDetalle '$bodegas','$Alcohol','$Allenada','$Uso'";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['inventario'])){
    $bodegas=$_GET['bodega'];
    $Alcohol=$_GET['alcohol'];
    $Allenada=$_GET['llenada'];
    $Uso=$_GET['uso'];
    $datos = "exec sp_InvParam '$bodegas','$Alcohol','$Allenada','$Uso'";
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
    $datos = "exec sp_RepOPDetalleLlenEncWeb '$fecha1' , '$fecha2'";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['llenadosT2'])){//Reporte de
    $fecha1=$_GET['fecha1'];
    $fecha2=$_GET['fecha2'];
    $datos = "exec sp_RepOPDetalleLlenWeb_v2 '$fecha1' , '$fecha2'";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['rellenados'])){//Reporte de rellenados
    $fecha1=$_GET['fecha1'];
    $fecha2=$_GET['fecha2'];
    $datos = "exec sp_RepOPRellenoEncWeb '$fecha1' , '$fecha2'";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['rellenadosT2'])){//Reporte de rellenados tabla detalles
    $fecha1=$_GET['fecha1'];
    $fecha2=$_GET['fecha2'];
    $datos = "exec sp_RepOPRellenoWeb '$fecha1' , '$fecha2'";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['trasiego'])){//Reporte de trasiego
    $fecha1=$_GET['fecha1'];
    $fecha2=$_GET['fecha2'];
    $datos = "exec sp_RepOPDetalleEncWeb 5, '$fecha1' , '$fecha2'";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['trasiegoT2'])){//Reporte de trasiego tabla detalles
    $fecha1=$_GET['fecha1'];
    $fecha2=$_GET['fecha2'];
    $datos = "exec sp_RepOPDetalleWeb_v2 5, '$fecha1' , '$fecha2'";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['trasiegoHoover'])){//Reporte de trasiego hoover
    $fecha1=$_GET['fecha1'];
    $fecha2=$_GET['fecha2'];
    $datos = "SELECT isnull((('01' + right('00' + convert(varChar(2),2),2) + right('000000' + convert(varChar(6),OpDe.NoSerie),6))),'Sin Asignar') as Etiqueta,
    OpDe.Litros,convert(varchar,OpDe.FechaLLenado,105) as FechaLLenado,OpHis.IdOrden,
    isnull((('01' + right('00' + convert(varChar(2),1),2) + right('000000' + convert(varChar(6),OpHis.Consecutivo),6))),'Sin Asignar') as EtiquetaBarr,
    Al.Descripcion,Datepart(YYYY,L.Recepcion) as Recepcion,OpHis.Capacidad
    from WM_OperacionTQH Op
    left join WM_OperacionTQHDetalle OpDe on Op.IdOperacion = OpDe.IdOperacion
    left join WM_OperacionTQHBarrilHis OpHis on OpHis.IdOperacion=Op.IdOperacion
    inner Join WM_LoteBarrica LB on LB.IdLoteBarica = OpHis.IdLoteBarrica
    inner Join PR_Lote L on L.Idlote = LB.IdLote
    inner Join CM_Alcohol Al on Al.IdAlcohol = L.IdAlcohol  where Op.fecha between '$fecha1 00:00' and '$fecha2 23:59' order by Op.IdOperacion,OpHis.IdOrden";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['detallesXBarril'])){//Reporte de descripcion
    $almacen=$_GET['almacen'];
    $area=$_GET['area'];
    $seccion=$_GET['seccion'];
    $alcohol=$_GET['alcohol'];
    $codificacion=$_GET['codificacion'];
    $fecha=$_GET['fecha'];
    $datos = "exec sp_InfoBarrilDetalle3 '$almacen','$area','$seccion','$alcohol','$codificacion','$fecha'";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['detallesTanquesPlantel'])){//Reporte de descripcion
    $almacen=$_GET['almacen'];
    $area=$_GET['area'];
    $datos = "exec sp_TanquePlantelDetalle '$almacen','$area'";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['detallesBarrilesPlantel'])){//Reporte de descripcion_bodegas
    $almacen=$_GET['almacen'];
    $area=$_GET['area'];
    $codificacion=$_GET['codificacion'];
    $datos = "exec sp_BarrilPlantelDetalle '$almacen','$area','$codificacion'";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['almacenes'])){
    $datos = "SELECT AlmacenId,Nombre from AA_Almacen ".($_GET['almacenes']!=="true"?"where AlmacenId=".$_GET['almacenes']:"")." order by Nombre";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['alcohol'])){
    $datos = "SELECT IdAlcohol,Descripcion from CM_Alcohol Where IdAlcohol <> 4 ".($_GET['alcohol']!=="true"?"and IdAlcohol=".$_GET['alcohol']:"")." order by Descripcion";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['Allenada'])){
    $datos = "SELECT Distinct YEAR(Recepcion) as Anio from PR_Lote order by YEAR(Recepcion)";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['uso'])){
    $datos = "SELECT IdCodificacion,Codigo from CM_Codificacion ".($_GET['uso']!=="true"?"where IdCodificacion=".$_GET['uso']:"")." order by Codigo";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['edades'])){
    $datos = "exec sp_ListaEdad";
    imprimir($datos,$conn);
  }

}else{
  echo '..Error.. No tienes acceso a esta area';
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
