<?php
include '../general_connection.php';
if(strpos($permisos,',6,') !== false){
  if(ISSET($_GET['lotes'])){
    $lotes = "SELECT A.AlmacenID ,A.Nombre as Bod,AA.AreaId ,AA.Nombre as Costado,S.SeccionID,C.IdCodificacion,C.Codigo as Barril
    ,ISNULL(( select COUNT(IdBarrica) from V_BarrilesNoId Where AlmacenID = A.AlmacenID and AreaId = AA.AreaId and Barril = C.Codigo and IdEdad = 1 and AreaId <> 45 ),0) as A
    ,ISNULL(( select COUNT(IdBarrica) from V_BarrilesNoId Where AlmacenID = A.AlmacenID and AreaId = AA.AreaId and Barril = C.Codigo and IdEdad = 2 and AreaId <> 45 ),0) as B
    ,ISNULL(( select COUNT(IdBarrica) from V_BarrilesNoId Where AlmacenID = A.AlmacenID and AreaId = AA.AreaId and Barril = C.Codigo and IdEdad = 3 and AreaId <> 45 ),0) as C
    ,ISNULL(( select COUNT(IdBarrica) from V_BarrilesNoId Where AlmacenID = A.AlmacenID and AreaId = AA.AreaId and Barril = C.Codigo and IdEdad = 4 and AreaId <> 45 ),0) as D
    ,ISNULL(( select COUNT(IdBarrica) from V_BarrilesNoId Where AlmacenID = A.AlmacenID and AreaId = AA.AreaId and Barril = C.Codigo and IdEdad = 5 and AreaId <> 45 ),0) as RC
    ,ISNULL(( select COUNT(IdBarrica) from V_BarrilesNoId Where AlmacenID = A.AlmacenID and AreaId = AA.AreaId and Barril = C.Codigo and IdEdad = 6 and AreaId <> 45 ),0) as ED
    ,ISNULL(( select COUNT(IdBarrica) from V_BarrilesNoId Where AlmacenID = A.AlmacenID and AreaId = AA.AreaId and Barril = C.Codigo and IdEdad = 7 and AreaId <> 45 ),0) as E
    ,ISNULL(( select COUNT(IdBarrica) from V_BarrilesNoId Where AlmacenID = A.AlmacenID and AreaId = AA.AreaId and Barril = C.Codigo and IdEdad = 8 and AreaId <> 45 ),0) as V
    ,ISNULL(( select COUNT(IdBarrica) from V_BarrilesNoId Where AlmacenID = A.AlmacenID and AreaId = AA.AreaId and Barril = C.Codigo and IdEdad = 9 and AreaId <> 45 ),0) as F
    ,ISNULL(( select COUNT(IdBarrica) from V_BarrilesNoId Where AlmacenID = A.AlmacenID and AreaId = AA.AreaId and Barril = C.Codigo and IdEdad = 10 and AreaId <> 45 ),0) as RF
    ,ISNULL(( select COUNT(IdBarrica) from V_BarrilesNoId Where AlmacenID = A.AlmacenID and AreaId = AA.AreaId and Barril = C.Codigo and IdEdad = 0 and AreaId <> 45 ),0) as '0'
    ,ISNULL(( select Count(IdBarrica) from V_BarrilesNoId Where AlmacenID = A.AlmacenID and AreaId = AA.AreaId and Barril = C.Codigo and AreaId <> 45 ),0) as Total
    from AA_Almacen A inner Join AA_Area AA on AA.AlmacenId = A.AlmacenID inner Join AA_Seccion S on S.AreaId = AA.AreaId
    inner join AA_Posicion P on P.SeccionID = S.SeccionID inner Join AA_Nivel N on N.PosicionId = P.PosicionID
    inner Join WM_RackLoc RL on RL.NivelID = n.NivelID inner Join WM_Pallet Pa on Pa.RackLocId = RL.RackLocID
    inner Join WM_Barrica B on B.IdPallet = Pa.IdPallet Left join CM_CodEdad CE on CE.IdCodEdad= B.IdCodificacion
    LEft join CM_Codificacion C on C.IdCodificacion = CE.IdCodificicacion Left Join CM_Edad E on E.IdEdad = CE.IdEdad
    Where A.AlmacenID = 13 and AA.AreaId <> 45 Group by A.AlmacenID ,A.Nombre,AA.AreaId ,AA.Nombre,S.SeccionID,C.IdCodificacion,C.Codigo order by AA.AreaId,C.IdCodificacion,C.Codigo";
    imprimir($lotes,$conn);
  }
}else{
  echo '..Error.. No tienes acceso a esta area';
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
