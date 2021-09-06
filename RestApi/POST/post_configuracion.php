<?php
include '../general_connection.php';
if(strpos($permisos,',4,') !== false){
  if(ISSET($_POST["data"])){
    if(isJson($_POST["data"])){
      $data = json_decode($_POST["data"], true);
      foreach ($data as $campo) {
        $query="UPDATE CM_Config set Val1=N'".$campo['valor']."' where IdConfig=".$campo['id'];
        sqlsrv_query( $conn , $query);
      }
      echo 'Configuración actualizada correctamiente';
    }else{
      echo '..Error.. Insertaste algún caracter no valido';
    }
  }else if(ISSET($_POST["nuevoMotivo"])){
    $evento=$_POST["evento"];
    $razon=$_POST["razon"];
    $query="INSERT into ADM_Razones (IdCaso,Descripcion) values ($evento,'$razon')";
    $resultCons =sqlsrv_query( $conn , $query);
    if($resultCons){
      echo 'Motivo agregado con exito';
    }else{
      echo '..Error.. hubo un problema al agregar esté motivo';
    }
  }else if(ISSET($_POST["borrarMotivo"])){
    $razon=$_POST["razon"];
    $query="DELETE from ADM_Razones where IdRazon=$razon";
    $resultCons =sqlsrv_query( $conn , $query);
    if($resultCons){
      echo 'Motivo eliminado con exito';
    }else{
      echo '..Error.. hubo un problema al eliminar esté motivo, esté motivo ya puede estar seleccionado en alguna solicitud';
    }
  }else if(ISSET($_POST["updateMotivo"])){
    $razon=$_POST["razon"];
    $descripcion=$_POST["descripcion"];
    $query="UPDATE ADM_Razones set Descripcion='$descripcion' where IdRazon=$razon";
    $resultCons =sqlsrv_query( $conn , $query);
    if($resultCons){
      echo 'Motivo actualizado con exito';
    }else{
      echo '..Error.. hubo un problema al actualizar esté motivo';
    }
  }

}else{
  echo '..Error.. No tienes permisos para procesar cambios';
}
?>
