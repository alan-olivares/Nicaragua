<?php
include '../general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){
  include '../revisar_permisos.php';
  if(strpos($permisos,',8,') !== false){
    if(ISSET($_GET['tanques'])){//Ultimo consecutivo e impresión
      $barriles = "SELECT T.NoSerie as 'N° de serie',
      CASE WHEN Am.Nombre is null THEN 'Tanque sin ubicación' ELSE CONCAT(Am.Nombre,', ', REPLACE(Ar.Nombre, 'COSTADO', 'Cos: '),', ',REPLACE(Se.Nombre, 'FILA', 'F: '),',', REPLACE(Po.Nombre, 'TORRE', 'T: ') ,',', REPLACE(N.Nombre, 'NIVEL', 'N: ')) END AS Ubicación,
      T.Capacidad,T.Litros,Convert(varchar,T.FechaLLenado,105) as 'Fecha de llenado',Convert(varchar,T.FechaRecepcion,105) as 'Fecha de recepción'
      from WM_Tanques T left Join WM_Pallet P on P.Idpallet = T.IdPallet
      left join WM_RackLoc R on P.RackLocID=R.RackLocID left Join AA_Nivel N on R.NivelID=N.NivelID
      left Join AA_Posicion Po on N.PosicionId=Po.PosicionID left Join AA_Seccion Se on Po.SeccionID=Se.SeccionID
      left Join AA_Area Ar on Se.AreaId = Ar.AreaId left Join AA_Almacen Am on Ar.AlmacenId=Am.AlmacenID";
      imprimir($barriles,$conn);
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
           $result[] = $row;
        }
    } while (sqlsrv_next_result($stmt));
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    sqlsrv_free_stmt($stmt);
  }else{
    echo '..Error.. Hubo un error al obtener los datos, intenta de nuevo más tarde';
  }
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
