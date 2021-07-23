<?php
//$usuario=ISSET($_POST['usuario'])?$_POST['usuario']:"null";
//$pass=ISSET($_POST['pass'])?$_POST['pass']:"null";
include '../general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){//Si es un usuario registrado
  include '../revisar_permisos.php';
  if(strpos($permisos,',9,') !== false){
    $consecutivos = explode(",", $_POST["consecutivos"]);
    $motivo=$_POST["motivo"];
    $litros=$_POST["litros"];
    $repetidos="";
    foreach ($consecutivos as $consecutivo) {
      //Verificar que el barril no tenga otras solicitudes pendientes
      $queryCons="select COUNT(*) from ADM_Ajustes where IdBarrica=(select IdBarrica from WM_Barrica where Consecutivo='$consecutivo') AND Estado=1";
      $resultCons = sqlsrv_query( $conn , $queryCons);
      $row = sqlsrv_fetch_array( $resultCons, SQLSRV_FETCH_NUMERIC);
      if($row[0]==0){
        $query="INSERT into ADM_Ajustes (Evento,IdBarrica,FechaSolicitud,Solicitante,Estado,Codigo,IdRazon)
                                values ('Ajuste litros',(select IdBarrica from WM_Barrica where Consecutivo='$consecutivo'),(SELECT GETDATE()),
                                        (select IdUsuario from CM_Usuario where Clave = '$usuario'),1,'',$motivo); SELECT SCOPE_IDENTITY()";
        $result = sqlsrv_query( $conn , $query);
        sqlsrv_next_result($result);
        sqlsrv_fetch($result);
        $IdAjuste= (int)sqlsrv_get_field($result, 0);
        $queryCons="SELECT IdPallet,IdLoteBarrica,IdCodificacion,IdEstado,Capacidad,CONVERT(varchar(10), FechaRevisado, 120) as FechaRevisado,
        CONVERT(varchar(10), FechaRelleno, 120) as FechaRelleno, NoTapa from WM_Barrica where Consecutivo='$consecutivo'";
        $resultCons = sqlsrv_query( $conn , $queryCons);
        $rowDatosP = sqlsrv_fetch_array( $resultCons, SQLSRV_FETCH_NUMERIC);

        $queryADM_logBAjuste1="INSERT into ADM_logBAjuste (IdAjuste,Op,IdPallet,IdLoteBarica,IdCodificacion,Consecutivo,IdEstado,Capacidad,
        FechaRevisado,FechaRelleno,NoTapa) values ($IdAjuste, 1,$rowDatosP[0],$rowDatosP[1],$rowDatosP[2],$consecutivo,$rowDatosP[3],$rowDatosP[4],
        CONVERT(DAte,'$rowDatosP[5]'), CONVERT(DAte,'$rowDatosP[6]'),$rowDatosP[7]);";
        $resultADM_logBAjuste1 = sqlsrv_query( $conn , $queryADM_logBAjuste1);
        $nuevaCapacidad=$rowDatosP[4]+$litros;
        $queryADM_logBAjuste2="INSERT into ADM_logBAjuste (IdAjuste,Op,IdPallet,IdLoteBarica,IdCodificacion,Consecutivo,IdEstado,Capacidad,
        FechaRevisado,FechaRelleno,NoTapa) values ($IdAjuste, 2,$rowDatosP[0],$rowDatosP[1],$rowDatosP[2],$consecutivo,$rowDatosP[3],$nuevaCapacidad,
        CONVERT(DAte,'$rowDatosP[5]'), CONVERT(DAte,'$rowDatosP[6]'),$rowDatosP[7]);";
        $resultADM_logBAjuste2 = sqlsrv_query( $conn , $queryADM_logBAjuste2);
        if($resultADM_logBAjuste1 && $resultADM_logBAjuste2){//Si se guardo los 2 registros en ADM_logBAjuste
          echo '';
        }else{//Si hubo un error se borra cualquier registro
          $query="DELETE from ADM_logBAjuste where IdAjuste='$IdAjuste'";
          sqlsrv_query( $conn , $query);
          $query="DELETE from ADM_Ajustes where IdAjuste='$IdAjuste'";
          sqlsrv_query( $conn , $query);
          $repetidos=$repetidos.$consecutivo.",";
        }
      }else{
        $repetidos=$repetidos.$consecutivo.",";
      }
    }
    echo $repetidos;
  }else{
    echo '..Error.. No tienes permiso para solicitar cambios';
  }

}else{
  echo '..Error.. Acceso no autorizado';
}
sqlsrv_close($conn); //Close the connnectiokn first

?>
