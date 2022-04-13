<?php
include '../general_connection.php';
if(strpos($permisos,',6,') !== false){//Agregar ordenes
  if(ISSET($_POST["data"])){
    if(isJson($_POST["data"])){
      $data = json_decode($_POST["data"], true);
      $enviar="";
      $contarErr=0;
      foreach($data as $campo) {
        $query="exec sp_OrdenTrasInsert_v2 '".$usuario."','".$campo['bodega']."','".$campo['year']."','".$campo['Alcohol']."','".$campo['Uso']."','".$campo['Cantidad']."','".$campo['tanque']."', 5";
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
        echo '..Error.. '.$contarErr.' órdenes tuvieron errores: '.$enviar.' No hay barriles suficientes para esta órden, alguna otra órden necesitará barriles con estas caracteristicas, intenta con un número de barriles disponibles o cancela las órdenes pendientes';
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
      generarNotificacion($orden,3,1,$usuario,'-1',$conn);
    }
  }else if(ISSET($_POST['cancelarOrdenId'])){//Cancelar orden
    $orden=$_POST['cancelarOrdenId'];
    if(cancelarOrden($orden,$conn)){
      generarNotificacion($orden,3,4,$usuario,'-1',$conn);
    }
  }else if(ISSET($_POST['terminarOrdenId'])){//Cancelar orden
    $orden=$_POST['terminarOrdenId'];
    if(terminarOrden($orden,$conn)){
      generarNotificacion($orden,3,3,$usuario,'-1',$conn);
    }
  }
}else{
  echo '..Error.. No tienes permisos para procesar cambios';
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
