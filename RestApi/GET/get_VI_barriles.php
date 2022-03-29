<?php
include '../general_connection.php';
if(strpos($permisos,',2,') !== false){
  if(ISSET($_GET['area'])){
    $area=$_GET["area"];
    $fila = "exec sp_AA_getFilas '$area'";
    imprimir($fila,$conn);
  }else if(ISSET($_GET['plantas'])){
    $bodegas = "SELECT PlantaID,Nombre from AA_Plantas";
    imprimir($bodegas,$conn);
  }else if(ISSET($_GET['bodegas'])){
    $planta=$_GET["bodegas"];
    $bodegas = "SELECT AlmacenId,Nombre from AA_Almacen where PlantaID=$planta order by Consecutivo";
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
    $isSerching=$_GET["isSerching"];
    $tabla = "exec sp_BarrPallet_v2 '$Rack',$isSerching";
    imprimir($tabla,$conn);
  }else if(ISSET($_GET['consecutivo'])){
    $Consecutivo=$_GET["consecutivo"];
    $barril = "exec sp_BarrilUbicacion_v2 '$Consecutivo'";
    imprimir($barril,$conn);
  }else if(ISSET($_GET['consecutivoBus'])){
    $Consecutivo=$_GET["consecutivoBus"];
    $barril = "IF EXISTS (SELECT R.RackLocID from WM_Barrica B inner Join WM_Pallet P on P.Idpallet = B.IdPallet
    inner join WM_RackLoc R on P.RackLocID=R.RackLocID inner Join AA_Nivel N on R.NivelID=N.NivelID Where B.Consecutivo ='$Consecutivo')
    SELECT Pl.PlantaID,Am.AlmacenID,Ar.AreaId,Se.SeccionID,Po.PosicionID,R.RackLocID,P.IdPallet,
    convert(varchar(100),Am.Nombre+', '+ REPLACE(Ar.Nombre, 'COSTADO', 'Cos: ')+', '+REPLACE(Se.Nombre, 'FILA', 'F: ')+','+ REPLACE(Po.Nombre, 'TORRE', 'T: ') +','+ REPLACE(N.Nombre, 'NIVEL', 'N: ')) AS Ubicacion
    from WM_Barrica B inner Join WM_Pallet P on P.Idpallet = B.IdPallet
    inner join WM_RackLoc R on P.RackLocID=R.RackLocID inner Join AA_Nivel N on R.NivelID=N.NivelID
    inner Join AA_Posicion Po on N.PosicionId=Po.PosicionID inner Join AA_Seccion Se on Po.SeccionID=Se.SeccionID
    inner Join AA_Area Ar on Se.AreaId = Ar.AreaId inner Join AA_Almacen Am on Ar.AlmacenId=Am.AlmacenID
    inner join AA_Plantas Pl on Pl.PlantaID=Am.PlantaID Where B.Consecutivo ='$Consecutivo'
    else select IdPallet,'Sin ubicación' as Ubicacion from WM_Barrica where Consecutivo='$Consecutivo'";
    imprimir($barril,$conn);
  }else if(ISSET($_GET['tapa'])){
    $tapa=$_GET["tapa"];
    $year=$_GET["year"];
    $barril = "SELECT COUNT(*) from  WM_Barrica b
              left join  WM_LoteBarrica w on b.IdLoteBarrica=w.IdLoteBarica
              left join PR_Lote l on l.IdLote=w.IdLote
              left join CM_Alcohol A on l.IdAlcohol=A.IdAlcohol where b.NoTapa=$tapa and Datepart(YYYY, w.Fecha)='$year';";
    if(ObtenerCantidad($barril,$conn)!=0){
      echo '..Error.. Este número de tapa ya esta ocupado por otro barril';
    }
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
    $barril = "SELECT w.IdLoteBarica,convert(varchar, w.Fecha, 23) as Lote,convert(varchar, l.Recepcion, 23)+' '+A.Descripcion as Recepcion
                from  WM_LoteBarrica w
                inner join PR_Lote l on l.IdLote=w.IdLote
                left join CM_Alcohol A on l.IdAlcohol=A.IdAlcohol where convert(varchar, w.Fecha, 23)='$IdLoteB';";
    imprimir($barril,$conn);
  }else if(ISSET($_GET['fechasLotes'])){//Obtenemos todas las fechas de los lotes
    $fecha = "SELECT DISTINCT(convert(varchar, Fecha, 23)) as Fecha from WM_LoteBarrica";
    imprimir($fecha,$conn);
  }else if(ISSET($_GET['razones'])){
    $razones = "SELECT IdRazon,Descripcion from ADM_Razones where IdCaso=".$_GET['razones'];
    imprimir($razones,$conn);
  }else if(ISSET($_GET['lugaresDisRack'])){//Obtiene los lugares disponibles de un Nivel
    $rack=$_GET['lugaresDisRack'];
    $razones = "SELECT 9-((select	count(*) from WM_Barrica B inner Join WM_Pallet P on P.Idpallet = B.IdPallet Where P.RackLocId=$rack)+(select	count(*)*9 from WM_Tanques T inner Join WM_Pallet P on P.Idpallet = T.IdPallet Where P.RackLocId =$rack )) as Dispon";
    imprimir($razones,$conn);
  }
}else{
  echo '..Error.. No tienes acceso a esta area';
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
