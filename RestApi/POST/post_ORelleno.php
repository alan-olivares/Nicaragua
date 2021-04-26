<?php
$usuario=ISSET($_POST['usuario'])?$_POST['usuario']:"null";
$pass=ISSET($_POST['pass'])?$_POST['pass']:"null";
include'general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){
  include'revisar_permisos.php';
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
