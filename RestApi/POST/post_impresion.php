<?php
include '../general_connection.php';
if(strpos($permisos,',5,') !== false){
  $etiqueta=$_POST["etiqueta"];
  $queryCons="SELECT COUNT(*) from AA_Impresion where IdAsText='$etiqueta'";
  if((ObtenerCantidad($queryCons,$conn)!=0 && AgregarHistorial($etiqueta,$usuario,$conn)) || (AgregarImpresion($etiqueta,$conn) && AgregarHistorial($etiqueta,$usuario,$conn))){
    echo 'Correcto';
  }else{
    echo '..Error.. Hubo un problema al guardar los registros';
  }
}else{
  echo '..Error.. No tienes permisos para imprimir';
}
sqlsrv_close($conn); //Close the connnectiokn first

function AgregarHistorial($etiqueta,$usuario,$conn){
  $queryCons="INSERT into AA_ImpresionHist (Idimpresion,IDUSUARIO,Fecha) values((SELECT top 1 Idimpresion from AA_Impresion where IdAsText='$etiqueta'),
  (SELECT IdUsuario from CM_Usuario_WEB where Clave='$usuario'),
              (SELECT GETDATE()))";
  $resultCons = sqlsrv_query( $conn , $queryCons);
  return $resultCons;
}
function AgregarImpresion($etiqueta,$conn){
  $queryCons="SELECT Val1 from CM_Config where IdConfig=4";
  $Planta=ObtenerCantidad($queryCons,$conn);
  $queryCons="INSERT into AA_Impresion (IdPlanta,IdRecurso,IdAsText) values('$Planta','01','$etiqueta')";
  $resultCons = sqlsrv_query( $conn , $queryCons);
  return $resultCons;
}

?>
