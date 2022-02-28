<?php
include '../general_connection.php';
if(strpos($permisos,',6,') !== false){
  if(ISSET($_GET['FSI82493']) && ISSET($_GET['tanque']) && ISSET($_GET['fecha'])){// Reporte Hoja de Análisis de Trasiego
    $tanque=$_GET['tanque'];
    $fecha=$_GET['fecha'];
    $datos = "exec [db_owner].[sp_RepFSI82493v2] '$fecha',$tanque";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['FSI61194']) && ISSET($_GET['tanque']) && ISSET($_GET['fecha'])){// Reporte Remisión Alcoholes de Entrega Blending
    $tanque=$_GET['tanque'];
    $fecha=$_GET['fecha'];
    $datos = "if exists(select * from WM_EnviosBlending where idTanque='$tanque' and CONVERT(varchar(10), Fecha,120)='$fecha')
    select E.EnvioNo,E.idTanque,E.tq,E.fcv,D.idItem,D.Litros,I.Codigo,I.Año,T.Codigo as Tanque from WM_EnviosBlending E
    inner join WM_EnviosBlendingDetalle D on E.IdEnvio=D.IdEnvio inner join CM_Item I on D.idItem=I.IdItem
    inner join CM_Tanque T on E.idTanque=T.IDTanque where E.idTanque='$tanque' and CONVERT(varchar(10), Fecha,120)='$fecha'
    else select ISNULL((select top 1 EnvioNo from WM_EnviosBlending order by EnvioNo desc),54)+1 as Envio ";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['FSI82498']) && ISSET($_GET['tanque']) && ISSET($_GET['fecha'])){//
    $tanque=$_GET['tanque'];
    $fecha=$_GET['fecha'];
    $datos = "exec sp_RepFSI82498_v2 '$fecha',$tanque";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['RepOPDetalle']) && ISSET($_GET['fecha']) && ISSET($_GET['ope'])){//
    $ope=$_GET['ope'];
    $fecha=$_GET['fecha'];
    $datos = "exec sp_RepOPDetalle_v2 '$ope','$fecha'";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['tanques'])){
    $tanques = "SELECT IDTanque,Descripcion from CM_Tanque order by case IsNumeric(Codigo) when 1 then Replicate('0', 100 - Len(Codigo)) + Codigo else Codigo end";
    imprimir($tanques,$conn);
  }else if(ISSET($_GET['tanque'])){
    $tanque=$_GET['tanque'];
    $usuarios = "SELECT IDTanque,Codigo,Descripcion,Capacidad,Tipo from CM_Tanque where IDTanque=$tanque";
    imprimir($usuarios,$conn);
  }else if(ISSET($_GET['alcohol']) ){// Reporte Remisión Alcoholes de Entrega Blending
    $alcohol=$_GET['alcohol'];
    $datos = "SELECT IdItem,Codigo,Año from CM_Item ".($alcohol!=='true'?' where IdItem='.$alcohol:'')." order by Año";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['envios'])){//Historico de envios de remision
    $datos = "SELECT E.EnvioNo,T.Codigo as Tanque,convert(varchar(10),E.fecha,120) as Fecha,E.tq,E.fcv from WM_EnviosBlending E
    inner join CM_Tanque T on E.idTanque=T.IDTanque order by E.EnvioNo";
    imprimir($datos,$conn);
  }
}else{
  echo '..Error.. No tienes acceso a esta area';
}
sqlsrv_close($conn);
?>
