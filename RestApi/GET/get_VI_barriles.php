<?php
include '../general_connection.php';
if(strpos($permisos,',2,') !== false){
  if(ISSET($_GET['area'])){
    $area=$_GET["area"];
    $fila = "exec sp_AA_getFilas '$area'";
    imprimir($fila,$conn);
  }else if(ISSET($_GET['plantas'])){
    $bodegas = "SELECT * from AA_Plantas";
    imprimir($bodegas,$conn);
  }else if(ISSET($_GET['bodegas'])){
    $planta=$_GET["bodegas"];
    $bodegas = "SELECT AlmacenId,Nombre from AA_Almacen where PlantaID=$planta";
    imprimir($bodegas,$conn);
  }else if(ISSET($_GET['bodega'])){
    $bodega=$_GET["bodega"];
    $costados = "exec sp_AA_getCostados '$bodega'";
    imprimir($costados,$conn);
  }else if(ISSET($_GET['fila'])){
    $fila=$_GET["fila"];
    $torres = "exec sp_AA_getTorres '$fila'";
    imprimir($torres,$conn);
  }else if(ISSET($_GET['torre'])){
    $torre=$_GET["torre"];
    $niveles = "exec sp_AA_getNiveles '$torre'";
    imprimir($niveles,$conn);
  }else if(ISSET($_GET['Rack'])){
    //Nos regresa la tabla en forma de json
    $Rack=$_GET["Rack"];
    $tabla = "exec sp_BarrPallet '$Rack'";
    imprimir($tabla,$conn);
  }else if(ISSET($_GET['consecutivo'])){
    $Consecutivo=$_GET["consecutivo"];
    $barril = "exec sp_BarrilUbicacion '$Consecutivo'";
    imprimir($barril,$conn);

  }else if(ISSET($_GET['tapa'])){
    $tapa=$_GET["tapa"];
    $year=$_GET["year"];
    $barril = "SELECT COUNT(*) from  WM_Barrica b
              left join  WM_LoteBarrica w on b.IdLoteBarrica=w.IdLoteBarica
              left join PR_Lote l on l.IdLote=w.IdLote
              left join CM_Alcohol A on l.IdAlcohol=A.IdAlcohol where b.NoTapa=$tapa and Datepart(YYYY, w.Fecha)='$year';";
    imprimir($barril,$conn);
  }else if(ISSET($_GET['loteA'])){
    $loteA=$_GET["loteA"];
    $barril = "SELECT COUNT(*) from WM_LoteBarrica where IdLoteBarica='$loteA';";
    imprimir($barril,$conn);

  }else if(ISSET($_GET['ConsecutivoEdad'])){
    $edad=$_GET["ConsecutivoEdad"];
    $barril = "SELECT IdCodificacion from WM_Barrica where Consecutivo='$edad';";
    imprimir($barril,$conn);
  }else if(ISSET($_GET['ConsecutivoLoteB'])){//Regresa información del lote cuando nos dan el concecutivo
    $consecutivo=$_GET["ConsecutivoLoteB"];
    $barril = "SELECT b.IdLoteBarrica,convert(varchar, w.Fecha, 23) as Lote,convert(varchar, l.Recepcion, 23) as Recepcion from  WM_Barrica b
              left join  WM_LoteBarrica w on b.IdLoteBarrica=w.IdLoteBarica
              left join PR_Lote l on l.IdLote=w.IdLote
              left join CM_Alcohol A on l.IdAlcohol=A.IdAlcohol where b.Consecutivo='$consecutivo';";
    imprimir($barril,$conn);
  }else if(ISSET($_GET['fechaLoteB'])){//Obtenemos informacion de los lotes por la fecha
    $IdLoteB=$_GET["fechaLoteB"];
    $barril = "SELECT w.IdLoteBarica,convert(varchar, w.Fecha, 23) as Lote,CONCAT(convert(varchar, l.Recepcion, 23),' ',A.Descripcion) as Recepcion
                from  WM_LoteBarrica w
                inner join PR_Lote l on l.IdLote=w.IdLote
                left join CM_Alcohol A on l.IdAlcohol=A.IdAlcohol where convert(varchar, w.Fecha, 23)='$IdLoteB';";
    imprimir($barril,$conn);
  }else if(ISSET($_GET['fechasLotes'])){//Obtenemos todas las fechas de los lotes
    $fecha = "SELECT DISTINCT(convert(varchar, Fecha, 23)) as Fecha from WM_LoteBarrica";
    imprimir($fecha,$conn);
  }else if(ISSET($_GET['razones'])){
    $razones = "SELECT * from ADM_Razones where IdCaso=".$_GET['razones'];
    imprimir($razones,$conn);
  }
}else{
  echo '..Error.. No tienes acceso a esta area';
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
