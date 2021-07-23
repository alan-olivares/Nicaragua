<?php
//$usuario=ISSET($_GET['usuario'])?$_GET['usuario']:"null";
//$pass=ISSET($_GET['pass'])?$_GET['pass']:"null";
include '../general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){
  include '../revisar_permisos.php';
  if(strpos($permisos,',7,') !== false){
    if(ISSET($_GET['detalleBodega'])){
      $bodega=$_GET['detalleBodega'];
      $bodegas = "exec sp_BodDetalle2 '$bodega'";
      imprimir($bodegas,$conn);
    }else if(ISSET($_GET['plantas']) && ISSET($_GET['bodegas']) && ISSET($_GET['areas']) && ISSET($_GET['filas']) && ISSET($_GET['torres']) && ISSET($_GET['niveles'])){
      $planta=$_GET['plantas'];
      $bodega=$_GET['bodegas'];
      $area=$_GET['areas'];
      $fila=$_GET['filas'];
      $torres=$_GET['torres'];
      $niveles=$_GET['niveles'];
      $barriles = "SELECT isnull((select ('01' + right('00' + convert(varChar(2),1),2) + right('000000' + convert(varChar(6),B.Consecutivo),6))),'Sin Asignar') as Etiqueta,
      B.Capacidad,C.Codigo as Barril,E.Codigo as Edad
      from AA_Plantas Pl left join AA_Almacen A on Pl.PlantaID=A.PlantaID inner Join AA_Area AA on AA.AlmacenId = A.AlmacenID
      inner Join AA_Seccion S on S.AreaId = AA.AreaId  inner join AA_Posicion P on P.SeccionID = S.SeccionID
      inner Join AA_Nivel N on N.PosicionId = P.PosicionID  inner Join WM_RackLoc RL on RL.NivelID = n.NivelID
      inner Join WM_Pallet Pa on Pa.RackLocId = RL.RackLocID inner Join WM_Barrica B on B.IdPallet = Pa.IdPallet
      Left join CM_CodEdad CE on CE.IdCodEdad= B.IdCodificacion LEft join CM_Codificacion C on C.IdCodificacion = CE.IdCodificicacion
      Left Join CM_Edad E on E.IdEdad = CE.IdEdad Where Pl.PlantaID=$planta ";
      if($bodega!=="null")
        $barriles=$barriles." and A.AlmacenID=".$bodega;
      if($area!=="null")
        $barriles=$barriles." and AA.AreaId=".$area;
      if($fila!=="null")
        $barriles=$barriles." and S.SeccionID=".$fila;
      if($torres!=="null")
        $barriles=$barriles." and P.PosicionID=".$torres;
      if($niveles!=="null")
        $barriles=$barriles." and N.NivelID=".$niveles;
      $barriles=$barriles." order by B.IdCodificacion";
      //echo $barriles;
      imprimir($barriles,$conn);
    }else if(ISSET($_GET['plantas'])){
      $bodegas = "SELECT * from AA_Plantas";
      imprimir($bodegas,$conn);
    }else if(ISSET($_GET['bodegas'])){
      $bodegas = "SELECT * from AA_Almacen where PlantaID=1";
      imprimir($bodegas,$conn);
    }else if(ISSET($_GET['areas'])){
      $bodega=$_GET['areas'];
      $areas = "SELECT * from AA_Area where AlmacenId=$bodega";
      imprimir($areas,$conn);
    }else if(ISSET($_GET['filas'])){
      $area=$_GET['filas'];
      $filas = "SELECT * from AA_Seccion where AreaId=$area";
      imprimir($filas,$conn);
    }else if(ISSET($_GET['torres'])){
      $fila=$_GET['torres'];
      $torres = "SELECT * from AA_Posicion where SeccionID=$fila";
      imprimir($torres,$conn);
    }else if(ISSET($_GET['niveles'])){
      $torre=$_GET['niveles'];
      $niveles = "SELECT * from AA_Nivel where PosicionId=$torre";
      imprimir($niveles,$conn);
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
