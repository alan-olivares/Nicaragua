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
    $consecutivo=$_POST["consecutivo"];
    $motivo=$_POST["motivo"];
    //Verificar que el barril no tenga otras solicitudes pendientes
    $queryCons="select COUNT(*) from ADM_Ajustes where IdBarrica=(select IdBarrica from WM_Barrica where Consecutivo='$consecutivo') AND Estado=1";
    $resultCons = sqlsrv_query( $conn , $queryCons);
    $row = sqlsrv_fetch_array( $resultCons, SQLSRV_FETCH_NUMERIC);
    if($row[0]==0){
      if(ISSET($_POST['restablecer'])){//Esta restableciendo el barril a una versión pasada o se esta poniendo en vacío
        $query="insert into ADM_Ajustes (Evento,IdBarrica,FechaSolicitud,Solicitante,Estado,Codigo,IdRazon)
                                values ('$evento',(select IdBarrica from WM_Barrica where Consecutivo='$consecutivo'),(SELECT GETDATE()),
                                        (select IdUsuario from CM_Usuario where Clave = '$usuario'),1,'',$motivo); SELECT SCOPE_IDENTITY()";
        $result = sqlsrv_query( $conn , $query);
        sqlsrv_next_result($result);
        sqlsrv_fetch($result);
        $IdAjuste= (int)sqlsrv_get_field($result, 0);
        if($result){//Si se guardo en ADM_Ajustes
          $queryCons="select W.IdPallet,W.IdLoteBarrica,E.IdCodificicacion,E.IdEdad,W.IdEstado,W.Capacidad,W.FechaRevisado,W.FechaRelleno,W.NoTapa
                      from WM_Barrica W left join CM_CodEdad E on W.IdCodificacion=E.IdCodEdad where Consecutivo='$consecutivo'";
          $resultCons = sqlsrv_query( $conn , $queryCons);
          $rowDatosP = sqlsrv_fetch_array( $resultCons, SQLSRV_FETCH_NUMERIC);
          $rowDatosP[6]=($rowDatosP[6]==null)?'null':"'".$rowDatosP[6]->format('Y-m-d')."'";
          $rowDatosP[7]=($rowDatosP[7]==null)?'null':"'".$rowDatosP[7]->format('Y-m-d')."'";
          if($_POST['restablecer']=='pasado'){
            $BRestablecer="select top 1 D.IdPallet,D.IdLoteBarrica,D.IdCodificacion,D.Fecha,D.Revisado,D.Relleno,D.Anio,D.NoTapa,D.Capacidad from adm_logbarril L join adm_logbarrildet D on L.logid=D.logid
             where D.Consecutivo='$consecutivo' and D.op=1 and idestado=1 order by D.logid desc";
            $resultBRestablecer = sqlsrv_query( $conn , $BRestablecer);
            $rowBRestablecer = sqlsrv_fetch_array( $resultBRestablecer, SQLSRV_FETCH_NUMERIC);
            $rowBRestablecer[4]=($rowBRestablecer[4]==null)?'null':"'".$rowBRestablecer[4]->format('Y-m-d')."'";
            $rowBRestablecer[5]=($rowBRestablecer[5]==null)?'null':"'".$rowBRestablecer[5]->format('Y-m-d')."'";
            $rowBRestablecer[7]=($rowBRestablecer[7]==null)?'null':"'".$rowBRestablecer[7]."'";

            $BEdad="select IdEdad from CM_CodEdad where IdCodEdad='$rowBRestablecer[2]'";
            $resultBEdad = sqlsrv_query( $conn , $BEdad);
            $rowBEdad = sqlsrv_fetch_array( $resultBEdad, SQLSRV_FETCH_NUMERIC);

            $BUso="select IdCodificicacion from CM_CodEdad where IdCodEdad='$rowBRestablecer[2]'";
            $resultBUso = sqlsrv_query( $conn , $BUso);
            $rowBUso = sqlsrv_fetch_array( $resultBUso, SQLSRV_FETCH_NUMERIC);

            $queryADM_logBAjuste1="exec sp_ADM_logBAjuste '$IdAjuste', 1,'$rowDatosP[0]','$rowDatosP[1]','$rowDatosP[2]','$rowDatosP[3]','$consecutivo','$rowDatosP[4]','$rowDatosP[5]',$rowDatosP[6],$rowDatosP[7],'$rowDatosP[8]'";
            $resultADM_logBAjuste1 = sqlsrv_query( $conn , $queryADM_logBAjuste1);
            $queryADM_logBAjuste2="exec sp_ADM_logBAjuste '$IdAjuste', 2,'$rowBRestablecer[0]','$rowBRestablecer[1]','$rowBEdad[0]',$rowBUso[0],'$consecutivo','1','$rowBRestablecer[8]',$rowBRestablecer[4],$rowBRestablecer[5],$rowBRestablecer[7]";
            $resultADM_logBAjuste2 = sqlsrv_query( $conn , $queryADM_logBAjuste2);
          }else{
            $queryADM_logBAjuste1="exec sp_ADM_logBAjuste '$IdAjuste', 1,'$rowDatosP[0]','$rowDatosP[1]','$rowDatosP[2]','$rowDatosP[3]','$consecutivo','$rowDatosP[4]','$rowDatosP[5]',$rowDatosP[6],$rowDatosP[7],'$rowDatosP[8]'";
            $resultADM_logBAjuste1 = sqlsrv_query( $conn , $queryADM_logBAjuste1);
            $queryADM_logBAjuste2="exec sp_ADM_logBAjuste '$IdAjuste', 2,'15894',0,'$rowDatosP[2]',$rowDatosP[3],'$consecutivo','2','0',null,null,null";
            $resultADM_logBAjuste2 = sqlsrv_query( $conn , $queryADM_logBAjuste2);
          }
          if($resultADM_logBAjuste1 && $resultADM_logBAjuste2){//Si se guardo los 2 registros en ADM_logBAjuste
            echo 'La solicitud se ha realizado correctamente, ID de solicitud: '.$IdAjuste;
          }else{//Si hubo un error se borra cualquier registro
            $query="delete from ADM_logBAjuste where IdAjuste='$IdAjuste'";
            sqlsrv_query( $conn , $query);
            $query="delete from ADM_Ajustes where IdAjuste='$IdAjuste'";
            sqlsrv_query( $conn , $query);
            echo '..Error.. Hubó un problema al procesar la tarea, por favor intenta de nuevo más tarde Codigo: 1';
          }
        }else{
          echo '..Error.. Hubó un problema al procesar la tarea, por favor intenta de nuevo más tarde Codigo: 2';
        }
      }else{
        $CBarriles=(ISSET($_POST['CBarriles']))?$_POST["CBarriles"]:0;
        //Preparar datos
        $queryCons="select W.IdPallet,W.IdLoteBarrica,E.IdCodificicacion,E.IdEdad,W.IdEstado,W.Capacidad,W.FechaRevisado,W.FechaRelleno,W.NoTapa
                    from WM_Barrica W left join CM_CodEdad E on W.IdCodificacion=E.IdCodEdad where Consecutivo='$consecutivo'";
        $resultCons = sqlsrv_query( $conn , $queryCons);
        $rowDatosP = sqlsrv_fetch_array( $resultCons, SQLSRV_FETCH_NUMERIC);
        $IdPallet=(ISSET($_POST['IdPallet']))?$_POST["IdPallet"]:$rowDatosP[0];
        $IdLoteBarica=(ISSET($_POST['IdLoteBarica']))?$_POST["IdLoteBarica"]:$rowDatosP[1];
        $uso=(ISSET($_POST['uso']))?$_POST["uso"]:$rowDatosP[2];
        $edad=(ISSET($_POST['edad']))?$_POST["edad"]:$rowDatosP[3];
        $IdEstado=(ISSET($_POST['IdEstado']))?$_POST["IdEstado"]:$rowDatosP[4];
        $rowDatosP[6]=($rowDatosP[6]==null)?'null':"'".$rowDatosP[6]->format('Y-m-d')."'";
        $rowDatosP[7]=($rowDatosP[7]==null)?'null':"'".$rowDatosP[7]->format('Y-m-d')."'";
        $Capacidad=(ISSET($_POST['Capacidad']))?$_POST["Capacidad"]:$rowDatosP[5];
        $Revisado=(ISSET($_POST['Revisado']))?"'".$_POST["Revisado"]."'":$rowDatosP[6];
        $Relleno=(ISSET($_POST['Relleno']))?"'".$_POST["Relleno"]."'":$rowDatosP[7];
        $NoTapa=(ISSET($_POST['NoTapa']))?$_POST["NoTapa"]:$rowDatosP[8];
        //Cuenta los barriles que existen en el pallet y en solicitudes pendientes para saber si hay lugar disponible
        $queryCons="select (select COUNT(*) from WM_Barrica B inner Join WM_Pallet P on P.Idpallet = B.IdPallet Where P.RackLocId = '$IdPallet')+
          (select COUNT(*) from  ADM_Ajustes aj left join ADM_logBAjuste ad on ad.IdAjuste=aj.IdAjuste
		        inner join WM_Pallet P on P.IdPallet=ad.IdPallet where P.RackLocId = '$IdPallet'
		          and aj.Estado=1 and ad.op=2 and (aj.Evento='Mover' or aj.Evento='Agregar'))";
        $resultCons = sqlsrv_query( $conn , $queryCons);
        $row = sqlsrv_fetch_array( $resultCons, SQLSRV_FETCH_NUMERIC);
        if(((int)$CBarriles+(int)$row[0])<=9){
          $query="insert into ADM_Ajustes (Evento,IdBarrica,FechaSolicitud,Solicitante,Estado,Codigo,IdRazon)
                                  values ('$evento',(select IdBarrica from WM_Barrica where Consecutivo='$consecutivo'),(SELECT GETDATE()),
                                          (select IdUsuario from CM_Usuario where Clave = '$usuario'),1,'',$motivo); SELECT SCOPE_IDENTITY()";
          $result = sqlsrv_query( $conn , $query);
          sqlsrv_next_result($result);
          sqlsrv_fetch($result);
          $IdAjuste= (int)sqlsrv_get_field($result, 0);
          if($result){//Si se guardo en ADM_Ajustes
            $Pallet="select top 1 IdPallet from WM_Pallet where RackLocID='$IdPallet' order by IdPallet desc";
            $resultPallet = sqlsrv_query( $conn , $Pallet);
            $rowPallet = sqlsrv_fetch_array( $resultPallet, SQLSRV_FETCH_NUMERIC);
            $queryADM_logBAjuste1="exec sp_ADM_logBAjuste '$IdAjuste', 1,'$rowDatosP[0]','$rowDatosP[1]','$rowDatosP[2]','$rowDatosP[3]','$consecutivo','$rowDatosP[4]','$rowDatosP[5]',$rowDatosP[6],$rowDatosP[7],'$rowDatosP[8]'";
            $resultADM_logBAjuste1 = sqlsrv_query( $conn , $queryADM_logBAjuste1);
            $queryADM_logBAjuste2="exec sp_ADM_logBAjuste '$IdAjuste', 2,'$rowPallet[0]','$IdLoteBarica','$uso','$edad','$consecutivo','$IdEstado','$Capacidad',$Revisado,$Relleno,'$NoTapa'";
            $resultADM_logBAjuste2 = sqlsrv_query( $conn , $queryADM_logBAjuste2);
            if($resultADM_logBAjuste1 && $resultADM_logBAjuste2){//Si se guardo los 2 registros en ADM_logBAjuste
              echo 'La solicitud se ha realizado correctamente, ID de solicitud: '.$IdAjuste;
            }else{//Si hubo un error se borra cualquier registro
              $query="delete from ADM_logBAjuste where IdAjuste='$IdAjuste'";
              sqlsrv_query( $conn , $query);
              $query="delete from ADM_Ajustes where IdAjuste='$IdAjuste'";
              sqlsrv_query( $conn , $query);
              echo '..Error.. Hubó un problema al procesar la tarea, por favor intenta de nuevo más tarde Codigo: 1';
            }
          }else{
            echo '..Error.. Hubó un problema al procesar la tarea, por favor intenta de nuevo más tarde Codigo: 2';
          }
        }else{
          echo '..Error.. El lugar donde intentas mover el/los barril(es) no tiene espacio suficiente para esta operación, posiblemente haya solicitudes pendientes por procesar o ya se encuentra lleno';
        }
      }
    }else{
      echo '..Error.. Este barril ya tiene una solicitud pendiente de autorizar, espera a que la procesen';
    }
  }else{
    echo '..Error.. No tienes permiso para solicitar cambios';
  }

}else{
  echo '..Error.. Acceso no autorizado';
}
sqlsrv_close($conn); //Close the connnectiokn first

?>
