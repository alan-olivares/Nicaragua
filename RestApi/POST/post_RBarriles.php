<?php
include '../general_connection.php';
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
        echo $Cant.' barriles registrados con exito';
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
sqlsrv_close($conn); //Close the connnectiokn first

?>
