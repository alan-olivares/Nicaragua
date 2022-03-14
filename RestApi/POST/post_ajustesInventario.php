<?php
include '../general_connection.php';
$correctos="";
$errores="";
$caso="barriles";
if(strpos($permisos,',9,') !== false){
  $motivo=$_POST["motivo"];
  if(ISSET($_POST["ids"])){
    $caso=$_POST["caso"];
    $ids = explode(",", $_POST["ids"]);
    $litros=($_POST["litros"]>0?'+'.$_POST["litros"]:$_POST["litros"]);
    if($caso==='barriles'){
      $repetidos="";
      foreach ($ids as $consecutivo) {
        //Verificar que el barril no tenga otras solicitudes pendientes
        $queryCons="SELECT COUNT(*) from ADM_Ajustes where IdBarrica=(select IdBarrica from WM_Barrica where Consecutivo='$consecutivo') AND Estado=1";
        if(ObtenerCantidad($queryCons,$conn)!=0){
          $errores=$errores.$consecutivo." (solicitud pendiente), ";
          //Verificar que el barril no se encuentre en estado vacío
        }else if(ObtenerCantidad("SELECT COUNT(*) from WM_Barrica where IdEstado=2 and Consecutivo='$consecutivo'",$conn)!=0){
          $errores=$errores.$consecutivo." (barril en estado vacío), ";
        }else{
          saveData($consecutivo,$usuario,$motivo,"Capacidad+".$litros,'Ajuste litros','IdCodificacion',$conn);
        }
      }
    }else if($caso==='tanques hoover'){
      foreach ($ids as $NoSerie) {
        //Verificar que el tanque no tenga otras solicitudes pendientes
        $queryCons="SELECT COUNT(*) from ADM_AjustesTanques where IdTanque=(SELECT IdTanque from WM_Tanques where NoSerie='$NoSerie') AND Estado=1";
        if(ObtenerCantidad( $queryCons,$conn )!=0){
          $errores=$errores.$NoSerie." (solicitud pendiente), ";
          //Verificar que el tanque no se encuentre en estado vacío
        }else if(ObtenerCantidad("SELECT COUNT(*) from WM_Tanques where NoSerie='$NoSerie' AND IdEstado=2",$conn )!=0){
          $errores=$errores.$NoSerie." (tanque en estado vacío), ";
        }else{
          $query="INSERT into ADM_AjustesTanques (Evento,IdTanque,FechaSolicitud,Solicitante,Estado,IdRazon)
          values ('Ajuste litros',(SELECT IdTanque from WM_Tanques where NoSerie=$NoSerie),(SELECT GETDATE()),(select IdUsuario from CM_Usuario_WEB where Clave = '$usuario'),1,$motivo); SELECT SCOPE_IDENTITY()";
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
              $errores=$errores.$NoSerie." (Código: 1), ";
            }
          }else{
            $errores=$errores.$NoSerie." (Código: 2), ";
          }
        }
      }
    }
  }else if(ISSET($_POST["jsonRelleno"]) && ISSET($_POST["uso"])){
    $uso=$_POST["uso"];
    if(isJson($_POST["jsonRelleno"])){
      $data = json_decode($_POST["jsonRelleno"], true);
      foreach ($data as $campo) {
        $consecutivo=$campo['Consecutivo'];
        $orden=$campo['Orden'];
        $litros=$campo['Litros'];
        $capacidad=$campo['Capacidad'];
        $tipo=$campo['Tipo'];
        $tipo=$tipo==='Relleno'?'2':($tipo==='Donador'?'4':($tipo==='Resto'?'5':'-1'));
        $queryCons="SELECT COUNT(*) from ADM_Ajustes where IdBarrica=(select IdBarrica from WM_Barrica where Consecutivo='$consecutivo') AND Estado=1";
        $queryCons2="SELECT COUNT(*) from PR_RegBarril P inner join PR_Orden O on P.IdOrden=O.IdOrden  where P.IdOrden='$orden' and P.Consecutivo='$consecutivo' and O.Fecha between DATEADD(day,-1, GETDATE()) and GETDATE()";
        if(ObtenerCantidad($queryCons,$conn)!=0){//Tiene solicitudes pendientes
          $errores=$errores.$consecutivo." (solicitud pendiente), ";
        }else if(ObtenerCantidad($queryCons2,$conn)==0){//Hace más de 2 días fue su relleno
          $errores=$errores.$consecutivo." (este barril fue rellenado hace más de 1 día), ";
        }else{
          $Cod=ObtenerCantidad("SELECT top 1 IdCodEdad from PR_RegBarril  where Consecutivo='$consecutivo' and IdOrden='$orden' order by IdRegBarril desc",$conn);
          $edad=ObtenerCantidad("SELECT top 1 IdEdad from CM_CodEdad  where IdCodEdad='$Cod'",$conn);
          if(saveDataPrRegBarril($consecutivo,$orden,$litros,$uso,$edad,$tipo,$conn)){
            saveData($consecutivo,$usuario,$motivo,$capacidad,'Ajuste relleno',"(select isnull((select top 1 IdCodEdad from CM_CodEdad where IdCodificicacion='$uso' and IdEdad='$edad'),0))",$conn);
          }else{
            $errores=$errores.$consecutivo." (No perimitió actualizar el registro), ";
          }
        }
      }
    }else{
      echo '..Error.. Insertaste algún caracter no valido';
    }
  }else if(ISSET($_POST["jsonLlenado"]) && ISSET($_POST["uso"])){
    $uso=$_POST["uso"];
    if(isJson($_POST["jsonLlenado"])){
      $data = json_decode($_POST["jsonLlenado"], true);
      foreach ($data as $campo) {
        $consecutivo=$campo['Consecutivo'];
        $orden=$campo['Orden'];
        $litros=$campo['Litros'];
        $queryCons="SELECT COUNT(*) from ADM_Ajustes where IdBarrica=(select IdBarrica from WM_Barrica where Consecutivo='$consecutivo') AND Estado=1";
        $queryCons2="SELECT COUNT(*) from WM_Reg_Rep_llen where IdBarrica=(select IdBarrica from WM_Barrica where Consecutivo='$consecutivo') and Fecha between DATEADD(day,-1, GETDATE()) and GETDATE()";
        if(ObtenerCantidad($queryCons,$conn)!=0){//Tiene solicitudes pendientes
          $errores=$errores.$consecutivo." (solicitud pendiente), ";
        }else if(ObtenerCantidad($queryCons2,$conn)==0){//Hace más de 1 día fue su llenado
          $errores=$errores.$consecutivo." (este barril fue llenado hace más de 1 día), ";
        }else{
          $Cod=ObtenerCantidad("SELECT top 1 IdCodificacion from WM_Barrica  where Consecutivo='$consecutivo'",$conn);
          $edad=ObtenerCantidad("SELECT top 1 IdEdad from CM_CodEdad  where IdCodEdad='$Cod'",$conn);
          if(saveDataPrRegBarril($consecutivo,$orden,$litros,$uso,$edad,'1',$conn)){//Si se pudo actualizar el registro
            saveData($consecutivo,$usuario,$motivo,$litros,'Ajuste llenado',"(select isnull((select top 1 IdCodEdad from CM_CodEdad where IdCodificicacion='$uso' and IdEdad='$edad'),0))",$conn);
          }else{
            $errores=$errores.$consecutivo." (No perimitió actualizar el registro), ";
          }
        }
      }
    }else{
      echo '..Error.. Insertaste algún caracter no valido';
    }
  }

  if($errores===""){
    echo 'La tarea se realizo correctamente con ID de solicitud(es) '.substr($correctos, 0, -2);
  }else{
    echo '..Error.. La tarea tuvo algunos errores donde los '.$caso." ".substr($errores, 0, -2)." tuvieron problemas al realizarse";
  }
}else{
  echo '..Error.. No tienes permiso para solicitar cambios';
}
sqlsrv_close($conn); //Close the connnectiokn first

function saveData($consecutivo,$usuario,$motivo,$litros,$caso,$IdCodificacion,$conn){
  global $correctos,$errores;
  $query="INSERT into ADM_Ajustes (Evento,IdBarrica,FechaSolicitud,Solicitante,Estado,IdRazon) values
  ('$caso',(select IdBarrica from WM_Barrica where Consecutivo='$consecutivo'),(SELECT GETDATE()),(select IdUsuario from CM_Usuario_WEB where Clave = '$usuario'),1,$motivo); SELECT SCOPE_IDENTITY()";
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
    FechaRevisado,FechaRelleno,NoTapa) (select $IdAjuste, 2,IdPallet,IdLoteBarrica,$IdCodificacion,$consecutivo,IdEstado,$litros,
    FechaRevisado, FechaRelleno,NoTapa from WM_Barrica where Consecutivo='$consecutivo');";
    $resultADM_logBAjuste2 = sqlsrv_query( $conn , $queryADM_logBAjuste2);
    if($resultADM_logBAjuste1 && $resultADM_logBAjuste2){//Si se guardo los 2 registros en ADM_logBAjuste
      $correctos=$correctos.$IdAjuste.", ";
    }else{//Si hubo un error se borra cualquier registro
      $query="DELETE from ADM_logBAjuste where IdAjuste='$IdAjuste'";
      sqlsrv_query( $conn , $query);
      $query="DELETE from ADM_Ajustes where IdAjuste='$IdAjuste'";
      sqlsrv_query( $conn , $query);
      $errores=$errores.$consecutivo." (desconocido 2), ";
    }
  }else{
    $errores=$errores.$consecutivo." (desconocido 1), ";
  }
}
function saveDataPrRegBarril($consecutivo,$Orden,$litros,$uso,$edad,$tipo,$conn){
  $query="UPDATE PR_RegBarril set IdCodEdad=(select isnull((select top 1 IdCodEdad from CM_CodEdad where IdCodificicacion='$uso' and IdEdad='$edad'),0)),Capacidad='$litros' where IdOrden='$Orden' and Consecutivo='$consecutivo' and TipoReg='$tipo'";
  $result = sqlsrv_query( $conn , $query);
  return $result;
}

?>
