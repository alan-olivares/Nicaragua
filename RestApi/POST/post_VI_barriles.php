<?php
include '../general_connection.php';
if(strpos($permisos,',2,') !== false){
  $evento=$_POST["evento"];
  $motivo=$_POST["motivo"];
  if(ISSET($_POST['restablecer'])){//Está editando el barril
    $consecutivo=$_POST["consecutivo"];
    //Verificar que el barril no tenga otras solicitudes pendientes
    $queryCons="SELECT COUNT(*) from ADM_Ajustes where IdBarrica=(select IdBarrica from WM_Barrica where Consecutivo='$consecutivo') AND Estado=1";
    if(ObtenerCantidad($queryCons,$conn)==0){
      $query="INSERT into ADM_Ajustes (Evento,IdBarrica,FechaSolicitud,Solicitante,Estado,IdRazon)
      values ('$evento',(select IdBarrica from WM_Barrica where Consecutivo='$consecutivo'),(SELECT GETDATE()),
      (select IdUsuario from CM_Usuario_WEB where Clave = '$usuario'),1,$motivo); SELECT SCOPE_IDENTITY();";
      $result = sqlsrv_query( $conn , $query);
      sqlsrv_next_result($result);
      sqlsrv_fetch($result);
      $IdAjuste= (int)sqlsrv_get_field($result, 0);
      if($result){//Si se guardo en ADM_Ajustes
        //Insertamos los valores actuales en ADM_logBAjuste
        $queryADM_logBAjuste1="INSERT into ADM_logBAjuste (IdAjuste,Op,IdPallet,IdLoteBarica,IdCodificacion,Consecutivo,IdEstado,Capacidad,FechaRevisado,FechaRelleno,NoTapa)
        (select '$IdAjuste',1, W.IdPallet,W.IdLoteBarrica,E.IdCodEdad,'$consecutivo',W.IdEstado,W.Capacidad,W.FechaRevisado,W.FechaRelleno,W.NoTapa
        from WM_Barrica W left join CM_CodEdad E on W.IdCodificacion=E.IdCodEdad where Consecutivo='$consecutivo')";
        $resultADM_logBAjuste1 = sqlsrv_query( $conn , $queryADM_logBAjuste1);
        if($_POST['restablecer']=='pasado'){//Esta pasando de vacío a lleno, por lo tanto buscamos el último registro encontrado en adm_logbarril
          $queryADM_logBAjuste2="INSERT into ADM_logBAjuste (IdAjuste,Op,IdPallet,IdLoteBarica,IdCodificacion,Consecutivo,IdEstado,Capacidad,FechaRevisado,FechaRelleno,NoTapa)
          SELECT top 1 '$IdAjuste',2,IdPallet,IdLoteBarrica,IdCodificacion,'$consecutivo',1,Capacidad,Revisado,Relleno,NoTapa from adm_logbarrildet
           where Consecutivo='$consecutivo' and op=1 and idestado=1 order by logid desc";
        }else if($_POST['restablecer']=='vacio'){//Esta pasando de lleno a vacio, por lo tanto generemos los datos de un barril vacío
          $queryADM_logBAjuste2="INSERT into ADM_logBAjuste (IdAjuste,Op,IdPallet,IdLoteBarica,IdCodificacion,Consecutivo,IdEstado,Capacidad,FechaRevisado,FechaRelleno,NoTapa)
          (select '$IdAjuste',2, '15894',0,E.IdCodEdad,'$consecutivo',2,0,null,null,null
          from WM_Barrica W left join CM_CodEdad E on W.IdCodificacion=E.IdCodEdad where Consecutivo='$consecutivo')";
        }else{//Son otras modificaciones
          $queryCons="SELECT W.IdPallet,W.IdLoteBarrica,E.IdCodificicacion,E.IdEdad,W.IdEstado,W.Capacidad,W.FechaRevisado,W.FechaRelleno,W.NoTapa
                  from WM_Barrica W left join CM_CodEdad E on W.IdCodificacion=E.IdCodEdad where Consecutivo='$consecutivo'";
          $resultCons = sqlsrv_query( $conn , $queryCons);
          $rowDatosP = sqlsrv_fetch_array( $resultCons, SQLSRV_FETCH_NUMERIC);
          $check=((int)$rowDatosP[4]!=2);//Verifica que el barril no este vacío, si lo esta, ignoramos los valores recibidos por el usuario
          $IdLoteBarica=(ISSET($_POST['IdLoteBarica']) && $check)?$_POST["IdLoteBarica"]:$rowDatosP[1];
          $uso=(ISSET($_POST['uso']))?$_POST["uso"]:$rowDatosP[2];
          $edad=(ISSET($_POST['edad']))?$_POST["edad"]:$rowDatosP[3];
          $IdEstado=(ISSET($_POST['IdEstado']) && $check)?$_POST["IdEstado"]:$rowDatosP[4];
          $rowDatosP[6]=($rowDatosP[6]==null)?'null':"'".$rowDatosP[6]->format('Y-m-d')."'";
          $rowDatosP[7]=($rowDatosP[7]==null)?'null':"'".$rowDatosP[7]->format('Y-m-d')."'";
          $Capacidad=(ISSET($_POST['Capacidad']) && $check)?$_POST["Capacidad"]:$rowDatosP[5];
          $Revisado=PonerFecha('Revisado',$rowDatosP[6],$check);
          $Relleno=PonerFecha('Relleno',$rowDatosP[7],$check);
          $NoTapa=(ISSET($_POST['NoTapa']) && $check)?$_POST["NoTapa"]:$rowDatosP[8];
          $queryADM_logBAjuste2="INSERT into ADM_logBAjuste (IdAjuste,Op,IdPallet,IdLoteBarica,IdCodificacion,Consecutivo,IdEstado,Capacidad,FechaRevisado,FechaRelleno,NoTapa) values
           ('$IdAjuste', 2,'$rowDatosP[0]','$IdLoteBarica',(Select IdCodEdad from CM_CodEdad where IdCodificicacion='$uso' and IdEdad='$edad'),'$consecutivo','$IdEstado','$Capacidad',CONVERT(DATE,$Revisado),CONVERT(DATE,$Relleno),'$NoTapa')";
        }
        $resultADM_logBAjuste2 = sqlsrv_query( $conn , $queryADM_logBAjuste2);
        $revisar=ObtenerCantidad("SELECT count(*) from ADM_logBAjuste where IdAjuste='$IdAjuste'",$conn);//Revisar que se hayan ingresado los 2 registros
        if($resultADM_logBAjuste1 && $resultADM_logBAjuste2 && $revisar==2){//Si se guardo los 2 registros en ADM_logBAjuste
          echo 'La solicitud se ha realizado correctamente, ID de solicitud: '.$IdAjuste;
        }else{//Si hubo un error se borra cualquier registro
          $query="DELETE from ADM_logBAjuste where IdAjuste='$IdAjuste'";
          sqlsrv_query( $conn , $query);
          $query="DELETE from ADM_Ajustes where IdAjuste='$IdAjuste'";
          sqlsrv_query( $conn , $query);
          echo '..Error.. Hubo un problema al procesar la tarea, por favor intenta de nuevo más tarde Codigo: 1';
        }
      }else{
        echo '..Error.. Hubo un problema al procesar la tarea, por favor intenta de nuevo más tarde Codigo: 2';
      }
    }else{
      echo '..Error.. Este barril ya cuenta con solicitudes pendientes de procesar, espera a que sean procesadas';
    }
  }else{//Agregando o moviendo barriles
      //Preparar IdPallet
      $IdPallet=ObtenerCantidad("SELECT top 1 IdPallet from WM_Pallet where RackLocID=".$_POST['IdPallet']."  order by IdPallet desc",$conn);
      if($IdPallet==-1){
        terminarScript($conn,'..Error.. Esta ubicación no tiene un Pallet asignado');//No se encontro el pallet
      }
      //Conocer a cual bodega lo enviará
      $queryCons="SELECT top 1 Am.Nombre  from WM_Pallet P left join WM_RackLoc R on P.RackLocID=R.RackLocID
      left Join AA_Nivel N on R.NivelID=N.NivelID left Join AA_Posicion Po on N.PosicionId=Po.PosicionID
      left Join AA_Seccion Se on Po.SeccionID=Se.SeccionID left Join AA_Area Ar on Se.AreaId = Ar.AreaId
      left Join AA_Almacen Am on Ar.AlmacenId=Am.AlmacenID where P.IdPallet=".$IdPallet;
      $bodega=getDato2($conn,$queryCons);
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
      if((count($consecutivos)+(int)ObtenerCantidad($queryCons,$conn))>9 && strpos($bodega,'EMBARRILADO') === false){
        echo '..Error.. El lugar donde intentas mover el/los barril(es) no tiene espacio suficiente para esta operación, posiblemente haya solicitudes pendientes por procesar o ya se encuentra lleno';
      }else if(ObtenerCantidad($queryCons2,$conn)!=0 && strpos($bodega,'EMBARRILADO') === false){
        echo '..Error.. El lugar donde intentas mover el/los barril(es) no tiene espacio suficiente para esta operación, posiblemente haya solicitudes pendientes por procesar o ya se encuentra un tanque hoover en esta posición';
      }else{
        $correctos="";
        $errores="";
        foreach ($consecutivos as $consecutivo) {
          //Verificar que el barril no tenga otras solicitudes pendientes
          $queryCons="SELECT COUNT(*) from ADM_Ajustes where IdBarrica=(select IdBarrica from WM_Barrica where Consecutivo='$consecutivo') AND Estado=1";
          $estado=ObtenerCantidad("SELECT COUNT(*) from WM_Barrica where Consecutivo='$consecutivo' AND IdEstado=2",$conn);
          $IdPalletActual=ObtenerCantidad("SELECT top 1 IdPallet from WM_Barrica where Consecutivo='$consecutivo'",$conn);
          if(ObtenerCantidad($queryCons,$conn)!=0){//Si tiene solicitud pendiente
            $errores=$errores.$consecutivo." (solicitud pendiente), ";
            //Si está haciendo un cambio en un barril lleno hacia embarrilado o si esta haciendolo de un barril vacio hacia una posicion no de embarrilado
          }else if(($estado!=0 && strpos($bodega,'EMBARRILADO') === false) || ($estado==0 && strpos($bodega,'EMBARRILADO') !== false)){
            $errores=$errores.$consecutivo." (ubicación invalida para barril ".($estado==0?"lleno":"vacío")."), ";
            //terminarScript($conn,"..Error.. La ubicación a la que quieres mover este barril es erronea. Si el barril está vacío solo podrá estar en EMBARRILADO o si está lleno podrá estár solo fuera de EMBARRILADO");//Terminamos el script
          }else if($IdPallet==$IdPalletActual){
            $errores=$errores.$consecutivo." (ya pertenece a este pallet), ";
          }else{
            $query="INSERT into ADM_Ajustes (Evento,IdBarrica,FechaSolicitud,Solicitante,Estado,IdRazon)
                                    values ('$evento',(select IdBarrica from WM_Barrica where Consecutivo='$consecutivo'),(SELECT GETDATE()),
                                            (select IdUsuario from CM_Usuario_WEB where Clave = '$usuario'),1,$motivo); SELECT SCOPE_IDENTITY()";
            $result = sqlsrv_query( $conn , $query);
            sqlsrv_next_result($result);
            sqlsrv_fetch($result);
            $IdAjuste= (int)sqlsrv_get_field($result, 0);
            if($result){//Si se guardo en ADM_Ajustes
              $queryADM_logBAjuste1="INSERT into ADM_logBAjuste (IdAjuste,Op,IdPallet,IdLoteBarica,IdCodificacion,Consecutivo,IdEstado,Capacidad,FechaRevisado,FechaRelleno,NoTapa)
              (select '$IdAjuste',1, W.IdPallet,W.IdLoteBarrica,E.IdCodEdad,'$consecutivo',W.IdEstado,W.Capacidad,W.FechaRevisado,W.FechaRelleno,W.NoTapa
              from WM_Barrica W left join CM_CodEdad E on W.IdCodificacion=E.IdCodEdad where Consecutivo='$consecutivo')";
              $resultADM_logBAjuste1 = sqlsrv_query( $conn , $queryADM_logBAjuste1);
              $queryADM_logBAjuste2="INSERT into ADM_logBAjuste (IdAjuste,Op,IdPallet,IdLoteBarica,IdCodificacion,Consecutivo,IdEstado,Capacidad,FechaRevisado,FechaRelleno,NoTapa)
              (select '$IdAjuste',2, '$IdPallet' ,W.IdLoteBarrica,E.IdCodEdad,'$consecutivo',W.IdEstado,W.Capacidad,W.FechaRevisado,W.FechaRelleno,W.NoTapa
              from WM_Barrica W left join CM_CodEdad E on W.IdCodificacion=E.IdCodEdad where Consecutivo='$consecutivo')";
              $resultADM_logBAjuste2 = sqlsrv_query( $conn , $queryADM_logBAjuste2);
              $revisar=ObtenerCantidad("SELECT count(*) from ADM_logBAjuste where IdAjuste='$IdAjuste'",$conn);//Revisar que se hayan ingresado los 2 registros
              if($resultADM_logBAjuste1 && $resultADM_logBAjuste2 && $revisar==2){//Si se guardo los 2 registros en ADM_logBAjuste
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
        if($errores===""){
          echo 'La tarea se realizo correctamente con ID de solicitud(es) '.substr($correctos, 0, -2);
        }else{
          echo '..Error.. La tarea tuvo algunos errores donde los consecutivos '.substr($errores, 0, -2)." tuvieron problemas al realizarse";
        }
      }
    }
}else{
  echo '..Error.. No tienes permiso para solicitar cambios';
}
function getDato2($conn , $query){
  $resultCons = sqlsrv_query( $conn , $query);
  $row = sqlsrv_fetch_array( $resultCons, SQLSRV_FETCH_NUMERIC);
  return $row[0];
}
function PonerFecha($campo,$valor, $check){
  if(ISSET($_POST[$campo]) && $check){
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
