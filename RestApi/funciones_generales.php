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
