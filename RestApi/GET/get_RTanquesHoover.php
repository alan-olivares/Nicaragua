<?php
include '../general_connection.php';
if(strpos($permisos,',8,') !== false){
  if(ISSET($_GET['tanques'])){//Ultimo consecutivo e impresión
    $barriles = "SELECT T.NoSerie as 'N° de serie',
    CASE WHEN Am.Nombre is null THEN 'Tanque sin ubicación' ELSE Am.Nombre+', '+ REPLACE(Ar.Nombre, 'COSTADO', 'Cos: ')+', '+REPLACE(Se.Nombre, 'FILA', 'F: ')+','+ REPLACE(Po.Nombre, 'TORRE', 'T: ') +','+ REPLACE(N.Nombre, 'NIVEL', 'N: ') END AS Ubicación,
    T.Capacidad,T.Litros,Convert(varchar,T.FechaLLenado,105) as 'Fecha de llenado',Convert(varchar,T.FechaRecepcion,105) as 'Fecha de recepción',E.Descripcion as Estado
    from WM_Tanques T left join CM_Estado E on E.IdEstado=T.IdEstado left Join WM_Pallet P on P.Idpallet = T.IdPallet
    left join WM_RackLoc R on P.RackLocID=R.RackLocID left Join AA_Nivel N on R.NivelID=N.NivelID
    left Join AA_Posicion Po on N.PosicionId=Po.PosicionID left Join AA_Seccion Se on Po.SeccionID=Se.SeccionID
    left Join AA_Area Ar on Se.AreaId = Ar.AreaId left Join AA_Almacen Am on Ar.AlmacenId=Am.AlmacenID";
    imprimir($barriles,$conn);
  }else if(ISSET($_GET['planteles'])){//Planteles existentes
    $fecha = "SELECT Ar.IdArea, A.Nombre
    from WM_RackLoc RL inner Join AA_Nivel N on N.NivelID = RL.NivelID
    Inner Join AA_Posicion P on P.PosicionID = N.PosicionId inner Join AA_Seccion S on S.SeccionID = P.SeccionID
    inner Join AA_Area A on A.AreaId = S.AreaId inner Join AA_Almacen AA on AA.AlmacenID = A.AlmacenId
    inner Join CM_Areas Ar on Ar.RackLocId = RL.RackLocID";
    imprimir($fecha,$conn);
  }else if(ISSET($_GET['proveedor'])){
    $ordenes = "SELECT  IdProveedor,Descripcion FROM CM_Proveedor";
    imprimir($ordenes,$conn);
  }
}else{
  echo '..Error.. No tienes acceso a esta area';
}
sqlsrv_close($conn);
?>
