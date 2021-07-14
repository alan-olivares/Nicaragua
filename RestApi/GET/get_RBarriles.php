<?php
include '../general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){
  include '../revisar_permisos.php';
  if(strpos($permisos,',8,') !== false){
    if(ISSET($_GET['barriles'])){//Ultimo consecutivo e impresión
      $barriles = "Declare @ConseLast	bigint, @ImpreLast	varchar(20)
      set @ConseLast = isnull((select Top 1 Consecutivo from WM_Barrica order by Consecutivo desc),0)
      Set @ImpreLast = isnull((select Top 1IdAsText from AA_Impresion where IdRecurso = 1 order by Idimpresion desc),0)
      select @ConseLast	as Conselast,	@ImpreLast	as ImpreLast";
      imprimir($barriles,$conn);
    }else if(ISSET($_GET['planteles'])){//Planteles existentes
      $fecha = "select Ar.IdArea, A.Nombre
      from WM_RackLoc RL inner Join AA_Nivel N on N.NivelID = RL.NivelID
      Inner Join AA_Posicion P on P.PosicionID = N.PosicionId inner Join AA_Seccion S on S.SeccionID = P.SeccionID
      inner Join AA_Area A on A.AreaId = S.AreaId inner Join AA_Almacen AA on AA.AlmacenID = A.AlmacenId
      inner Join CM_Areas Ar on Ar.RackLocId = RL.RackLocID";
      imprimir($fecha,$conn);

    }else if(ISSET($_GET['codificacion'])){//Codificación de los barriles
      $ordenes = "select CE.IdCodEdad, (C.Codigo + ' - ' + E.Codigo + ' - ' + E.Descripcion ) as CodiEdad
      From CM_CodEdad CE inner Join CM_Codificacion C on C.IdCodificacion = CE.IdCodificicacion inner Join CM_Edad E on E.IdEdad = CE.IdEdad";
      imprimir($ordenes,$conn);
    }else if(ISSET($_GET['proveedor'])){
      $ordenes = "SELECT  IdProveedor,Descripcion FROM CM_Proveedor";
      imprimir($ordenes,$conn);
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
