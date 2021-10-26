<?php
include '../general_connection.php';
if(strpos($permisos,',2,') !== false || strpos($permisos,',1,') !== false){
  $tipo=$_GET["tipo"];
  if(ISSET($_GET['estado'])){
    $estado=$_GET["estado"];
    if($tipo==='1'){//Barriles
      $barril = "SELECT ad.IdAjuste,ad.Evento,b.Consecutivo,CONVERT(varchar(16),ad.FechaSolicitud, 120)as FechaSolicitud ,u.Nombre as Solicitante,
      CONVERT(varchar(16),ad.FechaAutorizacion, 120) as FechaAutorizacion,
      (select Nombre from CM_Usuario where IdUsuario=ad.Autorizador) as Autorizador,ES.Descripcion as Estado,R.Descripcion from ADM_Ajustes ad
      left join CM_Usuario u on ad.Solicitante=u.IdUsuario
      left join WM_Barrica b on ad.IdBarrica=b.IdBarrica
      left join ADM_Estados ES on ES.IdEstado=ad.Estado
      left join ADM_Razones R on ad.IdRazon=R.IdRazon where Estado='$estado'";
    }else if($tipo==='2'){//Tanques Hoover
      $barril = "SELECT ad.IdAjuste,ad.Evento,T.NoSerie,CONVERT(varchar(16),ad.FechaSolicitud, 120)as FechaSolicitud ,u.Nombre as Solicitante,
      CONVERT(varchar(16),ad.FechaAutorizacion, 120)as FechaAutorizacion,
      (select Nombre from CM_Usuario where IdUsuario=ad.Autorizador) as Autorizador,ES.Descripcion as Estado,R.Descripcion from ADM_AjustesTanques ad
      left join CM_Usuario u on ad.Solicitante=u.IdUsuario
      left join WM_Tanques T on T.IdTanque=ad.IdTanque
      left join ADM_Estados ES on ES.IdEstado=ad.Estado
      left join ADM_Razones R on ad.IdRazon=R.IdRazon where ad.Estado='$estado'";
    }
    if(strpos($permisos,',1,')=== false){
      $barril = $barril." and u.Clave='$usuario'";
    }
    if($estado!=="1"){//Cuando la solicitud no es de pendientes
      $dias = "SELECT Val1 from CM_Config where IdConfig=3";
      $stmtDias = sqlsrv_query( $conn , $dias);
      $rowDias = sqlsrv_fetch_array( $stmtDias, SQLSRV_FETCH_NUMERIC);
      $barril=$barril." and ad.FechaAutorizacion > DATEADD(DAY, -".$rowDias[0].", GETDATE()) order by ad.IdAjuste desc";
    }else{
      $barril=$barril." order by ad.FechaSolicitud";
    }
    imprimir($barril,$conn);
  }else if(ISSET($_GET['idAjuste'])){//Es utilizado para las ventanas de las solicitudes
    $idAjuste=$_GET["idAjuste"];
    $opc=$_GET["opc"];
    if($tipo==='1'){
      $barril = "SELECT De.Consecutivo,De.Capacidad,
      CASE WHEN Am.Nombre is null THEN 'Barril sin ubicación' ELSE CONCAT(Am.Nombre,', ', REPLACE(Ar.Nombre, 'COSTADO', 'Cos: '),', ',REPLACE(Se.Nombre, 'FILA', 'F: '),',', REPLACE(Po.Nombre, 'TORRE', 'T: ') ,',', REPLACE(N.Nombre, 'NIVEL', 'N: ')) END AS Ubicación,
      C.Codigo as Uso,E.Codigo as Edad,es.Descripcion as Estado,convert(varchar, De.FechaRevisado, 23) as 'F.Revisado',
      convert(varchar, De.FechaRelleno, 23) as 'F.Relleno',Datepart(YYYY,L.Recepcion) as Recepción, Al.Descripcion as Alcohol from ADM_logBAjuste De
                  left Join CM_CodEdad CE on CE.IdCodEdad = De.IdCodificacion
                  left Join CM_Codificacion C on C.IdCodificacion = CE.IdCodificicacion
                  left Join CM_Edad E on E.IdEdad = CE.IdEdad
                  left Join CM_Estado ES on Es.IdEstado = De.IdEstado
                  left Join WM_LoteBarrica LB on Lb.IdLoteBarica = De.IdLoteBarica
                  left Join PR_Lote L on L.IdLote = LB.IdLote
                  left Join CM_Alcohol Al on Al.IdAlcohol = L.IdAlcohol
                  left Join WM_Pallet P on P.Idpallet = De.IdPallet
                  left join WM_RackLoc R on P.RackLocID=R.RackLocID left Join AA_Nivel N on R.NivelID=N.NivelID left Join AA_Posicion Po on N.PosicionId=Po.PosicionID
                  left Join AA_Seccion Se on Po.SeccionID=Se.SeccionID left Join AA_Area Ar on Se.AreaId = Ar.AreaId
                  left Join AA_Almacen Am on Ar.AlmacenId=Am.AlmacenID where IdAjuste=$idAjuste and op=$opc";
    }else if($tipo==='2'){
      $barril = "SELECT NoSerie,Litros,
      CASE WHEN Am.Nombre is null THEN 'Tanque sin ubicación' ELSE CONCAT(Am.Nombre,', ', REPLACE(Ar.Nombre, 'COSTADO', 'Cos: '),', ',REPLACE(Se.Nombre, 'FILA', 'F: '),',', REPLACE(Po.Nombre, 'TORRE', 'T: ') ,',', REPLACE(N.Nombre, 'NIVEL', 'N: ')) END AS Ubicación,
      convert(varchar(10), FechaLLenado, 120) as 'F. Llenado',E.Descripcion as Estado
      from ADM_logTAjuste l left join CM_Estado E on l.IdEstado=E.IdEstado left Join WM_Pallet P on P.Idpallet = l.IdPallet
      left join WM_RackLoc R on P.RackLocID=R.RackLocID left Join AA_Nivel N on R.NivelID=N.NivelID left Join AA_Posicion Po on N.PosicionId=Po.PosicionID
      left Join AA_Seccion Se on Po.SeccionID=Se.SeccionID left Join AA_Area Ar on Se.AreaId = Ar.AreaId
      left Join AA_Almacen Am on Ar.AlmacenId=Am.AlmacenID where IdAjuste=$idAjuste and op=$opc";
    }
    imprimir($barril,$conn);
  }
}else{
  echo '..Error.. No tienes acceso a esta area';
}
sqlsrv_close($conn);
?>
