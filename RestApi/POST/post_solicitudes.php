<?php
include '../general_connection.php';
$estado=$_POST["estado"];
$tipo=$_POST["tipo"];
$respuesta="";
$ids = explode(",", $_POST["ids"]);
if($tipo==='1'){//Solicitudes de barriles
  foreach ($ids as $IdSolicitud) {
    if(strpos($permisos,',1,') !== false){
      if($estado=='3'){
        $queryCons="UPDATE ADM_Ajustes set FechaAutorizacion=(SELECT GETDATE()),Autorizador=(select IdUsuario from CM_Usuario_WEB where Clave = '$usuario'),
                    Estado='$estado' where IdAjuste='$IdSolicitud'";
        $resultCons = sqlsrv_query( $conn , $queryCons);
        if(!$resultCons){
          $respuesta=$respuesta.$IdSolicitud.",";
        }
      }else if($estado=='2'){
        //Actualiza WM_Barrica
        $queryCons="UPDATE B set B.IdPallet=adm.IdPallet ,B.IdLoteBarrica=adm.IdLoteBarica,B.IdCodificacion=(case when adm.IdCodificacion is not null then adm.IdCodificacion else 0 end),
        B.IdEstado=adm.IdEstado,B.Capacidad=adm.Capacidad,B.FechaRevisado=adm.FechaRevisado, B.FechaRelleno=adm.FechaRelleno,B.NoTapa=adm.NoTapa
        FROM WM_Barrica B INNER JOIN ADM_logBAjuste adm ON adm.Consecutivo = B.Consecutivo where adm.Op=2 and adm.IdAjuste='$IdSolicitud'";
        $resultCons = sqlsrv_query( $conn , $queryCons);
        if($resultCons){
          //Actualiza el estado de la solicitud
          $queryCons="UPDATE ADM_Ajustes set FechaAutorizacion=(SELECT GETDATE()),Autorizador=(select IdUsuario from CM_Usuario_WEB where Clave = '$usuario'),
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
        $queryCons="UPDATE ADM_Ajustes set FechaAutorizacion=(SELECT GETDATE()),Autorizador=(select IdUsuario from CM_Usuario_WEB where Clave = '$usuario'),
                    Estado='4' where IdAjuste='$IdSolicitud'";
        $resultCons = sqlsrv_query( $conn , $queryCons);
        if(!$resultCons){
          $respuesta=$respuesta.$IdSolicitud.",";
        }
      }else{
        exit('..Error.. No tienes permisos para procesar cambios');
      }
    }
  }
}else if($tipo==='2'){//Solicitudes de Tanques hoover
  foreach ($ids as $IdSolicitud) {
    if(strpos($permisos,',1,') !== false){
      if($estado=='3'){
        $queryCons="UPDATE ADM_AjustesTanques set FechaAutorizacion=(SELECT GETDATE()),Autorizador=(select IdUsuario from CM_Usuario_WEB where Clave = '$usuario'),
                    Estado='$estado' where IdAjuste='$IdSolicitud'";
        $resultCons = sqlsrv_query( $conn , $queryCons);
        if(!$resultCons){
          $respuesta=$respuesta.$IdSolicitud.",";
        }
      }else if($estado=='2'){
        //Actualiza WM_Tanques
        $queryCons="UPDATE T set T.IdPallet=adm.IdPallet ,T.Litros=adm.Litros,T.FechaLLenado=adm.FechaLLenado,T.IdEstado=adm.IdEstado from WM_Tanques T
        INNER JOIN ADM_logTAjuste adm on adm.NoSerie=T.NoSerie where adm.Op=2 and adm.IdAjuste='$IdSolicitud'";
        $resultCons = sqlsrv_query( $conn , $queryCons);
        if($resultCons){
          //Actualiza el estado de la solicitud
          $queryCons="UPDATE ADM_AjustesTanques set FechaAutorizacion=(SELECT GETDATE()),Autorizador=(select IdUsuario from CM_Usuario_WEB where Clave = '$usuario'),
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
        $queryCons="UPDATE ADM_AjustesTanques set FechaAutorizacion=(SELECT GETDATE()),Autorizador=(select IdUsuario from CM_Usuario_WEB where Clave = '$usuario'),
                    Estado='4' where IdAjuste='$IdSolicitud'";
        $resultCons = sqlsrv_query( $conn , $queryCons);
        if(!$resultCons){
          $respuesta=$respuesta.$IdSolicitud.",";
        }
      }else{
        exit('..Error.. No tienes permisos para procesar cambios');
      }
    }
  }
}
if($respuesta!==""){
  echo '..Error.. Las solicitudes '.$respuesta." tuvieron problemas para ser procesadas, por favor intenta de nuevo más tarde";
}else{
  echo "Las solicitudes han sido actualizadas correctamente";
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
