<?php
include '../general_connection.php';
if(strpos($permisos,',6,') !== false){
  if(ISSET($_POST["data"])){//Crear orden
    if(isJson($_POST["data"])){
      $data = json_decode($_POST["data"], true);
      $enviar="";
      $contar=0;$contarErr=0;
      foreach($data as $campo) {
        $query="exec sp_OrdenRelleInsert_v2 '".$usuario."','".$campo['bodega']."','".$campo['year']."','".$campo['Alcohol']."','".$campo['Uso']."','".$campo['Cantidad']."'";
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
        echo '..Error.. '.$contarErr.' órdenes tuvieron errores: '.$enviar.' No hay barriles suficientes para esta órden, alguna otra órden necesitará barriles con estas caracteristicas, intenta con un número de barriles disponibles o termina las órdenes pendientes';
      }

    }else{
      echo '..Error.. Se generó un problema al generar el Json';
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
        echo 'Orden asignada con exito';
        //Generamos la notificacion
        generarNotificacion($orden,2,1,$usuario,'-1',$conn);
      }else{
        echo '..Error.. Hubo un problema al asignar la orden, intenta de nuevo mas tarde';
      }
    }else{
      echo '..Error.. Esta orden ya se encuentra en proceso y no puede ser modificada';
    }
  }else if(ISSET($_POST['cancelarOrdenId'])){//Cancelar orden
    $orden=$_POST['cancelarOrdenId'];
    $check="SELECT Estatus from PR_Orden where IdOrden=$orden";
    $stmtCheck = sqlsrv_query( $conn , $check);
    $row = sqlsrv_fetch_array( $stmtCheck, SQLSRV_FETCH_NUMERIC);
    if($row[0]=='1' || $row[0]=='0'){
      $tsql = "UPDATE PR_Orden SET Estatus=3,IdOperario=0, IdOperarioMon=0,IdSupervisor=0 where IdOrden=$orden";
      $stmt = sqlsrv_query( $conn , $tsql);
      if($stmt){
        echo 'Orden cancelada con exito';
        generarNotificacion($orden,2,4,$usuario,'-1',$conn);
      }else{
        echo '..Error.. Hubo un problema al cancelar la orden, intenta de nuevo mas tarde';
      }
    }else{
      echo '..Error.. Esta orden ya se encuentra en proceso y no puede ser cancelada';
    }
  }else if(ISSET($_POST['terminarOrdenId'])){//Terminar orden
    $orden=$_POST['terminarOrdenId'];
    $check="SELECT Estatus from PR_Orden where IdOrden=$orden";
    $stmtCheck = sqlsrv_query( $conn , $check);
    $row = sqlsrv_fetch_array( $stmtCheck, SQLSRV_FETCH_NUMERIC);
    if($row[0]!='0'){
      $tsql = "UPDATE PR_Orden SET Estatus=3 where IdOrden=$orden";
      $stmt = sqlsrv_query( $conn , $tsql);
      if($stmt){
        echo 'Orden terminada con exito';
        generarNotificacion($orden,2,3,$usuario,'-1',$conn);
      }else{
        echo '..Error.. Hubo un problema al terminar la orden, intenta de nuevo mas tarde';
      }
    }else{
      echo '..Error.. Esta orden no puede ser terminada porque aún no se encuentra en proceso';
    }
  }

}else{
  echo '..Error.. No tienes permisos para procesar cambios';
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
