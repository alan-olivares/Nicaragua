<?php
//$usuario=ISSET($_GET['usuario'])?$_GET['usuario']:"null";
//$pass=ISSET($_GET['pass'])?$_GET['pass']:"null";
include '../general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){
  include '../revisar_permisos.php';
  if(strpos($permisos,',2,') !== false || strpos($permisos,',1,') !== false){
    if(ISSET($_GET['estado'])){
      $estado=$_GET["estado"];
      $barril = "select ad.IdAjuste,ad.Evento,b.Consecutivo,CONVERT(varchar(16),ad.FechaSolicitud, 120)as FechaSolicitud ,u.Nombre as Solicitante,
      CONVERT(varchar(16),ad.FechaAutorizacion, 120)as FechaAutorizacion,
      (select Nombre from CM_Usuario where IdUsuario=ad.Autorizador) as Autorizador,ES.Descripcion as Estado,R.Descripcion from ADM_Ajustes ad
      left join CM_Usuario u on ad.Solicitante=u.IdUsuario
      left join WM_Barrica b on ad.IdBarrica=b.IdBarrica
      left join ADM_Estados ES on ES.IdEstado=ad.Estado
      left join ADM_Razones R on ad.IdRazon=R.IdRazon where Estado='$estado'";
      if(strpos($permisos,',1,')=== false){
        $barril = $barril." and u.Clave='$usuario'";
      }
      if($estado!=="1"){
        $dias = "select Val1 from CM_Config where IdConfig=3";
        $stmtDias = sqlsrv_query( $conn , $dias);
        $rowDias = sqlsrv_fetch_array( $stmtDias, SQLSRV_FETCH_NUMERIC);
        $barril=$barril." and ad.FechaSolicitud > DATEADD(DAY, -".$rowDias[0].", GETDATE()) order by ad.IdAjuste desc";
      }
      imprimir($barril,$conn);
    }else if(ISSET($_GET['idAjuste'])){//Es utilizado para las ventanas de las solicitudes
      $idAjuste=$_GET["idAjuste"];
      $opc=$_GET["opc"];

      $barril = "select De.Consecutivo,De.Capacidad,De.IdPallet,C.Codigo as Uso,E.Codigo as Edad,es.Descripcion as Estado,convert(varchar, De.FechaRevisado, 23) as Revisado,
                  Datepart(YYYY,L.Recepcion) as Recepcion, Al.Descripcion as Alcohol,convert(varchar, De.FechaRelleno, 23) as Relleno from ADM_logBAjuste De
                  left Join CM_CodEdad CE on CE.IdCodEdad = De.IdCodificacion
                  left Join CM_Codificacion C on C.IdCodificacion = CE.IdCodificicacion
                  left Join CM_Edad E on E.IdEdad = CE.IdEdad
                  left Join CM_Estado ES on Es.IdEstado = De.IdEstado
                  left Join WM_LoteBarrica LB on Lb.IdLoteBarica = De.IdLoteBarica
                  left Join PR_Lote L on L.IdLote = LB.IdLote
                  left Join CM_Alcohol Al on Al.IdAlcohol = L.IdAlcohol
                  where IdAjuste=$idAjuste and op=$opc";
      imprimir($barril,$conn);
    }
  }else{
    echo '..Error.. No tienes acceso a esta area';
  }

}else{
  echo '..Error.. Acceso no autorizado';
}
function imprimir($query,$conn){
  $stmt = sqlsrv_query( $conn , $query);
  if($stmt){
    $result = array();
    do {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
           $result[] = $row;
        }
    } while (sqlsrv_next_result($stmt));
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    sqlsrv_free_stmt($stmt);
  }else{
    echo $GeneralError;
  }
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
