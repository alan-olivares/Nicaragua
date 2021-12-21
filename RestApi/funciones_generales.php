<?php
/**
 * Metodo que convierte la consulta de un sql a Json
 *
 * @param type $query - Query de la consulta
 * @param type $conn - Variable de la conexión activa a la base de datos
 * @return JSON datos
 */
function imprimir($query,$conn){
  try {
    $stmt = sqlsrv_query( $conn , $query);
    if($stmt){
      $result = array();
      do {
          while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
             $result[] = $row;
          }
      } while (sqlsrv_next_result($stmt));
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
      sqlsrv_free_stmt($stmt);
    }else{
      echo '..Error.. Hubo un error al obtener los datos, intenta de nuevo más tarde';
    }
  } catch (\Exception $e) {
    echo '..Error.. '.$e;
  }


}
/**
 * Obtiene un valor entero de una consulta que recibe como parametro
 *
 * @param type $queryCons - Query de la consulta
 * @param type $conn - Variable de la conexión activa a la base de datos
 * @return value entero obtenido
 */
function ObtenerCantidad($queryCons,$conn){
  try {
    $resultCons = sqlsrv_query( $conn , $queryCons);
    $row = sqlsrv_fetch_array( $resultCons, SQLSRV_FETCH_NUMERIC);
    if(empty($row))
      return -1;
    return (int)$row[0];
  } catch (\Exception $e) {
    return -1;
  }
}
function ejecutarDato($conn,$queryCons){
  try {
    return sqlsrv_query( $conn , $queryCons);
  } catch (\Exception $e) {
    return false;
  }
}
/**
 * Valida si un String se puede convertir en un JSON
 *
 * @param type $string - String en forma de JSON
 * @return is_bool
 */
function isJson($string) {
 json_decode($string);
 return (json_last_error() == JSON_ERROR_NONE);
}

function generarNotificacion($referencia,$caso,$estatus,$enviador,$receptor,$conn){
  //Borramos las notificaciones pasadas si existen
  sqlsrv_query( $conn , "delete from ADM_Notificaciones where convert(varchar,fecha,105) <>convert(varchar,GETDATE(),105)");
  //Generamos la notificacion
  $titulo=($caso==1?'Lote de alcohol ':'Orden'.getCaso($caso)).$referencia.getMsgTipo($estatus).($caso==1?'o':'a');
  $mensaje=' ha'.getMsgTipo($estatus).($caso==1?'o el lote ':'o la orden ').$referencia;
  $tsql = "if not exists(select * from ADM_Notificaciones where referencia='$referencia' and Estatus='$estatus')
  INSERT into ADM_Notificaciones(referencia,caso,Estatus,titulo,mensaje,idEnviador,idReceptor,fecha) values('$referencia','$caso',$estatus,'$titulo',
  concat((select nombre from cm_usuario where Clave='$enviador'),'$mensaje'),ISNULL((select idusuario from cm_usuario where Clave='$enviador'),0),
  ISNULL((select idusuario from cm_usuario where Clave='$receptor'),0),GETDATE())";
  return sqlsrv_query( $conn , $tsql);
}
function getMsgTipo($estatus){
  $regresar="";
  if($estatus==1){
    $regresar=' asignad';
  }else if($estatus==2){
    $regresar= ' conmenzad';
  }else if($estatus==3){
    $regresar= ' terminad';
  }else if($estatus==4){
    $regresar= ' cancelad';
  }else{
    return '';
  }
  return $regresar;
}
function getCaso($caso){
  $regresar="";
  if($caso==1){
    $regresar=' de llenado ';
  }else if($caso==2){
    $regresar= ' de relleno ';
  }else if($caso==3){
    $regresar= ' de trasiego ';
  }else if($caso==4){
    $regresar= ' de trasiego hoover ';
  }else{
    return ' ';
  }
  return $regresar;
}
/**
 * Termina el script que se está ejecutando
 *
 * @param type $conn - Variable de la conexión activa a la base de datos
 * @param type $mensaje - Mensaje que se le mostrará al usuario
 */
function terminarScript($conn,$mensaje) {
 sqlsrv_close($conn);
 exit($mensaje);
}
 ?>
