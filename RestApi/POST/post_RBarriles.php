<?php
include '../general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){
  include '../revisar_permisos.php';
  if(strpos($permisos,',8,') !== false){
    if(ISSET($_POST['Cant']) && ISSET($_POST['Capac']) && ISSET($_POST['IdCodEdad']) && ISSET($_POST['Area']) && ISSET($_POST['Provee'])&& ISSET($_POST['OC'])){//Crear orden
      $Cant=$_POST['Cant'];
      $Capac=$_POST['Capac'];
      $IdCodEdad=$_POST['IdCodEdad'];
      $Area=$_POST['Area'];
      $Provee=$_POST['Provee'];
      $OC=$_POST['OC'];
      if($Cant>0 && $Capac>0 && $IdCodEdad!=="" && $Area!=="" && $Provee!==""){
        $tsql = "exec SP_Barricainsert '$Cant' , '$Capac','$IdCodEdad','$Area','$Provee','$OC'";
        $stmt = sqlsrv_query( $conn , $tsql);
        if($stmt){
          echo 'Barriles registrados con exito';
        }else{
          echo '..Error.. Hubo un problema al registrar los barriles';
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
