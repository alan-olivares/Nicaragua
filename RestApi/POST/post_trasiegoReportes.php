<?php
include '../general_connection.php';
if(strpos($permisos,',6,') !== false){//Agregar ordenes
  if(ISSET($_POST["detalles"])){
    if(isJson($_POST["detalles"])){
      $data = json_decode($_POST["detalles"], true);
      $fecha=$_POST['fecha'];
      $tanque=$_POST['tanque'];
      $tq=$_POST['tq'];
      $fcv=$_POST['fcv'];
      $consEnvioNo = "if exists(select * from WM_EnviosBlending where idTanque='$tanque' and CONVERT(varchar(10), Fecha,120)='$fecha')
      select EnvioNo from WM_EnviosBlending where idTanque='$tanque' and CONVERT(varchar(10), Fecha,120)='$fecha'
      else select ISNULL((select top 1 EnvioNo from WM_EnviosBlending order by EnvioNo desc),53)+1";
      $EnvioNo=ObtenerCantidad($consEnvioNo,$conn);
      $consInsert = "if exists(select * from WM_EnviosBlending where EnvioNo='$EnvioNo')
      update WM_EnviosBlending set tq='$tq',fcv='$fcv' where EnvioNo='$EnvioNo'
      else insert into WM_EnviosBlending (EnvioNo,idTanque,fecha,tq,fcv) values ('$EnvioNo','$tanque',convert(date,'$fecha'),'$tq','$fcv')";
      if(ejecutarDato($conn,$consInsert)){
        $idEnvio=ObtenerCantidad("SELECT top 1 IdEnvio from WM_EnviosBlending where EnvioNo='$EnvioNo'",$conn);
        ejecutarDato($conn,"DELETE from WM_EnviosBlendingDetalle where IdEnvio=$idEnvio");
        foreach($data as $campo) {
          $item=$campo['IdItem'];
          $litros=$campo['Litros'];
          ejecutarDato($conn,"INSERT into WM_EnviosBlendingDetalle (IdEnvio,idItem,Litros) values ('$idEnvio','$item','$litros')");
        }
        echo 'Datos almacenados correctamente';
      }else{
        echo '..Error.. Ocurrió un problema al registrar el envío';
      }

    }else{
      echo '..Error.. Se generó un problema al decodificar el Json';
    }
  }

}else{
  echo '..Error.. No tienes permisos para procesar cambios';
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
