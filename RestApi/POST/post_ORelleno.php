<?php
include '../general_connection.php';
if(strpos($permisos,',6,') !== false){
  if(ISSET($_POST['bodega']) && ISSET($_POST['year']) && ISSET($_POST['Alcohol']) && ISSET($_POST['Uso']) && ISSET($_POST['Cantidad'])){//Crear orden
    $bodega=$_POST['bodega'];
    $year=$_POST['year'];
    $Alcohol=$_POST['Alcohol'];
    $Uso=$_POST['Uso'];
    $Cantidad=$_POST['Cantidad'];
    $tsql = "exec sp_OrdenRelleInsert '$usuario' , '$bodega','$year','$Alcohol','$Uso','$Cantidad'";
    $stmt = sqlsrv_query( $conn , $tsql);
    if($stmt){
      echo 'Ordenes registradas con exito';
    }else{
      echo '..Error.. Hubo un problema al registrar las ordenes';
    }
  }else if(ISSET($_POST['orden']) && ISSET($_POST['operador']) && ISSET($_POST['motaca']) && ISSET($_POST['supervisor'])){//Actualizar o asignar orden
    $orden=$_POST['orden'];
    $operador=$_POST['operador'];
    $motaca=$_POST['motaca'];
    $supervisor=$_POST['supervisor'];
    $check="SELECT Estatus from PR_Orden where IdOrden=$orden";
    $stmtCheck = sqlsrv_query( $conn , $check);
    $row = sqlsrv_fetch_array( $stmtCheck, SQLSRV_FETCH_NUMERIC);
    if($row[0]=='1' || $row[0]=='0'){
      $tsql = "UPDATE PR_Orden SET Estatus=1 ,IdOperario=$operador, IdOperarioMon=$motaca,IdSupervisor=$supervisor where IdOrden=$orden";
      $stmt = sqlsrv_query( $conn , $tsql);
      if($stmt){
        echo 'Ordenen asignada con exito';
      }else{
        echo '..Error.. Hubo un problema al asignar la orden, intenta de nuevo mas tarde';
      }
    }else{
      echo '..Error.. Esta orden ya se encuentra en proceso y no puede ser modificada';
    }
  }

}else{
  echo '..Error.. No tienes permisos para procesar cambios';
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
