<?php
//$usuario=ISSET($_GET['usuario'])?$_GET['usuario']:"null";
//$pass=ISSET($_GET['pass'])?$_GET['pass']:"null";
include '../general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){
  include '../revisar_permisos.php';
  if(strpos($permisos,',10,') !== false){
    if(ISSET($_GET['gerencia'])){// Reporte de Gerencia
      $datos = "exec sp_RepGerencia";
      imprimir($datos,$conn);
    }else if(ISSET($_GET['inventarioDeta'])){
      $bodegas=$_GET['bodega'];
      $Alcohol=$_GET['alcohol'];
      $Allenada=$_GET['llenada'];
      $Uso=$_GET['uso'];
      $datos = "exec sp_InvParamDetalle '$bodegas','$Alcohol','$Allenada','$Uso'";
      imprimir($datos,$conn);
    }else if(ISSET($_GET['inventario'])){
      $bodegas=$_GET['bodega'];
      $Alcohol=$_GET['alcohol'];
      $Allenada=$_GET['llenada'];
      $Uso=$_GET['uso'];
      $datos = "exec sp_InvParam '$bodegas','$Alcohol','$Allenada','$Uso'";
      imprimir($datos,$conn);
    }else if(ISSET($_GET['almacenes'])){
      $datos = "SELECT AlmacenId,Nombre from AA_Almacen order by Nombre";
      imprimir($datos,$conn);
    }else if(ISSET($_GET['alcohol'])){
      $datos = "SELECT IdAlcohol,Descripcion from CM_Alcohol Where IdAlcohol <> 4 order by Descripcion";
      imprimir($datos,$conn);
    }else if(ISSET($_GET['Allenada'])){
      $datos = "SELECT Distinct YEAR(Recepcion) as Anio from PR_Lote order by YEAR(Recepcion)";
      imprimir($datos,$conn);
    }else if(ISSET($_GET['uso'])){
      $datos = "SELECT IdCodificacion,Codigo from CM_Codificacion order by Codigo";
      imprimir($datos,$conn);
    }else if(ISSET($_GET['edades'])){
      $datos = "exec sp_ListaEdad";
      imprimir($datos,$conn);
    }else if(ISSET($_GET['barriles_plantel'])){//Reporte de barriles de plantel
      $datos = "exec sp_BarrilPlantel";
      imprimir($datos,$conn);
    }else if(ISSET($_GET['llenados'])){//Reporte de barriles de plantel
      $fecha1=$_GET['fecha1'];
      $fecha2=$_GET['fecha2'];
      $datos = "exec sp_RepOPDetalleLlenEncWeb '$fecha1' , '$fecha2'";
      imprimir($datos,$conn);
    }else if(ISSET($_GET['llenadosT2'])){//Reporte de barriles de plantel
      $fecha1=$_GET['fecha1'];
      $fecha2=$_GET['fecha2'];
      $datos = "exec sp_RepOPDetalleLlenWeb_v2 '$fecha1' , '$fecha2'";
      imprimir($datos,$conn);
    }else if(ISSET($_GET['rellenados'])){//Reporte de barriles de plantel
      $fecha1=$_GET['fecha1'];
      $fecha2=$_GET['fecha2'];
      $datos = "exec sp_RepOPRellenoEncWeb '$fecha1' , '$fecha2'";
      imprimir($datos,$conn);
    }else if(ISSET($_GET['rellenadosT2'])){//Reporte de barriles de plantel
      $fecha1=$_GET['fecha1'];
      $fecha2=$_GET['fecha2'];
      $datos = "exec sp_RepOPRellenoWeb '$fecha1' , '$fecha2'";
      imprimir($datos,$conn);
    }else if(ISSET($_GET['trasiego'])){//Reporte de barriles de plantel
      $fecha1=$_GET['fecha1'];
      $fecha2=$_GET['fecha2'];
      $datos = "exec sp_RepOPDetalleEncWeb 5, '$fecha1' , '$fecha2'";
      imprimir($datos,$conn);
    }else if(ISSET($_GET['trasiegoT2'])){//Reporte de barriles de plantel
      $fecha1=$_GET['fecha1'];
      $fecha2=$_GET['fecha2'];
      $datos = "exec sp_RepOPDetalleWeb_v2 5, '$fecha1' , '$fecha2'";
      imprimir($datos,$conn);
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
