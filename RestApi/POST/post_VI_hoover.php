<?php
include '../general_connection.php';
if(strpos($permisos,',2,') !== false){
  $evento=$_POST["evento"];
  $NoSerie=$_POST["NoSerie"];
  $motivo=$_POST["motivo"];
  //Verificar que el tanque no tenga otras solicitudes pendientes
  $queryCons="SELECT COUNT(*) from ADM_AjustesTanques where IdTanque=(select IdTanque from WM_Tanques where NoSerie='$NoSerie') AND Estado=1";
  if(ObtenerCantidad($queryCons,$conn)==0){
    if(ISSET($_POST['restablecer'])){//Esta editando el tanque
      $query="INSERT into ADM_AjustesTanques (Evento,IdTanque,FechaSolicitud,Solicitante,Estado,IdRazon)
      values ('$evento',(SELECT IdTanque from WM_Tanques where NoSerie=$NoSerie),(SELECT GETDATE()),(select IdUsuario from CM_Usuario where Clave = '$usuario'),1,$motivo); SELECT SCOPE_IDENTITY()";
      $result = sqlsrv_query( $conn , $query);
      sqlsrv_next_result($result);
      sqlsrv_fetch($result);
      $IdAjuste= (int)sqlsrv_get_field($result, 0);
      if($result){//Si se guardo en ADM_Ajustes
        //Insertamos los valores actuales en ADM_logBAjuste con Op=1
        $queryADM_logBAjuste1="INSERT into ADM_logTAjuste(IdAjuste,Op,NoSerie,IdPallet,Litros,FechaLLenado,IdEstado) (SELECT $IdAjuste,1,$NoSerie,IdPallet,Litros,FechaLLenado,IdEstado from WM_Tanques where NoSerie='$NoSerie');";
        $resultADM_logBAjuste1 = sqlsrv_query( $conn , $queryADM_logBAjuste1);
        if($_POST['restablecer']=='pasado'){//Esta pasando de vacío a lleno, por lo tanto buscamos el último regirtro encontrado en adm_logbarril
          $queryADM_logBAjuste2="INSERT into ADM_logTAjuste(IdAjuste,Op,NoSerie,IdPallet,Litros,FechaLLenado,IdEstado) SELECT top 1 $IdAjuste,2,$NoSerie,D.IdPallet,D.Litros,D.FechaLLenado,D.IdEstado from WM_OperacionTQH O left join WM_OperacionTQHDetalle D on O.IdOperacion=D.IdOperacion where D.NoSerie='$NoSerie' and D.IdEstado=1 order by O.Fecha desc";
        }else if($_POST['restablecer']=='vacio'){//Esta pasando de lleno a vacío, por lo tanto generamos los datos de un barril vacío
          $queryADM_logBAjuste2="INSERT into ADM_logTAjuste(IdAjuste,Op,NoSerie,IdPallet,Litros,FechaLLenado,IdEstado) values($IdAjuste,2,$NoSerie,0,0,null,2)";
        }else{//Son otras modificaciones
          $queryCons="SELECT IdPallet,Litros,FechaLLenado,IdEstado from WM_Tanques where NoSerie='$NoSerie'";
          $resultCons = sqlsrv_query( $conn , $queryCons);
          $rowDatosP = sqlsrv_fetch_array( $resultCons, SQLSRV_FETCH_NUMERIC);
          $check=((int)$rowDatosP[3]!=2);//Verifica que el barril no este vacío, si lo esta, ignoramos los valores recibidos por el usuario
          $IdPallet=$rowDatosP[0];
          $Litros=(ISSET($_POST['Litros']) && $check)?$_POST["Litros"]:$rowDatosP[1];
          $IdEstado=(ISSET($_POST['IdEstado']) && $check)?$_POST["IdEstado"]:$rowDatosP[3];
          $rowDatosP[2]=($rowDatosP[2]==null)?'null':"'".$rowDatosP[2]->format('Y-m-d')."'";
          $FechaLLenado=PonerFecha('Llenado',$rowDatosP[2],$check);
          $queryADM_logBAjuste2="INSERT into ADM_logTAjuste(IdAjuste,Op,NoSerie,IdPallet,Litros,FechaLLenado,IdEstado) values($IdAjuste,2,$NoSerie,$IdPallet,$Litros,CONVERT(DATE,$FechaLLenado),$IdEstado)";
        }
        $resultADM_logBAjuste2 = sqlsrv_query( $conn , $queryADM_logBAjuste2);
        $revisar=ObtenerCantidad("SELECT count(*) from ADM_logTAjuste where IdAjuste='$IdAjuste'",$conn);//Revisar que se hayan ingresado los 2 registros
        if($resultADM_logBAjuste1 && $resultADM_logBAjuste2 && $revisar==2){//Si se guardo los 2 registros en ADM_logTAjuste
          echo 'La solicitud se ha realizado correctamente, ID de solicitud: '.$IdAjuste;
        }else{//Si hubo un error se borra cualquier registro
          $query="DELETE from ADM_logTAjuste where IdAjuste='$IdAjuste'";
          sqlsrv_query( $conn , $query);
          $query="DELETE from ADM_AjustesTanques where IdAjuste='$IdAjuste'";
          sqlsrv_query( $conn , $query);
          echo '..Error.. Hubó un problema al procesar la tarea, por favor intenta de nuevo más tarde Codigo: 1';
        }
      }else{
        echo '..Error.. Hubó un problema al procesar la tarea, por favor intenta de nuevo más tarde Codigo: 2';
      }
    }else{
      $IdPallet=ObtenerCantidad("SELECT top 1 IdPallet from WM_Pallet where RackLocID=".$_POST['IdPallet']." order by IdPallet desc",$conn);
      if($IdPallet==-1){
        terminarScript($conn,'..Error.. Esta ubicación no tiene un Pallet asignado');//No se encontro el pallet
      }
      //Conocer a cual bodega lo enviará
      $queryCons="SELECT top 1 Am.Nombre  from WM_Pallet P left join WM_RackLoc R on P.RackLocID=R.RackLocID
      left Join AA_Nivel N on R.NivelID=N.NivelID left Join AA_Posicion Po on N.PosicionId=Po.PosicionID
      left Join AA_Seccion Se on Po.SeccionID=Se.SeccionID left Join AA_Area Ar on Se.AreaId = Ar.AreaId
      left Join AA_Almacen Am on Ar.AlmacenId=Am.AlmacenID where P.IdPallet=".$IdPallet;
      $bodega=getDato2($conn,$queryCons);
      //Cuenta los barriles que existen en el pallet y en solicitudes pendientes para saber si hay lugar disponible
      $queryCons="SELECT (select COUNT(*) from WM_Barrica B inner Join WM_Pallet P on P.Idpallet = B.IdPallet Where P.RackLocId = ".$_POST['IdPallet'].")+
        (select COUNT(*) from  ADM_Ajustes aj left join ADM_logBAjuste ad on ad.IdAjuste=aj.IdAjuste
          inner join WM_Pallet P on P.IdPallet=ad.IdPallet where P.RackLocId = ".$_POST['IdPallet']."
            and aj.Estado=1 and ad.op=2 and (aj.Evento='Mover' or aj.Evento='Agregar'))";
      $queryCons2="SELECT (select COUNT(*) from WM_Tanques B inner Join WM_Pallet P on P.Idpallet = B.IdPallet Where P.RackLocId = ".$_POST['IdPallet'].")+
      (select COUNT(*) from  ADM_AjustesTanques aj left join ADM_logTAjuste ad on ad.IdAjuste=aj.IdAjuste
      inner join WM_Pallet P on P.IdPallet=ad.IdPallet where P.RackLocId = ".$_POST['IdPallet']."
      and aj.Estado=1 and ad.op=2 and (aj.Evento='Mover' or aj.Evento='Agregar'))";
      $estado=ObtenerCantidad("SELECT COUNT(*) from WM_Tanques where NoSerie='$NoSerie' AND IdEstado=2",$conn);
      if(ObtenerCantidad($queryCons,$conn)!=0 && strpos($bodega,'EMBARRILADO') === false){
        echo '..Error.. Ésta posición ya cuenta con barriles registrados o posiblemente haya solicitudes pendientes por procesar, intenta asignar una posición que se encuentre vacía';
      }else if(ObtenerCantidad($queryCons2,$conn)!=0 && strpos($bodega,'EMBARRILADO') === false){
        echo '..Error.. El lugar donde intentas mover el tanque no tiene espacio suficiente para esta operación, posiblemente haya solicitudes pendientes por procesar o ya se encuentra otro tanque en ésta posición';
      }else if(($estado!=0 && strpos($bodega,'EMBARRILADO') === false) || ($estado==0 && strpos($bodega,'EMBARRILADO') !== false)){
        terminarScript($conn,"..Error.. La ubicación a la que quieres mover éste tanque es erronea. Si el tanque está vacío solo podrá estar en EMBARRILADO o si está lleno podrá estár solo fuera de EMBARRILADO");//Terminamos el script
      }else{
        //Preparar datos
        $query="INSERT into ADM_AjustesTanques (Evento,IdTanque,FechaSolicitud,Solicitante,Estado,IdRazon)
        values ('$evento',(SELECT IdTanque from WM_Tanques where NoSerie=$NoSerie),(SELECT GETDATE()),(select IdUsuario from CM_Usuario where Clave = '$usuario'),1,$motivo); SELECT SCOPE_IDENTITY()";
        $result = sqlsrv_query( $conn , $query);
        sqlsrv_next_result($result);
        sqlsrv_fetch($result);
        $IdAjuste= (int)sqlsrv_get_field($result, 0);
        if($result){//Si se guardo en ADM_Ajustes
          $queryADM_logBAjuste1="INSERT into ADM_logTAjuste(IdAjuste,Op,NoSerie,IdPallet,Litros,FechaLLenado,IdEstado) (SELECT $IdAjuste,1,$NoSerie,IdPallet,Litros,FechaLLenado,IdEstado from WM_Tanques where NoSerie='$NoSerie');";
          $resultADM_logBAjuste1 = sqlsrv_query( $conn , $queryADM_logBAjuste1);
          $queryADM_logBAjuste2="INSERT into ADM_logTAjuste(IdAjuste,Op,NoSerie,IdPallet,Litros,FechaLLenado,IdEstado) (SELECT $IdAjuste,2,$NoSerie,$IdPallet,Litros,FechaLLenado,IdEstado from WM_Tanques where NoSerie='$NoSerie');";
          $resultADM_logBAjuste2 = sqlsrv_query( $conn , $queryADM_logBAjuste2);
          $revisar=ObtenerCantidad("SELECT count(*) from ADM_logTAjuste where IdAjuste='$IdAjuste'",$conn);//Revisar que se hayan ingresado los 2 registros
          if($resultADM_logBAjuste1 && $resultADM_logBAjuste2 && $revisar==2){//Si se guardo los 2 registros en ADM_logTAjuste
            echo 'La solicitud se ha realizado correctamente, ID de solicitud: '.$IdAjuste;
          }else{//Si hubo un error se borra cualquier registro
            $query="DELETE from ADM_logTAjuste where IdAjuste='$IdAjuste'";
            sqlsrv_query( $conn , $query);
            $query="DELETE from ADM_AjustesTanques where IdAjuste='$IdAjuste'";
            sqlsrv_query( $conn , $query);
            echo '..Error.. Hubo un problema al procesar la tarea, por favor intenta de nuevo más tarde Codigo: 1';
          }
        }else{
          echo '..Error.. Hubo un problema al procesar la tarea, por favor intenta de nuevo más tarde Codigo: 2';
        }
      }
    }
  }else{
    echo '..Error.. Este tanque ya tiene una solicitud pendiente de autorizar, espera a que la procesen';
  }
}else{
  echo '..Error.. No tienes permiso para solicitar cambios';
}
function getDato2($conn , $query){
  $resultCons = sqlsrv_query( $conn , $query);
  $row = sqlsrv_fetch_array( $resultCons, SQLSRV_FETCH_NUMERIC);
  return $row[0];
}
function PonerFecha($campo,$valor,$check){
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
