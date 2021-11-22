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
    $tabla = "SELECT T.NoSerie,T.Capacidad,T.Litros,CONVERT(varchar(10),T.FechaLLenado, 120) as 'Fecha de llenado', Es.Descripcion as Estado
    from WM_Tanques T inner Join WM_Pallet P on P.Idpallet = T.IdPallet left Join CM_Estado ES on Es.IdEstado = T.IdEstado Where P.RackLocId ='$Rack'";
    imprimir($tabla,$conn);
  }else if(ISSET($_GET['id'])){
    $NoSerie=$_GET["id"];
    $barril = "SELECT T.NoSerie,T.Capacidad,T.Litros,CONVERT(varchar(10),T.FechaLLenado, 120) as llenado, Es.Descripcion as Estado,
    CASE WHEN Am.Nombre is null THEN 'Tanque sin ubicación' ELSE CONCAT(Am.Nombre,', ', REPLACE(Ar.Nombre, 'COSTADO', 'Cos: '),', ',REPLACE(Se.Nombre, 'FILA', 'F: '),',', REPLACE(Po.Nombre, 'TORRE', 'T: ') ,',', REPLACE(N.Nombre, 'NIVEL', 'N: ')) END AS Ubicacion
    from WM_Tanques T left Join WM_Pallet P on P.Idpallet = T.IdPallet left Join CM_Estado ES on Es.IdEstado = T.IdEstado
    left join WM_RackLoc R on P.RackLocID=R.RackLocID left Join AA_Nivel N on R.NivelID=N.NivelID
    left Join AA_Posicion Po on N.PosicionId=Po.PosicionID left Join AA_Seccion Se on Po.SeccionID=Se.SeccionID left Join AA_Area Ar on Se.AreaId = Ar.AreaId
    left Join AA_Almacen Am on Ar.AlmacenId=Am.AlmacenID where T.NoSerie='$NoSerie'";
    imprimir($barril,$conn);

  }else if(ISSET($_GET['razones'])){
    $razones = "SELECT IdRazon,Descripcion from ADM_Razones where IdCaso=".$_GET['razones'];
    imprimir($razones,$conn);
  }
}else{
  echo '..Error.. No tienes acceso a esta area';
}
sqlsrv_close($conn);
?>
