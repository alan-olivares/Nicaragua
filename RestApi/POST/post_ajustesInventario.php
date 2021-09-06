<?php
include '../general_connection.php';
if(strpos($permisos,',9,') !== false){
  $caso=$_POST["caso"];
  $ids = explode(",", $_POST["ids"]);
  $motivo=$_POST["motivo"];
  $litros=($_POST["litros"]>0?'+'.$_POST["litros"]:$_POST["litros"]);
  $correctos="";
  $errores="";
  if($caso==='barriles'){
    $repetidos="";
    foreach ($ids as $consecutivo) {
      //Verificar que el barril no tenga otras solicitudes pendientes
      $queryCons="SELECT COUNT(*) from ADM_Ajustes where IdBarrica=(select IdBarrica from WM_Barrica where Consecutivo='$consecutivo') AND Estado=1";
      if(ObtenerCantidad($conn , $queryCons)!=0){
        $errores=$errores.$consecutivo." (solicitud pendiente), ";
        //Verificar que el barril no se encuentre en estado vacío
      }else if(ObtenerCantidad($conn , "SELECT COUNT(*) from WM_Barrica where IdEstado=2 and Consecutivo='$consecutivo'")!=0){
        $errores=$errores.$consecutivo." (barril en estado vacío), ";
      }else{
        $query="INSERT into ADM_Ajustes (Evento,IdBarrica,FechaSolicitud,Solicitante,Estado,IdRazon) values
        ('Ajuste litros',(select IdBarrica from WM_Barrica where Consecutivo='$consecutivo'),(SELECT GETDATE()),(select IdUsuario from CM_Usuario where Clave = '$usuario'),1,$motivo); SELECT SCOPE_IDENTITY()";
        $result = sqlsrv_query( $conn , $query);
        if($result){//Si se guardo en ADM_Ajustes
          sqlsrv_next_result($result);
          sqlsrv_fetch($result);
          $IdAjuste= (int)sqlsrv_get_field($result, 0);
          $queryADM_logBAjuste1="INSERT into ADM_logBAjuste (IdAjuste,Op,IdPallet,IdLoteBarica,IdCodificacion,Consecutivo,IdEstado,Capacidad,
          FechaRevisado,FechaRelleno,NoTapa) (select $IdAjuste, 1,IdPallet,IdLoteBarrica,IdCodificacion,$consecutivo,IdEstado,Capacidad,
          FechaRevisado, FechaRelleno,NoTapa from WM_Barrica where Consecutivo='$consecutivo');";
          $resultADM_logBAjuste1 = sqlsrv_query( $conn , $queryADM_logBAjuste1);
          $queryADM_logBAjuste2="INSERT into ADM_logBAjuste (IdAjuste,Op,IdPallet,IdLoteBarica,IdCodificacion,Consecutivo,IdEstado,Capacidad,
          FechaRevisado,FechaRelleno,NoTapa) (select $IdAjuste, 2,IdPallet,IdLoteBarrica,IdCodificacion,$consecutivo,IdEstado,Capacidad+$litros,
          FechaRevisado, FechaRelleno,NoTapa from WM_Barrica where Consecutivo='$consecutivo');";
          $resultADM_logBAjuste2 = sqlsrv_query( $conn , $queryADM_logBAjuste2);
          if($resultADM_logBAjuste1 && $resultADM_logBAjuste2){//Si se guardo los 2 registros en ADM_logBAjuste
            $correctos=$correctos.$IdAjuste.", ";
          }else{//Si hubo un error se borra cualquier registro
            $query="DELETE from ADM_logBAjuste where IdAjuste='$IdAjuste'";
            sqlsrv_query( $conn , $query);
            $query="DELETE from ADM_Ajustes where IdAjuste='$IdAjuste'";
            sqlsrv_query( $conn , $query);
            $errores=$errores.$consecutivo." (desconocido), ";
          }
        }else{
          $errores=$errores.$consecutivo." (desconocido), ";
        }
      }
    }
  }else if($caso==='tanques hoover'){
    foreach ($ids as $NoSerie) {
      //Verificar que el tanque no tenga otras solicitudes pendientes
      $queryCons="SELECT COUNT(*) from ADM_AjustesTanques where IdTanque=(SELECT IdTanque from WM_Tanques where NoSerie='$NoSerie') AND Estado=1";
      if(ObtenerCantidad($conn , $queryCons)!=0){
        $errores=$errores.$NoSerie." (solicitud pendiente), ";
        //Verificar que el tanque no se encuentre en estado vacío
      }else if(ObtenerCantidad($conn , "SELECT COUNT(*) from WM_Tanques where NoSerie='$NoSerie' AND IdEstado=2")!=0){
        $errores=$errores.$NoSerie." (tanque en estado vacío), ";
      }else{
        $query="INSERT into ADM_AjustesTanques (Evento,NoSerie,FechaSolicitud,Solicitante,Estado,IdRazon)
        values ('Ajuste litros',$NoSerie,(SELECT GETDATE()),(select IdUsuario from CM_Usuario where Clave = '$usuario'),1,$motivo); SELECT SCOPE_IDENTITY()";
        $result = sqlsrv_query( $conn , $query);
        if($result){//Si se guardo en ADM_Ajustes
          sqlsrv_next_result($result);
          sqlsrv_fetch($result);
          $IdAjuste= (int)sqlsrv_get_field($result, 0);
          $queryADM_logBAjuste1="INSERT into ADM_logTAjuste(IdAjuste,Op,NoSerie,IdPallet,Litros,FechaLLenado,IdEstado) (SELECT $IdAjuste,1,$NoSerie,IdPallet,Litros,FechaLLenado,IdEstado from WM_Tanques where NoSerie='$NoSerie');";
          $resultADM_logBAjuste1 = sqlsrv_query( $conn , $queryADM_logBAjuste1);
          $queryADM_logBAjuste2="INSERT into ADM_logTAjuste(IdAjuste,Op,NoSerie,IdPallet,Litros,FechaLLenado,IdEstado) (SELECT $IdAjuste,2,$NoSerie,IdPallet,Litros $litros,FechaLLenado,IdEstado from WM_Tanques where NoSerie='$NoSerie');";
          $resultADM_logBAjuste2 = sqlsrv_query( $conn , $queryADM_logBAjuste2);
          if($resultADM_logBAjuste1 && $resultADM_logBAjuste2){//Si se guardo los 2 registros en ADM_logTAjuste
            $correctos=$correctos.$IdAjuste.", ";
          }else{//Si hubo un error se borra cualquier registro
            $query="DELETE from ADM_logTAjuste where IdAjuste='$IdAjuste'";
            sqlsrv_query( $conn , $query);
            $query="DELETE from ADM_AjustesTanques where IdAjuste='$IdAjuste'";
            sqlsrv_query( $conn , $query);
            $errores=$errores.$NoSerie." (desconocido), ";
          }
        }else{
          $errores=$errores.$NoSerie." (desconocido), ";
        }
      }
    }
  }
  if($errores===""){
    echo 'La tarea se realizo correctamente con ID de solicitud(es) '.substr($correctos, 0, -2);
  }else{
    echo 'La tarea tuvo algunos errores donde los '.$caso." ".substr($errores, 0, -2)." tuvieron problemas al realizarse";
  }

}else{
  echo '..Error.. No tienes permiso para solicitar cambios';
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
