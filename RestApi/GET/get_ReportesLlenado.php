<?php
include '../general_connection.php';
if(strpos($permisos,',6,') !== false){
  if(ISSET($_GET['OPDetalleLlen']) && ISSET($_GET['fecha'])){// Reporte Detalle de Operaciones Llenado
    $fecha=$_GET['fecha'];
    $datos = "exec sp_RepOPDetalleLlen_v2 '$fecha'";
    imprimir($datos,$conn);
  }else  if(ISSET($_GET['OPDetalleMantGen']) && ISSET($_GET['fecha'])){// Reporte Detalle de Barriles en Mantenimiento
    $fecha=$_GET['fecha'];
    $fecha2=ISSET($_GET['fecha2'])?$_GET['fecha2']:$fecha;
    $datos = "SELECT Case M.IdTipoMant When 1 then 'Cambio de Aro' When 2 Then 'Reparacion Gral' end as 'Reparación',
             C.Codigo as Uso,
             count(M.IdtipoMant) as Total
             from PR_Mantenimiento M inner join WM_Barrica B on B.IdBarrica = M.IdBarrica
             inner Join CM_CodEdad CE on CE.IdCodEdad = B.IdCodificacion
             inner join CM_Codificacion C on C.IdCodificacion = CE.IdCodificicacion
             inner join CM_Usuario_WEB U on U.IdUsuario = M.IdUsuario
             Where Convert(Date,M.Fecha) between Convert(Date,'$fecha') and Convert(Date,'$fecha2')
             group by M.IdTipoMant, C.Codigo order by M.IdTipoMant, C.Codigo";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['OPDetalleMant']) && ISSET($_GET['fecha'])){// Reporte Detalle de Barriles en Mantenimiento
    $fecha=$_GET['fecha'];
    $fecha2=ISSET($_GET['fecha2'])?$_GET['fecha2']:$fecha;
    $datos = "SELECT Convert(varchar(10),M.Fecha,120) as fecha, Case M.IdTipoMant When 1 then 'Cambio de Aro' When 2 Then 'Reparacion Gral' end as 'Reparación',
             C.Codigo as Uso,
             count(M.IdtipoMant) as Total
             from PR_Mantenimiento M inner join WM_Barrica B on B.IdBarrica = M.IdBarrica
             inner Join CM_CodEdad CE on CE.IdCodEdad = B.IdCodificacion
             inner join CM_Codificacion C on C.IdCodificacion = CE.IdCodificicacion
             inner join CM_Usuario_WEB U on U.IdUsuario = M.IdUsuario
             Where Convert(Date,M.Fecha) between Convert(Date,'$fecha') and Convert(Date,'$fecha2')
             group by M.IdTipoMant, C.Codigo,Convert(varchar(10),M.Fecha,120) order by Convert(varchar(10),M.Fecha,120),M.IdTipoMant, C.Codigo";
    imprimir($datos,$conn);

  }else if(ISSET($_GET['OPDetalleMantDet']) && ISSET($_GET['fecha'])){// Reporte Remisión Alcoholes de Entrega Blending
    $fecha=$_GET['fecha'];
    $datos = "SELECT M.Fecha,	isnull((('01' + right('00' + convert(varChar(2),1),2) + right('000000' + convert(varChar(6),B.Consecutivo),6))),'Sin Asignar') as Etiqueta
    ,C.Codigo as Uso,	U.Nombre as Operario,   M.IdTipoMant,	Case M.IdTipoMant When 1 then 'Cambio de Aro' When 2 Then 'Reparacion Gral' end TipoMant
    ,Det.CAro,	Det.CTapas,	Det.CDuela,	Case Det.CepDuela When 0 then 'No' When 1 then 'Si' end As CepDuela,	Case Det.RepCanal When 0 then 'No' When 1 then 'Si' end As RepCanal
    ,	Case Det.CanalNvo When 0 then 'No' When 1 then 'Si' end As CanalNvo from PR_Mantenimiento M inner join WM_Barrica B on B.IdBarrica = M.IdBarrica
    inner Join CM_CodEdad CE on CE.IdCodEdad = B.IdCodificacion inner join CM_Codificacion C on C.IdCodificacion = CE.IdCodificicacion
    inner join CM_Usuario U on U.IdUsuario = M.IdUsuario left join PR_MantAcciones Det on M.IdMantenimiento=Det.IdMantenimiento
    Where Convert(Date,M.Fecha)= Convert(Date,'$fecha') order by M.Fecha,M.IdTipoMant,C.Codigo,B.Consecutivo";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['RepOpRevisado']) && ISSET($_GET['fecha'])){
    $fecha=$_GET['fecha'];
    $datos = "SELECT '01' + '01' +(right('000000' + convert(varChar(6),B.Consecutivo ),6)) as Etiqueta
    ,	CONVERT(varchar(10),L.Recepcion,105) as FechaLote,CONVERT(varchar(10),B.FechaRevisado,105) as FechaRevisado ,	B.NoTapa ,	C.Codigo as Uso,	B.Capacidad ,	Al.Descripcion as Alcohol,
    isnull((select top 1 Bod + ' - ' + Costado + ' - ' + FilaN + ' - T' + Convert(varchar(2),Torre) + ' - N' + Convert(varchar(2),Nivel) from V_Barriles Where IdBarrica = B.IdBarrica ),'Sin Ubicación') as Ubicacion
    from WM_Barrica B inner Join WM_LoteBarrica LB on LB.IdLoteBarica = B.IdLoteBarrica left Join PR_Lote L on L.Idlote = LB.IdLote
    inner Join CM_CodEdad CE on CE.IdCodEdad = B.IdCodificacion inner Join CM_Codificacion C on C.IdCodificacion = CE.IdCodificicacion
    inner Join CM_Alcohol Al on Al.IdAlcohol = L.IdAlcohol Where B.FechaRevisado = CONVERT(date, '$fecha') order by C.Codigo, B.NoTapa";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['RepOpRevisadoTotal']) && ISSET($_GET['fecha'])){
    $fecha=$_GET['fecha'];
    $datos = "SELECT C.Codigo as Uso, 'Total barriles: '+ count(B.Consecutivo)  as Barriles, 'Total Lts: '+sum(B.Capacidad) as Litros
    from WM_Barrica B inner Join WM_LoteBarrica LB on LB.IdLoteBarica = B.IdLoteBarrica
    inner Join CM_CodEdad CE on CE.IdCodEdad = B.IdCodificacion inner Join CM_Codificacion C on C.IdCodificacion = CE.IdCodificicacion
    Where B.FechaRevisado = CONVERT(date, '$fecha') group by C.Codigo";
    imprimir($datos,$conn);
  }
}else{
  echo '..Error.. No tienes acceso a esta area';
}
sqlsrv_close($conn);
?>
