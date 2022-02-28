<?php
include '../general_connection.php';
if(strpos($permisos,',8,') !== false){
  if(ISSET($_POST['operacion'])){//Crear orden
    $codigo=$_POST['codigo'];
    $desc=$_POST['desc'];
    $capa=$_POST['capa'];
    $tipo=$_POST['tipo'];
    $bomba=$_POST['bomba'];
    $tube=$_POST['tube'];
    $conex=$_POST['conex'];
    $fallas=$_POST['fallas'];
    $operacion=$_POST['operacion'];
    $tsql="";
    if($operacion==='editados'){
      $tsql = "UPDATE CM_Tanque set Descripcion='$desc', Capacidad='$capa', Tipo='$tipo' where Codigo='$codigo'";
      $tsql2="if exists(select IDTanque from CM_TanqDetalle where IDTanque=(select IDTanque from CM_Tanque where Codigo='$codigo'))
      UPDATE CM_TanqDetalle set A= '$bomba', B='$tube', C='$conex', D='$fallas' where IdTanque=(select IDTanque from CM_Tanque where Codigo='$codigo')
      else INSERT into CM_TanqDetalle (IdTanque,A,B,C,D) values((select IDTanque from CM_Tanque where Codigo='$codigo'),'$bomba','$tube','$conex','$fallas')";
      ejecutar($tsql,$tsql2,$conn,$operacion);
    }else if($operacion==='agregados'){
      if(ObtenerCantidadBol("SELECT * from CM_Tanque where Codigo='$codigo' ",$conn)){
        $tsql = "INSERT into CM_Tanque (Codigo,Descripcion,Capacidad,Tipo) values('$codigo','$desc','$capa','$tipo')";
        $tsql2="INSERT into CM_TanqDetalle (IdTanque,A,B,C,D) values((select IDTanque from CM_Tanque where Codigo='$codigo'),'$bomba','$tube','$conex','$fallas')";
        ejecutar($tsql,$tsql2,$conn,$operacion);
      }else{
        echo "..Error.. Éste código ya se encuentra registrado";
      }

    }else if($operacion==='eliminados'){
      $tsql = "DELETE from CM_Tanque where Codigo='$codigo'";
      $tsql2="DELETE from CM_TanqDetalle where IdTanque=(select IDTanque from CM_Tanque where Codigo='$codigo')";
      ejecutar($tsql,$tsql2,$conn,$operacion);
    }
  }else if(ISSET($_POST['operacionA'])){
    $codigo=$_POST['codigo'];
    $desc=$_POST['desc'];
    $grado=$_POST['grado'];
    $obs=$_POST['obs'];
    $operacion=$_POST['operacionA'];
    if($operacion==='editados'){
      $codigoA=$_POST['codigoA'];
      $codigoAN=$_POST['codigoAN'];
      if($codigoA!==$codigoAN && ObtenerCantidadBol("SELECT * from CM_Alcohol where Codigo='$codigoA' ",$conn)){
        $tsql = "UPDATE CM_Alcohol set Codigo=$codigoA, Descripcion='$desc', Grado='$grado', Observaciones='$obs' where IdAlcohol='$codigo'";
        ejecutar2($tsql,$conn,$operacion);
      }else if($codigoA===$codigoAN){
        $tsql = "UPDATE CM_Alcohol set Descripcion='$desc', Grado='$grado', Observaciones='$obs' where IdAlcohol='$codigo'";
        ejecutar2($tsql,$conn,$operacion);
      }else{
        echo "..Error.. Éste código ya se encuentra registrado";
      }
    }else if($operacion==='agregados'){
      if(ObtenerCantidadBol("SELECT * from CM_Alcohol where Codigo='$codigo' ",$conn)){
        $tsql = "INSERT into CM_Alcohol (Codigo,Descripcion,Grado,Observaciones) values('$codigo','$desc','$grado','$obs')";
        ejecutar2($tsql,$conn,$operacion);
      }else{
        echo "..Error.. Éste código ya se encuentra registrado";
      }

    }else if($operacion==='eliminados'){
      $tsql = "DELETE from CM_Alcohol where IdAlcohol='$codigo'";
      ejecutar2($tsql,$conn,$operacion);
    }
  }else if(ISSET($_POST['finalizaTanque'])){
    $tanque=$_POST['finalizaTanque'];
    $tsql = "if exists(SELECT * from WM_RecDetail where IdRecDetail='$tanque' and Estatus='0')
    UPDATE WM_RecDetail set Estatus='1',Merma=(select Litros-Consumo from WM_RecDetail where IdRecDetail='$tanque') where IdRecDetail='$tanque'";
    ejecutar2($tsql,$conn,"actualizados");
    generarNotificacion(ObtenerDatoSimple("SELECT IdLote from WM_RecDetail where IdRecDetail='$tanque' and Estatus='0'",$conn),1,3,$usuario,'-1',$conn);
  }else if(ISSET($_POST['nuevoEnvio'])){
    $tanque=$_POST['tanque'];$litros=$_POST['litros'];$medida=$_POST['medida'];$IdAlcohol=$_POST['IdAlcohol'];
    $anno=$_POST['anno'];$para=$_POST['para'];$fecha=$_POST['fecha'];$envio=$_POST['envio'];
    $tsql = "SELECT * from WM_RecDetail where IdTanque=(select IDTanque from CM_Tanque where Codigo='$tanque') and Estatus=0 and IdAlcohol<>'$IdAlcohol'";
    if(ObtenerCantidadBol($tsql,$conn)){
      $tsql = "exec sp_LoteCrea_v2 '$fecha','$IdAlcohol'";
      $idLote=ObtenerDatoSimple($tsql,$conn);
      $tsql = "if not exists (select * from CM_Item where Año='$anno')
      insert into CM_Item (Codigo,Año,Estatus) OUTPUT Inserted.IdItem values ((select top 1 Codigo from CM_Item where Codigo <>8130010050 order by Codigo desc)+1,'$anno','0')
      else select IdItem from CM_Item where Año='$anno';";
      $idItem=ObtenerDatoSimple($tsql,$conn);
      $tsql = "INSERT into WM_Recepcion (EnvioNo,Remitente,Para,Destino,Fecha,AñoAlcohol,Hora,Estatus) OUTPUT Inserted.IdRecepcion
      values('$envio','Almacén Producto Terminado','$para','Tanque $tanque',GETDATE(),'$anno', SUBSTRING(CONVERT(VARCHAR,getdate(),	22) , 10, 20),'0');";
      $idRecepcion=ObtenerDatoSimple($tsql,$conn);
      $tsql = "INSERT into WM_RecDetail (IdRecepcion,IdTanque,Litros,Consumo,Merma,MermaReal,Medida,IdItem,IdAlcohol,IdLote,Estatus) OUTPUT Inserted.IdRecDetail
      values('$idRecepcion',(select IDTanque from CM_Tanque where Codigo='$tanque'),'$litros','0','0','0','$medida','$idItem' ,'$IdAlcohol','$idLote',0);";
      $IdRecDetail=ObtenerDatoSimple($tsql,$conn);
      if($idRecepcion!=null && $IdRecDetail!=null){
        echo "Envío guardado correctamente con IdRecepcion=".$idRecepcion." y IdDetalle=".$IdRecDetail;
        generarNotificacion($idLote,1,1,$usuario,'-1',$conn);
      }else{
        echo "..Error.. Se produjo un error al guardar el envio, por favor intenta de nuevo más tarde";
      }
    }else{
      echo "..Error.. Este tanque ya se encuentra lleno de otro alcohol";
    }
  }

}else{
  echo '..Error.. No tienes permisos para procesar cambios';
}
sqlsrv_close($conn); //Close the connnectiokn first

function ObtenerCantidadBol($queryCons,$conn){
  $resultCons = sqlsrv_query( $conn , $queryCons);
  $row = sqlsrv_fetch_array( $resultCons, SQLSRV_FETCH_NUMERIC);
  return empty($row);
}

function ObtenerDatoSimple($queryCons,$conn){
  $resultBRestablecer = sqlsrv_query( $conn , $queryCons);
  $row = sqlsrv_fetch_array( $resultBRestablecer, SQLSRV_FETCH_NUMERIC);
  if(empty($row))
    return null;
  else
    return $row[0];

}
function ejecutar2($tsql,$conn,$operacion){
  $stmt = sqlsrv_query( $conn , $tsql);
  if($stmt){
    echo 'Datos '.$operacion." con exito";
  }else{
    echo '..Error.. Hubo un problema al '.$operacion.' los datos';
  }
}
function ejecutar($tsql,$tsql2,$conn,$operacion){
  $stmt = sqlsrv_query( $conn , $tsql);
  $stmt2 = sqlsrv_query( $conn , $tsql2);

  if($stmt && $stmt2){
    echo 'Datos '.$operacion." con exito";
  }else{
    echo '..Error.. Hubo un problema al '.$operacion.' los datos';
  }
}
?>
