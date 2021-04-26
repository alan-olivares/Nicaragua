<?php
$usuario=ISSET($_GET['usuario'])?$_GET['usuario']:"null";
$pass=ISSET($_GET['pass'])?$_GET['pass']:"null";
include'general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){
  include'revisar_permisos.php';
  if(strpos($permisos,',6,') !== false){
    if(ISSET($_GET['lotes'])){
      $lotes = "exec sp_LoteTrasiego";
      imprimir($lotes,$conn);

    }else if(ISSET($_GET['fechasLotes'])){//Obtenemos informacion de las ordenes por la fecha
      $fecha = "SELECT DISTINCT(convert(varchar, Fecha, 23)) as Fecha from PR_Orden where IdTipoOp in (5,6) order by Fecha";
      imprimir($fecha,$conn);

    }else if(ISSET($_GET['fechaOrdenes'])){//Obtenemos las ordenes dadas por una fecha en especifico
      $fecha=$_GET['fechaOrdenes'];
      $ordenes = "exec sp_OrdenTras '$fecha'";
      imprimir($ordenes,$conn);

    }else if(ISSET($_GET['operador'])){//Operadores dado a un grupo
      $operador=$_GET['operador'];
      $usuarios = "SELECT Nombre,IdUsuario as Id from CM_Usuario where IdGrupo=$operador";
      imprimir($usuarios,$conn);

    }else if(ISSET($_GET['tanques'])){//Operadores dado a un grupo
      $usuarios = "SELECT IDTanque,Codigo from CM_Tanque";
      imprimir($usuarios,$conn);
    }
  }else{
    echo '..Error.. No tienes acceso a esta area';
  }

}else{
  echo '..Error.. Acceso no autorizado';
}
function imprimir($query,$conn){
  $stmt = sqlsrv_query( $conn , $query);
  if($stmt){
    $result = array();
    do {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
           $result[] = array_map("utf8_encode",$row);
        }
    } while (sqlsrv_next_result($stmt));
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    sqlsrv_free_stmt($stmt);
  }else{
    echo 'Hubo un error al obtener los datos, intenta de nuevo más tarde';
  }
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
