<?php
include '../general_connection.php';
if(strpos($permisos,',6,') !== false){
  if(ISSET($_POST["data"])){//Agregar ordenes
    if(isJson($_POST["data"])){
      $data = json_decode($_POST["data"], true);
      $enviar="";
      $contar=0;$contarErr=0;
      foreach($data as $campo) {
        $query="exec sp_OrdenTrasHoover '".$usuario."','".$campo['Cantidad']."','".$campo['tanque']."','".$campo['tanqueDest']."', 8";
        $stmtCheck = sqlsrv_query( $conn , $query);
        $row = sqlsrv_fetch_array( $stmtCheck, SQLSRV_FETCH_NUMERIC);
        if($row[0]!==""){
          $enviar=$enviar."\n".$row[0]."\n";
          $contarErr++;
        }
      }
      if($enviar===""){
        echo count($data).' órdenes registradas con exito';
      }else{
        echo '..Error.. '.$contarErr.' órdenes tuvieron errores: '.$enviar.' No hay litros suficientes para esta órden, alguna otra órden necesitará litros con estas caracteristicas, intenta con un número de litros disponibles o termina las órdenes pendientes';
      }

    }else{
      echo '..Error.. Se generó un problema al generar el Json';
    }
  }else if(ISSET($_POST['orden']) && ISSET($_POST['operador']) && ISSET($_POST['motaca']) && ISSET($_POST['supervisor'])){//Actualizar o asignar orden
    $orden=$_POST['orden'];
    $operador=$_POST['operador'];
    $motaca=$_POST['motaca'];
    $supervisor=$_POST['supervisor'];
    if(updateOrden($orden,$conn,$operador,$motaca,$supervisor)){
      generarNotificacion($orden,4,1,$usuario,'-1',$conn);
    }
  }else if(ISSET($_POST['cancelarOrdenId'])){//Cancelar orden
    $orden=$_POST['cancelarOrdenId'];
    if(cancelarOrden($orden,$conn)){
      generarNotificacion($orden,4,4,$usuario,'-1',$conn);
    }
  }else if(ISSET($_POST['terminarOrdenId'])){//Cancelar orden
    $orden=$_POST['terminarOrdenId'];
    if(terminarOrden($orden,$conn)){
      generarNotificacion($orden,4,3,$usuario,'-1',$conn);
    }
  }

}else{
  echo '..Error.. No tienes permisos para procesar cambios';
}
sqlsrv_close($conn); //Close the connnectiokn first

?>
