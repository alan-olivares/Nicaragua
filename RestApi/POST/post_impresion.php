<?php
//$usuario=ISSET($_POST['usuario'])?$_POST['usuario']:"null";
//$pass=ISSET($_POST['pass'])?$_POST['pass']:"null";
include '../general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){
  include '../revisar_permisos.php';
  if(strpos($permisos,',5,') !== false){
    $etiqueta=$_POST["etiqueta"];
    $queryCons="SELECT COUNT(*) from AA_Impresion where IdAsText='$etiqueta'";
    if(ObtenerCantidad($queryCons,$conn)!=0 && AgregarHistorial($etiqueta,$usuario,$conn)){
      echo 'Correcto';
    }else if(AgregarImpresion($etiqueta,$conn) && AgregarHistorial($etiqueta,$usuario,$conn)){
      echo 'Correcto';
    }else{
      echo '..Error.. Hubo un problema al guardar los registros';
    }

  }else{
    echo '..Error.. No tienes permisos para imprimir';
  }
}else{
  echo '..Error.. Acceso no autorizado';
}
sqlsrv_close($conn); //Close the connnectiokn first

function AgregarHistorial($etiqueta,$usuario,$conn){
  $queryCons="SELECT top 1 Idimpresion from AA_Impresion where IdAsText='$etiqueta'";
  $resultCons = sqlsrv_query( $conn , $queryCons);
  $row = sqlsrv_fetch_array( $resultCons, SQLSRV_FETCH_NUMERIC);
  $queryCons="INSERT into AA_ImpresionHist (Idimpresion,IDUSUARIO,Fecha) values('$row[0]',(SELECT IdUsuario from CM_Usuario where Clave='$usuario'),
              (SELECT GETDATE()))";
  $resultCons = sqlsrv_query( $conn , $queryCons);
  return $resultCons;
}
function AgregarImpresion($etiqueta,$conn){
  $queryCons="SELECT Val1 from CM_Config where IdConfig=4";
  $resultCons = sqlsrv_query( $conn , $queryCons);
  $row = sqlsrv_fetch_array( $resultCons, SQLSRV_FETCH_NUMERIC);
  $Planta=(int)$row[0];
  $queryCons="INSERT into AA_Impresion (IdPlanta,IdRecurso,IdAsText) values('$Planta','01','$etiqueta')";
  $resultCons = sqlsrv_query( $conn , $queryCons);
  return $resultCons;
}
function ObtenerCantidad($queryCons,$conn){
  $resultCons = sqlsrv_query( $conn , $queryCons);
  $row = sqlsrv_fetch_array( $resultCons, SQLSRV_FETCH_NUMERIC);
  return (int)$row[0];
}
?>
