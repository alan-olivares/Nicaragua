<?php
//$usuario=ISSET($_POST['usuario'])?$_POST['usuario']:"null";
//$pass=ISSET($_POST['pass'])?$_POST['pass']:"null";
include '../general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){
  include '../revisar_permisos.php';
  if(strpos($permisos,',8,') !== false){
    if(ISSET($_POST['Capac']) && ISSET($_POST['NoSerie']) && ISSET($_POST['Ref'])){//Crear nuevo tanque hoover
      $Capac=$_POST['Capac'];
      $NoSerie=$_POST['NoSerie'];
      $Ref=$_POST['Ref'];
      $tsql = "SELECT count(*) from WM_Tanques where NoSerie='$NoSerie'";
      $veri=ObtenerCantidad($tsql,$conn);
      if($veri>0){
        echo '..Error.. Este número de serie ya se encuentra registrado en la base de datos, verificalo y vuelve a intentarlo';
      }else if($Capac>0 && $NoSerie>0 && $NoSerie<999999){
        $tsql = "INSERT INTO WM_Tanques (NoSerie,IdPallet,Capacidad,Litros,FechaRecepcion) values('$NoSerie',0,'$Capac',0,GETDATE())";
        $stmt = sqlsrv_query( $conn , $tsql);
        if($stmt){
          echo 'Tanque registrado con exito';
        }else{
          echo '..Error.. Hubo un problema al registrar el tanque';
        }
      }else{
        echo '..Error.. Los datos ingresados fueron erroneos';
      }

    }
  }else{
    echo '..Error.. No tienes permisos para procesar cambios';
  }

}else{
  echo '..Error.. Acceso no autorizado';
}
sqlsrv_close($conn); //Close the connnectiokn first

function ObtenerCantidad($queryCons,$conn){
  $resultCons = sqlsrv_query( $conn , $queryCons);
  $row = sqlsrv_fetch_array( $resultCons, SQLSRV_FETCH_NUMERIC);
  return (int)$row[0];
}
?>
