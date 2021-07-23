<?php
//$usuario=ISSET($_GET['usuario'])?$_GET['usuario']:"null";
//$pass=ISSET($_GET['pass'])?$_GET['pass']:"null";
include '../general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){
  include '../revisar_permisos.php';
  if(strpos($permisos,',6,') !== false){
    if(ISSET($_GET['annio']) && ISSET($_GET['alcohol']) && ISSET($_GET['uso']) && ISSET($_GET['fechas'])){
      $barriles = "SELECT  B.Consecutivo, B.NoTapa, B.Capacidad, CONVERT(varchar(10),B.FechaRevisado, 120) as Revisado,
      CONVERT(varchar(10),B.FechaRelleno, 120) as Relleno, B.Año, es.Descripcion as Estado, C.Codigo as Uso, E.Codigo as Edad,
      Al.Descripcion as Alcohol, Datepart(YYYY,L.Recepcion) as Recepcion,
      CASE WHEN Am.Nombre is null THEN 'Barril sin ubicación' ELSE CONCAT(Am.Nombre,', ', REPLACE(Ar.Nombre, 'COSTADO', 'Cos: '),', ',REPLACE(Se.Nombre, 'FILA', 'F: '),',', REPLACE(Po.Nombre, 'TORRE', 'T: ') ,',', REPLACE(N.Nombre, 'NIVEL', 'N: ')) END AS Ubicacion
      from WM_Barrica B inner Join WM_Pallet P on P.Idpallet = B.IdPallet
      left Join CM_CodEdad CE on CE.IdCodEdad = B.IdCodificacion left Join CM_Codificacion C on C.IdCodificacion = CE.IdCodificicacion
      left Join CM_Edad E on E.IdEdad = CE.IdEdad  left Join WM_LoteBarrica LB on Lb.IdLoteBarica = B.IdLoteBarrica
      left Join PR_Lote L on L.IdLote = LB.IdLote left Join CM_Alcohol Al on Al.IdAlcohol = L.IdAlcohol
      left Join CM_Estado ES on Es.IdEstado = B.IdEstado left join WM_RackLoc R on P.RackLocID=R.RackLocID
      inner Join AA_Nivel N on R.NivelID=N.NivelID inner Join AA_Posicion Po on N.PosicionId=Po.PosicionID
      inner Join AA_Seccion Se on Po.SeccionID=Se.SeccionID inner Join AA_Area Ar on Se.AreaId = Ar.AreaId
      inner Join AA_Almacen Am on Ar.AlmacenId=Am.AlmacenID where es.IdEstado=1 ";
      if($_GET['annio']!==''){
        $barriles=$barriles." and Datepart(YYYY,L.Recepcion)=".$_GET['annio'];
      }
      if($_GET['alcohol']!==''){
        $barriles=$barriles." and Al.IdAlcohol=".$_GET['alcohol'];
      }
      if($_GET['uso']!==''){
        $barriles=$barriles." and C.IdCodificacion=".$_GET['uso'];
      }
      if($_GET['fechas']!==''){
        $barriles=$barriles." and CONVERT(varchar(10),B.FechaRelleno, 120) ='".$_GET['fechas']."'";
      }
      //echo $barriles;
      imprimir($barriles,$conn);

    }else if(ISSET($_GET['annio'])){
      $lotes = "select distinct(Datepart(YYYY,Recepcion)) as annio from PR_Lote order by Datepart(YYYY,Recepcion) desc";
      imprimir($lotes,$conn);
    }else if(ISSET($_GET['alcohol'])){//Obtenemos informacion de las ordenes por la fecha
      $Alcohol = "select * from CM_Alcohol";
      imprimir($Alcohol,$conn);

    }else if(ISSET($_GET['uso'])){//Obtenemos las ordenes dadas por una fecha en especifico
      $uso = "select IdCodificacion,Codigo from CM_Codificacion";
      imprimir($uso,$conn);

    }else if(ISSET($_GET['fechasLotes'])){//Obtenemos informacion de los lotes por la fecha
      $fecha = "select DISTINCT(convert(varchar, Fecha, 23)) as Fecha from WM_LoteBarrica";
      imprimir($fecha,$conn);
    }else if(ISSET($_GET['motivo'])){//Obtenemos informacion de los lotes por la fecha
      $razones = "SELECT * from ADM_Razones where IdCaso=4";
      imprimir($razones,$conn);
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
