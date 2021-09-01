<?php
include '../general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){
  include '../revisar_permisos.php';
  $estado=$_POST["estado"];
  $tipo=$_POST["tipo"];
  $respuesta="";
  $ids = explode(",", $_POST["ids"]);
  if($tipo==='1'){//Solicitudes de barriles
    foreach ($ids as $IdSolicitud) {
      if(strpos($permisos,',1,') !== false){
        if($estado=='3'){
          $queryCons="UPDATE ADM_Ajustes set FechaAutorizacion=(SELECT GETDATE()),Autorizador=(select IdUsuario from CM_Usuario where Clave = '$usuario'),
                      Estado='$estado' where IdAjuste='$IdSolicitud'";
          $resultCons = sqlsrv_query( $conn , $queryCons);
          if(!$resultCons){
            $respuesta=$respuesta.$IdSolicitud.",";
          }
        }else if($estado=='2'){
          $barril = "SELECT IdPallet, IdLoteBarica,IdCodificacion,Consecutivo,IdEstado,Capacidad,FechaRevisado,FechaRelleno,NoTapa
                    from ADM_logBAjuste where IdAjuste='$IdSolicitud' and op=2";
          $stmtBarril = sqlsrv_query( $conn , $barril);
          $row = sqlsrv_fetch_array( $stmtBarril, SQLSRV_FETCH_NUMERIC);
          $row[6]=($row[6]==null)?'null':"'".$row[6]->format('Y-m-d')."'";
          $row[7]=($row[7]==null)?'null':"'".$row[7]->format('Y-m-d')."'";
          //Actualiza WM_Barrica
          $queryCons="UPDATE WM_Barrica set IdPallet='$row[0]' ,IdLoteBarrica='$row[1]',IdCodificacion='$row[2]',IdEstado='$row[4]',Capacidad='$row[5]',
                      FechaRevisado=$row[6],FechaRelleno=$row[7],NoTapa='$row[8]' where Consecutivo='$row[3]'";
          $resultCons = sqlsrv_query( $conn , $queryCons);
          if($resultCons){
            //Actualiza el estado de la solicitud
            $queryCons="UPDATE ADM_Ajustes set FechaAutorizacion=(SELECT GETDATE()),Autorizador=(select IdUsuario from CM_Usuario where Clave = '$usuario'),
                        Estado='$estado' where IdAjuste='$IdSolicitud'";
            $resultCons = sqlsrv_query( $conn , $queryCons);
            if(!$resultCons){
              $respuesta=$respuesta.$IdSolicitud.",";
            }
          }else{
            $respuesta=$respuesta.$IdSolicitud.",";
          }
        }
      }else if(strpos($permisos,',2,') !== false){
        if($estado=='3'){
          $queryCons="UPDATE ADM_Ajustes set FechaAutorizacion=(SELECT GETDATE()),Autorizador=(select IdUsuario from CM_Usuario where Clave = '$usuario'),
                      Estado='4' where IdAjuste='$IdSolicitud'";
          $resultCons = sqlsrv_query( $conn , $queryCons);
          if(!$resultCons){
            $respuesta=$respuesta.$IdSolicitud.",";
          }
        }else{
          echo '..Error.. No tienes permisos para procesar cambios';
        }
      }
    }
  }else if($tipo==='2'){//Solicitudes de Tanques hoover
    foreach ($ids as $IdSolicitud) {
      if(strpos($permisos,',1,') !== false){
        if($estado=='3'){
          $queryCons="UPDATE ADM_AjustesTanques set FechaAutorizacion=(SELECT GETDATE()),Autorizador=(select IdUsuario from CM_Usuario where Clave = '$usuario'),
                      Estado='$estado' where IdAjuste='$IdSolicitud'";
          $resultCons = sqlsrv_query( $conn , $queryCons);
          if(!$resultCons){
            $respuesta=$respuesta.$IdSolicitud.",";
          }
        }else if($estado=='2'){
          $barril = "SELECT IdPallet, Litros,FechaLLenado,IdEstado,NoSerie from ADM_logTAjuste where IdAjuste='$IdSolicitud' and op=2";
          $stmtBarril = sqlsrv_query( $conn , $barril);
          $row = sqlsrv_fetch_array( $stmtBarril, SQLSRV_FETCH_NUMERIC);
          $row[2]=($row[2]==null)?'null':"'".$row[2]->format('Y-m-d')."'";
          //Actualiza WM_Tanques
          $queryCons="UPDATE WM_Tanques set IdPallet='$row[0]' ,Litros='$row[1]',FechaLLenado=$row[2],IdEstado='$row[3]'  where NoSerie='$row[4]'";
          $resultCons = sqlsrv_query( $conn , $queryCons);
          if($resultCons){
            //Actualiza el estado de la solicitud
            $queryCons="UPDATE ADM_AjustesTanques set FechaAutorizacion=(SELECT GETDATE()),Autorizador=(select IdUsuario from CM_Usuario where Clave = '$usuario'),
                        Estado='$estado' where IdAjuste='$IdSolicitud'";
            $resultCons = sqlsrv_query( $conn , $queryCons);
            if(!$resultCons){
              $respuesta=$respuesta.$IdSolicitud.",";
            }
          }else{
            $respuesta=$respuesta.$IdSolicitud.",";
          }
        }
      }else if(strpos($permisos,',2,') !== false){
        if($estado=='3'){
          $queryCons="UPDATE ADM_AjustesTanques set FechaAutorizacion=(SELECT GETDATE()),Autorizador=(select IdUsuario from CM_Usuario where Clave = '$usuario'),
                      Estado='4' where IdAjuste='$IdSolicitud'";
          $resultCons = sqlsrv_query( $conn , $queryCons);
          if(!$resultCons){
            $respuesta=$respuesta.$IdSolicitud.",";
          }
        }else{
          echo '..Error.. No tienes permisos para procesar cambios';
        }
      }
    }
  }
  if($respuesta!==""){
    echo '..Error.. Las solicitudes '.$respuesta." tuvieron problemas para ser procesadas, por favor intenta de nuevo más tarde";
  }else{
    echo "Las solicitudes han sido actualizadas correctamente";
  }


}else{
  echo '..Error.. Acceso no autorizado';
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
