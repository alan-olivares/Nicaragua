<?php
//$usuario=ISSET($_POST['usuario'])?$_POST['usuario']:"null";
//$pass=ISSET($_POST['pass'])?$_POST['pass']:"null";
include '../general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){//Si es un usuario registrado
  include '../revisar_permisos.php';
  if(strpos($permisos,',2,') !== false){
    $evento=$_POST["evento"];
    $motivo=$_POST["motivo"];
    if(ISSET($_POST['restablecer'])){//Esta restableciendo el barril a una versión pasada o se esta poniendo en vacío
      $consecutivo=$_POST["consecutivo"];
      //Verificar que el barril no tenga otras solicitudes pendientes
      $queryCons="SELECT COUNT(*) from ADM_Ajustes where IdBarrica=(select IdBarrica from WM_Barrica where Consecutivo='$consecutivo') AND Estado=1";
      if(getDato($conn,$queryCons)==0){
        $query="INSERT into ADM_Ajustes (Evento,IdBarrica,FechaSolicitud,Solicitante,Estado,Codigo,IdRazon)
        values ('$evento',(select IdBarrica from WM_Barrica where Consecutivo='$consecutivo'),(SELECT GETDATE()),
        (select IdUsuario from CM_Usuario where Clave = '$usuario'),1,'',$motivo); SELECT SCOPE_IDENTITY()";
        $result = sqlsrv_query( $conn , $query);
        sqlsrv_next_result($result);
        sqlsrv_fetch($result);
        $IdAjuste= (int)sqlsrv_get_field($result, 0);
        if($result){//Si se guardo en ADM_Ajustes
          $IdPallet=getDato($conn,"SELECT top 1 IdPallet from WM_Pallet where RackLocID=".$_POST['IdPallet']."  order by IdPallet desc");
          //Insertamos los valores actuales en ADM_logBAjuste
          $queryADM_logBAjuste1="INSERT into ADM_logBAjuste (IdAjuste,Op,IdPallet,IdLoteBarica,IdCodificacion,Consecutivo,IdEstado,Capacidad,FechaRevisado,FechaRelleno,NoTapa)
          (select '$IdAjuste',1, W.IdPallet,W.IdLoteBarrica,E.IdCodEdad,'$consecutivo',W.IdEstado,W.Capacidad,W.FechaRevisado,W.FechaRelleno,W.NoTapa
          from WM_Barrica W left join CM_CodEdad E on W.IdCodificacion=E.IdCodEdad where Consecutivo='$consecutivo')";
          $resultADM_logBAjuste1 = sqlsrv_query( $conn , $queryADM_logBAjuste1);
          if($_POST['restablecer']=='pasado'){//Esta pasando de vacío a lleno, por lo tanto buscamos el último regirtro encontrado en adm_logbarril
            $queryADM_logBAjuste2="INSERT into ADM_logBAjuste (IdAjuste,Op,IdPallet,IdLoteBarica,IdCodificacion,Consecutivo,IdEstado,Capacidad,FechaRevisado,FechaRelleno,NoTapa)
            (SELECT top 1 '$IdAjuste',2,D.IdPallet,D.IdLoteBarrica,D.IdCodificacion,'$consecutivo',1,D.Capacidad,D.Revisado,D.Relleno,D.NoTapa from adm_logbarril L join adm_logbarrildet D on L.logid=D.logid
             where D.Consecutivo='$consecutivo' and D.op=1 and idestado=1 order by D.logid desc)";
            //$queryADM_logBAjuste2="exec sp_ADM_logBAjuste '$IdAjuste', 2,'$rowBRestablecer[0]','$rowBRestablecer[1]','$rowBEdad[0]',$rowBUso[0],'$consecutivo','1','$rowBRestablecer[8]',$rowBRestablecer[4],$rowBRestablecer[5],$rowBRestablecer[7]";
            $resultADM_logBAjuste2 = sqlsrv_query( $conn , $queryADM_logBAjuste2);
          }else if($_POST['restablecer']=='vacio'){//Esta pasando de vacío a lleno, por lo tanto generemos los datos de un barril vacío
            $queryADM_logBAjuste2="INSERT into ADM_logBAjuste (IdAjuste,Op,IdPallet,IdLoteBarica,IdCodificacion,Consecutivo,IdEstado,Capacidad,FechaRevisado,FechaRelleno,NoTapa)
            (select '$IdAjuste',2, '15894',0,E.IdCodEdad,'$consecutivo',2,0,null,null,null
            from WM_Barrica W left join CM_CodEdad E on W.IdCodificacion=E.IdCodEdad where Consecutivo='$consecutivo')";
            //$queryADM_logBAjuste2="exec sp_ADM_logBAjuste '$IdAjuste', 2,'15894',0,'$rowDatosP[2]',$rowDatosP[3],'$consecutivo','2','0',null,null,null";
            $resultADM_logBAjuste2 = sqlsrv_query( $conn , $queryADM_logBAjuste2);
          }else{//Son otras modificaciones
            $queryCons="SELECT W.IdPallet,W.IdLoteBarrica,E.IdCodificicacion,E.IdEdad,W.IdEstado,W.Capacidad,W.FechaRevisado,W.FechaRelleno,W.NoTapa
                    from WM_Barrica W left join CM_CodEdad E on W.IdCodificacion=E.IdCodEdad where Consecutivo='$consecutivo'";
            $resultCons = sqlsrv_query( $conn , $queryCons);
            $rowDatosP = sqlsrv_fetch_array( $resultCons, SQLSRV_FETCH_NUMERIC);
            $IdLoteBarica=(ISSET($_POST['IdLoteBarica']))?$_POST["IdLoteBarica"]:$rowDatosP[1];
            $uso=(ISSET($_POST['uso']))?$_POST["uso"]:$rowDatosP[2];
            $edad=(ISSET($_POST['edad']))?$_POST["edad"]:$rowDatosP[3];
            $IdEstado=(ISSET($_POST['IdEstado']))?$_POST["IdEstado"]:$rowDatosP[4];
            $rowDatosP[6]=($rowDatosP[6]==null)?'null':"'".$rowDatosP[6]->format('Y-m-d')."'";
            $rowDatosP[7]=($rowDatosP[7]==null)?'null':"'".$rowDatosP[7]->format('Y-m-d')."'";
            $Capacidad=(ISSET($_POST['Capacidad']))?$_POST["Capacidad"]:$rowDatosP[5];
            $Revisado=PonerFecha('Revisado',$rowDatosP[6]);
            $Relleno=PonerFecha('Relleno',$rowDatosP[7]);
            $NoTapa=(ISSET($_POST['NoTapa']))?$_POST["NoTapa"]:$rowDatosP[8];
            $queryADM_logBAjuste2="exec sp_ADM_logBAjuste '$IdAjuste', 2,'$rowDatosP[0]','$IdLoteBarica','$uso','$edad','$consecutivo','$IdEstado','$Capacidad',$Revisado,$Relleno,'$NoTapa'";
            $resultADM_logBAjuste2 = sqlsrv_query( $conn , $queryADM_logBAjuste2);
          }
          if($resultADM_logBAjuste1 && $resultADM_logBAjuste2){//Si se guardo los 2 registros en ADM_logBAjuste
            echo 'La solicitud se ha realizado correctamente, ID de solicitud: '.$IdAjuste;
          }else{//Si hubo un error se borra cualquier registro
            $query="DELETE from ADM_logBAjuste where IdAjuste='$IdAjuste'";
            sqlsrv_query( $conn , $query);
            $query="DELETE from ADM_Ajustes where IdAjuste='$IdAjuste'";
            sqlsrv_query( $conn , $query);
            echo '..Error.. Hubó un problema al procesar la tarea, por favor intenta de nuevo más tarde Codigo: 1';
          }
        }else{
          echo '..Error.. Hubó un problema al procesar la tarea, por favor intenta de nuevo más tarde Codigo: 2';
        }
      }else{
        echo '..Error.. Éste barril ya cuenta con solicitudes pendientes de procesar, espera a que sean procesadas';
      }
    }else{
        $consecutivos = explode(",", $_POST["consecutivos"]);
        //Cuenta los barriles que existen en el pallet y en solicitudes pendientes para saber si hay lugar disponible
        $queryCons2="SELECT (select COUNT(*) from WM_Tanques B inner Join WM_Pallet P on P.Idpallet = B.IdPallet Where P.RackLocId = ".$_POST['IdPallet'].")+
        (select COUNT(*) from  ADM_AjustesTanques aj left join ADM_logTAjuste ad on ad.IdAjuste=aj.IdAjuste
      	inner join WM_Pallet P on P.IdPallet=ad.IdPallet where P.RackLocId = ".$_POST['IdPallet']."
      	and aj.Estado=1 and ad.op=2 and (aj.Evento='Mover' or aj.Evento='Agregar'))";
        $queryCons="SELECT (select COUNT(*) from WM_Barrica B inner Join WM_Pallet P on P.Idpallet = B.IdPallet Where P.RackLocId = ".$_POST['IdPallet'].")+
        (select COUNT(*) from  ADM_Ajustes aj left join ADM_logBAjuste ad on ad.IdAjuste=aj.IdAjuste
		    inner join WM_Pallet P on P.IdPallet=ad.IdPallet where P.RackLocId = ".$_POST['IdPallet']."
		    and aj.Estado=1 and ad.op=2 and (aj.Evento='Mover' or aj.Evento='Agregar'))";
        if((count($consecutivos)+(int)getDato($conn,$queryCons))>9){
          echo '..Error.. El lugar donde intentas mover el/los barril(es) no tiene espacio suficiente para esta operación, posiblemente haya solicitudes pendientes por procesar o ya se encuentra lleno';
        }else if(getDato($conn,$queryCons2)!=0){
          echo '..Error.. El lugar donde intentas mover el/los barril(es) no tiene espacio suficiente para esta operación, posiblemente haya solicitudes pendientes por procesar o ya se encuentra un tanque hoover en ésta posición';
        }else{
          $correctos="";
          $errores="";
          foreach ($consecutivos as $consecutivo) {
            //Verificar que el barril no tenga otras solicitudes pendientes
            $queryCons="SELECT COUNT(*) from ADM_Ajustes where IdBarrica=(select IdBarrica from WM_Barrica where Consecutivo='$consecutivo') AND Estado=1";
            if(getDato($conn,$queryCons)==0){
              $query="INSERT into ADM_Ajustes (Evento,IdBarrica,FechaSolicitud,Solicitante,Estado,Codigo,IdRazon)
                                      values ('$evento',(select IdBarrica from WM_Barrica where Consecutivo='$consecutivo'),(SELECT GETDATE()),
                                              (select IdUsuario from CM_Usuario where Clave = '$usuario'),1,'',$motivo); SELECT SCOPE_IDENTITY()";
              $result = sqlsrv_query( $conn , $query);
              sqlsrv_next_result($result);
              sqlsrv_fetch($result);
              $IdAjuste= (int)sqlsrv_get_field($result, 0);
              if($result){//Si se guardo en ADM_Ajustes
                //Preparar IdPallet
                $IdPallet=getDato($conn,"SELECT top 1 IdPallet from WM_Pallet where RackLocID=".$_POST['IdPallet']."  order by IdPallet desc");
                $queryADM_logBAjuste1="INSERT into ADM_logBAjuste (IdAjuste,Op,IdPallet,IdLoteBarica,IdCodificacion,Consecutivo,IdEstado,Capacidad,FechaRevisado,FechaRelleno,NoTapa)
                (select '$IdAjuste',1, W.IdPallet,W.IdLoteBarrica,E.IdCodEdad,'$consecutivo',W.IdEstado,W.Capacidad,W.FechaRevisado,W.FechaRelleno,W.NoTapa
                from WM_Barrica W left join CM_CodEdad E on W.IdCodificacion=E.IdCodEdad where Consecutivo='$consecutivo')";
                $resultADM_logBAjuste1 = sqlsrv_query( $conn , $queryADM_logBAjuste1);
                $queryADM_logBAjuste2="INSERT into ADM_logBAjuste (IdAjuste,Op,IdPallet,IdLoteBarica,IdCodificacion,Consecutivo,IdEstado,Capacidad,FechaRevisado,FechaRelleno,NoTapa)
                (select '$IdAjuste',2, '$IdPallet' ,W.IdLoteBarrica,E.IdCodEdad,'$consecutivo',W.IdEstado,W.Capacidad,W.FechaRevisado,W.FechaRelleno,W.NoTapa
                from WM_Barrica W left join CM_CodEdad E on W.IdCodificacion=E.IdCodEdad where Consecutivo='$consecutivo')";
                $resultADM_logBAjuste2 = sqlsrv_query( $conn , $queryADM_logBAjuste2);
                if($resultADM_logBAjuste1 && $resultADM_logBAjuste2){//Si se guardo los 2 registros en ADM_logBAjuste
                  $correctos=$correctos.$IdAjuste.", ";
                }else{//Si hubo un error se borra cualquier registro
                  $query="DELETE from ADM_logBAjuste where IdAjuste='$IdAjuste'";
                  sqlsrv_query( $conn , $query);
                  $query="DELETE from ADM_Ajustes where IdAjuste='$IdAjuste'";
                  sqlsrv_query( $conn , $query);
                  $errores=$errores.$consecutivo."(desconocido), ";
                }
              }else{
                $errores=$errores.$consecutivo."(desconocido), ";
              }
            }else{
              $errores=$errores.$consecutivo."(solicitud pendiente), ";
            }

          }
          if($errores===""){
            echo 'La tarea se realizo correctamente con ID de solicitud(es) '.substr($correctos, 0, -2);
          }else{
            echo 'La tarea tuvo algunos errores donde los consecutivos '.substr($errores, 0, -2)." tuvieron problemas al realizarse";
          }
        }
      }
  }else{
    echo '..Error.. No tienes permiso para solicitar cambios';
  }

}else{
  echo '..Error.. Acceso no autorizado';
}
function getDato($conn , $query){
  $resultCons = sqlsrv_query( $conn , $query);
  $row = sqlsrv_fetch_array( $resultCons, SQLSRV_FETCH_NUMERIC);
  return (int)$row[0];
}
function PonerFecha($campo,$valor){
  if(ISSET($_POST[$campo])){
    if($_POST[$campo]===''){
      return 'null';
    }else{
      return "'".$_POST[$campo]."'";
    }
  }else{
    return $valor;
  }
}
sqlsrv_close($conn); //Close the connnectiokn first

?>
