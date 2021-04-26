<?php
$usuario=ISSET($_GET['usuario'])?$_GET['usuario']:"null";
$pass=ISSET($_GET['pass'])?$_GET['pass']:"null";
include'general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){
  include'revisar_permisos.php';
  if(strpos($permisos,',2,') !== false){
    $GeneralError='..Error.. Hubó un problema con la base de datos, intenta de nuevo más tarde';
    if(ISSET($_GET['area'])){
      $area=$_GET["area"];
      $fila = "exec sp_AA_getFilas '$area'";
      $stmtFila = sqlsrv_query( $conn , $fila);
      if($stmtFila){
        $result = array();
        do {
          while ($row = sqlsrv_fetch_array($stmtFila, SQLSRV_FETCH_ASSOC)){
            $result[] = array_map("utf8_encode",$row);
          }
        } while (sqlsrv_next_result($stmtFila));

        echo json_encode($result);
      }else{
        echo $GeneralError;
      }
      sqlsrv_free_stmt($stmtFila);

    }else if(ISSET($_GET['bodega'])){
      $bodega=$_GET["bodega"];
      $costados = "exec sp_AA_getCostados '$bodega'";

      $stmtCostados = sqlsrv_query( $conn , $costados);
      if($stmtCostados){
        $result = array();

        do {
            while ($row = sqlsrv_fetch_array($stmtCostados, SQLSRV_FETCH_ASSOC)){
               $result[] = array_map("utf8_encode",$row);
            }
        } while (sqlsrv_next_result($stmtCostados));
        echo json_encode($result); //
      }else{
        echo $GeneralError;
      }
      sqlsrv_free_stmt($stmtCostados);
    }else if(ISSET($_GET['fila'])){
      $fila=$_GET["fila"];
      $torres = "exec sp_AA_getTorres '$fila'";

      $stmtTorres = sqlsrv_query( $conn , $torres);
      if($stmtTorres){
        $result = array();

        do {
            while ($row = sqlsrv_fetch_array($stmtTorres, SQLSRV_FETCH_ASSOC)){
               $result[] = array_map("utf8_encode",$row);
            }
        } while (sqlsrv_next_result($stmtTorres));
        echo json_encode($result); //
      }else{
        echo $GeneralError;
      }
      sqlsrv_free_stmt($stmtTorres);
    }else if(ISSET($_GET['torre'])){
      $torre=$_GET["torre"];
      $niveles = "exec sp_AA_getNiveles '$torre'";

      $stmtNiveles = sqlsrv_query( $conn , $niveles);
      if($stmtNiveles){
        $result = array();

        do {
            while ($row = sqlsrv_fetch_array($stmtNiveles, SQLSRV_FETCH_ASSOC)){
               $result[] = array_map("utf8_encode",$row);
            }
        } while (sqlsrv_next_result($stmtNiveles));
        echo json_encode($result);
      }else{
        echo $GeneralError;
      }
      sqlsrv_free_stmt($stmtNiveles);
    }else if(ISSET($_GET['Rack'])){
      //Nos regresa la tabla en forma de json
      $Rack=$_GET["Rack"];
      $tabla = "exec sp_BarrPallet '$Rack'";

      $stmtTabla = sqlsrv_query( $conn , $tabla);
      if($stmtTabla){
        $result = "[";
        while( $row = sqlsrv_fetch_array( $stmtTabla, SQLSRV_FETCH_NUMERIC) ) {
          if($row[6]==null){$row[6]= '';}else{$row[6]= $row[6]->format('Y-m-d');}
          if($row[7]==null){$row[7]= '';}else{$row[7]= $row[7]->format('Y-m-d');}
          if($row[3]==null){$row[3]= "\"\"";}
          if($row[4]==null){$row[4]= "\"\"";}
          if($row[8]==null){$row[8]= "\"\"";}
          if($row[9]==null){$row[9]= "\"\"";}
          if($row[12]==null){$row[12]= "\"\"";}
          if($row[2]==null){$row[2]= "\"\"";}
          $result=$result. "{\"Consecutivo\":".$row[3].","."\"IDLote\":".$row[2].",".
            "\"Capacidad\":".$row[4].","."\"Revisado\":\"".$row[6]."\","."\"Relleno\":\"".$row[7]."\",".
            "\"Año\":".$row[8].","."\"NoTapa\":".$row[9].","."\"Uso\":\"".$row[10]."\","."\"Edad\":\"".$row[11]."\",".
            "\"Recepcion\":".$row[12].","."\"Alcohol\":\"".$row[13]."\","."\"Estado\":\"".utf8_encode($row[14])."\"},";

        }
        if(strlen($result)>1){
          $result = substr($result, 0, -1);
        }
        $result=$result."]";
        echo $result;

        sqlsrv_free_stmt($stmtTabla);
      }else{
        echo $GeneralError;
      }

    }else if(ISSET($_GET['consecutivo'])){
      $Consecutivo=$_GET["consecutivo"];
      $barril = "exec sp_BarrilUbicacion '$Consecutivo'";
      $stmtBarril = sqlsrv_query( $conn , $barril);
      if($stmtBarril){
        $result = "[";

        while( $row = sqlsrv_fetch_array( $stmtBarril, SQLSRV_FETCH_NUMERIC) ) {
          if($row[3]==null){$row[3]= '';}else{$row[3]= $row[3]->format('Y-m-d');}
          if($row[4]==null){$row[4]= '';}else{$row[4]= $row[4]->format('Y-m-d');}
          if($row[0]==null){$row[0]= "\"\"";}
          if($row[2]==null){$row[2]= "\"\"";}
          if($row[5]==null){$row[5]= "\"\"";}
          if($row[1]==null){$row[1]= "\"\"";}
          if($row[10]==null){$row[10]= "\"\"";}
          $result=$result. "{\"Consecutivo\":".$row[0].","."\"NoTapa\":".$row[1].",".
            "\"Capacidad\":".$row[2].","."\"Revisado\":\"".$row[3]."\","."\"Relleno\":\"".$row[4]."\",".
            "\"Año\":".$row[5].","."\"Estado\":\"".utf8_encode($row[6])."\","."\"Uso\":\"".$row[7]."\","."\"Edad\":\"".$row[8]."\",".
            "\"Alcohol\":\"".$row[9]."\",\"Recepcion\":".$row[10].","."\"Ubicación\":\"".utf8_encode($row[11])."\"}";
        }

        $result=$result."]";
        echo $result;
        sqlsrv_free_stmt($stmtBarril);
      }else{
        echo $GeneralError;
      }

    }else if(ISSET($_GET['tapa'])){
      $tapa=$_GET["tapa"];
      $year=$_GET["year"];
      $barril = "select COUNT(*) from  WM_Barrica b
                left join  WM_LoteBarrica w on b.IdLoteBarrica=w.IdLoteBarica
                left join PR_Lote l on l.IdLote=w.IdLote
                left join CM_Alcohol A on l.IdAlcohol=A.IdAlcohol where b.NoTapa=$tapa and Datepart(YYYY, w.Fecha)='$year';";
      $stmtBarril = sqlsrv_query( $conn , $barril);
      if($stmtBarril){
        $row = sqlsrv_fetch_array( $stmtBarril, SQLSRV_FETCH_NUMERIC);
        if($row[0]!=0){
          echo '..Error.. Este número de tapa ya existe';
        }
      }else{
        echo $GeneralError;
      }

      sqlsrv_free_stmt($stmtBarril);
    }else if(ISSET($_GET['loteA'])){
      $loteA=$_GET["loteA"];
      $barril = "select COUNT(*) from WM_LoteBarrica where IdLoteBarica='$loteA';";
      $stmtBarril = sqlsrv_query( $conn , $barril);
      if($stmtBarril){
        $row = sqlsrv_fetch_array( $stmtBarril, SQLSRV_FETCH_NUMERIC);
        if($row[0]==0){
          echo '..Error.. Este lote de alcohol no existe';
        }
      }else{
        echo $GeneralError;
      }

      sqlsrv_free_stmt($stmtBarril);
    }else if(ISSET($_GET['ConsecutivoEdad'])){
      $edad=$_GET["ConsecutivoEdad"];
      $barril = "select IdCodificacion from WM_Barrica where Consecutivo='$edad';";
      $stmtBarril = sqlsrv_query( $conn , $barril);
      if($stmtBarril){
        $row = sqlsrv_fetch_array( $stmtBarril, SQLSRV_FETCH_NUMERIC);
        echo $row[0];
      }else{
        echo $GeneralError;
      }

      sqlsrv_free_stmt($stmtBarril);
    }else if(ISSET($_GET['ConsecutivoLoteB'])){//Regresa información del lote cuando nos dan el concecutivo
      $consecutivo=$_GET["ConsecutivoLoteB"];
      $barril = "select b.IdLoteBarrica,convert(varchar, w.Fecha, 23) as Lote,convert(varchar, l.Recepcion, 23) as Recepcion from  WM_Barrica b
                left join  WM_LoteBarrica w on b.IdLoteBarrica=w.IdLoteBarica
                left join PR_Lote l on l.IdLote=w.IdLote
                left join CM_Alcohol A on l.IdAlcohol=A.IdAlcohol where b.Consecutivo='$consecutivo';";
      $stmtBarril = sqlsrv_query( $conn , $barril);
      if($stmtBarril){
        $result = array();
        do {
            while ($row = sqlsrv_fetch_array($stmtBarril, SQLSRV_FETCH_ASSOC)){
               $result[] = array_map("utf8_encode",$row);
            }
        } while (sqlsrv_next_result($stmtBarril));
        echo json_encode($result);
      }else{
        echo $GeneralError;
      }

      sqlsrv_free_stmt($stmtBarril);
    }else if(ISSET($_GET['fechaLoteB'])){//Obtenemos informacion de los lotes por la fecha
      $IdLoteB=$_GET["fechaLoteB"];
      $barril = "select w.IdLoteBarica,convert(varchar, w.Fecha, 23) as Lote,CONCAT(convert(varchar, l.Recepcion, 23),' ',A.Descripcion) as Recepcion
                  from  WM_LoteBarrica w
                  inner join PR_Lote l on l.IdLote=w.IdLote
                  left join CM_Alcohol A on l.IdAlcohol=A.IdAlcohol where convert(varchar, w.Fecha, 23)='$IdLoteB';";
      $stmtBarril = sqlsrv_query( $conn , $barril);
      if($stmtBarril){
        $result = array();
        do {
            while ($row = sqlsrv_fetch_array($stmtBarril, SQLSRV_FETCH_ASSOC)){
               $result[] = array_map("utf8_encode",$row);
            }
        } while (sqlsrv_next_result($stmtBarril));
        echo json_encode($result);
      }else{
        echo $GeneralError;
      }

      sqlsrv_free_stmt($stmtBarril);
    }else if(ISSET($_GET['fechasLotes'])){//Obtenemos informacion de los lotes por la fecha
      $fecha = "select DISTINCT(convert(varchar, Fecha, 23)) as Fecha from WM_LoteBarrica";
      $stmtFecha = sqlsrv_query( $conn , $fecha);
      if($stmtFecha){
        $result = array();
        do {
            while ($row = sqlsrv_fetch_array($stmtFecha, SQLSRV_FETCH_ASSOC)){
               $result[] = array_map("utf8_encode",$row);
            }
        } while (sqlsrv_next_result($stmtFecha));
        echo json_encode($result);
      }else{
        echo $GeneralError;
      }

      sqlsrv_free_stmt($stmtFecha);
    }
  }else{
    echo '..Error.. No tienes acceso a esta area';
  }

}else{
  echo '..Error.. Acceso no autorizado';
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
