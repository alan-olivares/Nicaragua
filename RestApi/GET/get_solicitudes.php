<?php
header('Content-Type: application/json; charset=utf-8');
$usuario=$_GET["usuario"];
$pass=$_GET["pass"];
include'general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){
  include'revisar_permisos.php';
  if(strpos($permisos,',2,') !== false || strpos($permisos,',1,') !== false){
    if(ISSET($_GET['estado'])){
      $estado=$_GET["estado"];
      if(strpos($permisos,',1,')!== false){
        $barril = "select ad.IdAjuste,ad.Evento,b.Consecutivo,ad.FechaSolicitud,u.Nombre,ad.FechaAutorizacion,
  		  (select Nombre from CM_Usuario where IdUsuario=ad.Autorizador) as Autorizador,ES.Descripcion from ADM_Ajustes ad
        left join CM_Usuario u on ad.Solicitante=u.IdUsuario
        left join WM_Barrica b on ad.IdBarrica=b.IdBarrica
        left join ADM_Estados ES on ES.IdEstado=ad.Estado where Estado='$estado'";
      }else{
        $barril = "select ad.IdAjuste,ad.Evento,b.Consecutivo,ad.FechaSolicitud,u.Nombre,ad.FechaAutorizacion,
  		  (select Nombre from CM_Usuario where IdUsuario=ad.Autorizador) as Autorizador,ES.Descripcion from ADM_Ajustes ad
        left join CM_Usuario u on ad.Solicitante=u.IdUsuario
        left join WM_Barrica b on ad.IdBarrica=b.IdBarrica
        left join ADM_Estados ES on ES.IdEstado=ad.Estado where Estado='$estado' and u.Clave='$usuario'";
      }
      $stmtBarril = sqlsrv_query( $conn , $barril);
      if($stmtBarril){
        $result = "[";

        while( $row = sqlsrv_fetch_array( $stmtBarril, SQLSRV_FETCH_NUMERIC) ) {
          if($row[3]==null){$row[3]= '';}else{$row[3]= $row[3]->format('Y-m-d H:i');}
          if($row[5]==null){$row[5]= '';}else{$row[5]= $row[5]->format('Y-m-d H:i');}
          if($row[0]==null){$row[0]= "\"\"";}
          if($row[2]==null){$row[2]= "\"\"";}
          $result=$result. "{\"IdAjuste\":".$row[0].","."\"Evento\":\"".$row[1]."\",".
            "\"Consecutivo\":".$row[2].","."\"FechaSolicitud\":\"".$row[3]."\","."\"Solicitante\":\"".utf8_encode($row[4])."\",".
            "\"FechaAutorizacion\":\"".$row[5]."\","."\"Autorizador\":\"".utf8_encode($row[6])."\","."\"Estado\":\"".$row[7]."\"},";
        }
        if(strlen($result)>1){
          $result = substr($result, 0, -1);
        }
        $result=$result."]";
        echo $result;
      }else{
        echo '..Error.. La tabla no pudó ser cargada, intenta de nuevo más tarde';
      }

      sqlsrv_free_stmt($stmtBarril);
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
      $stmtFila = sqlsrv_query( $conn , $barril);
      if($stmtFila){
        $result = array();
        do {
          while ($row = sqlsrv_fetch_array($stmtFila, SQLSRV_FETCH_ASSOC)){
            $result[] = array_map("utf8_encode",$row);
          }
        } while (sqlsrv_next_result($stmtFila));

        sqlsrv_free_stmt($stmtFila);
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        //echo json_decode($result,false,512,JSON_UNESCAPED_UNICODE);
      }else{
        echo '..Error.. La tabla no pudó ser cargada, intenta de nuevo más tarde';
      }

    }
  }else{
    echo '..Error.. No tienes acceso a esta area';
  }

}else{
  echo '..Error.. Acceso no autorizado';
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
