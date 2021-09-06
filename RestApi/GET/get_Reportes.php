<?php
include '../general_connection.php';
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
  }else if(ISSET($_GET['detallesXBarril'])){//Reporte de descripcion
    $almacen=$_GET['almacen'];
    $area=$_GET['area'];
    $seccion=$_GET['seccion'];
    $alcohol=$_GET['alcohol'];
    $codificacion=$_GET['codificacion'];
    $fecha=$_GET['fecha'];
    $datos = "exec sp_InfoBarrilDetalle3 '$almacen','$area','$seccion','$alcohol','$codificacion','$fecha'";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['detallesBarrilesPlantel'])){//Reporte de descripcion_bodegas
    $almacen=$_GET['almacen'];
    $area=$_GET['area'];
    $codificacion=$_GET['codificacion'];
    $datos = "exec sp_BarrilPlantelDetalle '$almacen','$area','$codificacion'";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['almacenes'])){
    $datos = "SELECT AlmacenId,Nombre from AA_Almacen ".($_GET['almacenes']!=="true"?"where AlmacenId=".$_GET['almacenes']:"")." order by Nombre";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['alcohol'])){
    $datos = "SELECT IdAlcohol,Descripcion from CM_Alcohol Where IdAlcohol <> 4 ".($_GET['alcohol']!=="true"?"and IdAlcohol=".$_GET['alcohol']:"")." order by Descripcion";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['Allenada'])){
    $datos = "SELECT Distinct YEAR(Recepcion) as Anio from PR_Lote order by YEAR(Recepcion)";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['uso'])){
    $datos = "SELECT IdCodificacion,Codigo from CM_Codificacion ".($_GET['uso']!=="true"?"where IdCodificacion=".$_GET['uso']:"")." order by Codigo";
    imprimir($datos,$conn);
  }else if(ISSET($_GET['edades'])){
    $datos = "exec sp_ListaEdad";
    imprimir($datos,$conn);
  }

}else{
  echo '..Error.. No tienes acceso a esta area';
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
