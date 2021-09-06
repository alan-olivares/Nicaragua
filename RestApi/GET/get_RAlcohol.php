<?php
include '../general_connection.php';
if(strpos($permisos,',8,') !== false){
  if(ISSET($_GET['tanques'])){//Tanques en existencia
    $barriles = "SELECT T.Codigo,T.Descripcion,T.Capacidad,T.Tipo,TD.A,TD.B,TD.C,TD.D from CM_Tanque T
    left join CM_TanqDetalle TD on T.IDTanque=TD.IdTanque ".($_GET['tanques']!=="true"?"where T.Tipo=".$_GET['tanques']:"");
    imprimir($barriles,$conn);
  }else if(ISSET($_GET['planteles'])){//Planteles existentes
    $fecha = "SELECT Ar.IdArea, A.Nombre
    from WM_RackLoc RL inner Join AA_Nivel N on N.NivelID = RL.NivelID
    Inner Join AA_Posicion P on P.PosicionID = N.PosicionId inner Join AA_Seccion S on S.SeccionID = P.SeccionID
    inner Join AA_Area A on A.AreaId = S.AreaId inner Join AA_Almacen AA on AA.AlmacenID = A.AlmacenId
    inner Join CM_Areas Ar on Ar.RackLocId = RL.RackLocID";
    imprimir($fecha,$conn);
  }else if(ISSET($_GET['codificacion'])){//Codificación de los barriles
    $ordenes = "SELECT CE.IdCodEdad, (C.Codigo + ' - ' + E.Codigo + ' - ' + E.Descripcion ) as CodiEdad
    From CM_CodEdad CE inner Join CM_Codificacion C on C.IdCodificacion = CE.IdCodificicacion inner Join CM_Edad E on E.IdEdad = CE.IdEdad";
    imprimir($ordenes,$conn);
  }else if(ISSET($_GET['proveedor'])){
    $ordenes = "SELECT  IdProveedor,Descripcion FROM CM_Proveedor";
    imprimir($ordenes,$conn);
  }else if(ISSET($_GET['alcohol'])){//alcoholes en existencia
    $barriles = "SELECT * from CM_Alcohol ".($_GET['alcohol']!=="true"?"where IdAlcohol=".$_GET['alcohol']:"");
    imprimir($barriles,$conn);
  }else if(ISSET($_GET['envios'])){//Envios existentes
    $barriles = "SELECT Re.IdRecDetail, T.Descripcion as tanque,Re.Litros,Re.Medida,A.Codigo as CodAlcohol,A.Descripcion as alcohol,Re.Estatus from WM_Recepcion R
    left join WM_RecDetail Re on R.IdRecepcion=Re.IdRecepcion
    left join CM_Tanque T on Re.IdTanque=T.IDTanque
    left join CM_Alcohol A on A.IdAlcohol = Re.IdAlcohol where R.EnvioNo='".$_GET['envios']."'";
    imprimir($barriles,$conn);
  }else if(ISSET($_GET['checkTanque'])){
    $tanque=$_GET['checkTanque'];
    $tanques = "SELECT R.EnvioNo, A.* from WM_Recepcion R left join WM_RecDetail Re on R.IdRecepcion=Re.IdRecepcion
    left join CM_Alcohol A on A.IdAlcohol = Re.IdAlcohol
    where Re.IdTanque = (select IDTanque from CM_Tanque where Codigo='$tanque') and Re.Estatus = 0";
    imprimir($tanques,$conn);
  }else if(ISSET($_GET['historico'])){
    $tanques = "SELECT R.EnvioNo,R.Para,REPLACE(CONVERT(varchar, R.Fecha, 102),'.','-') as Creación,R.AñoAlcohol as 'Año Alcohol',
    T.Descripcion as Tanque,Re.Litros,cast(Re.Consumo as decimal(10,3)) as Cosumo,cast(Re.Merma as decimal(10,3)) as Merma,A.Descripcion as Alcohol,
    REPLACE(CONVERT(varchar, L.Recepcion, 102),'.','-') as 'Fecha Lote',case when Re.Estatus=0 then 'Activo' else 'Terminada' end as Estatus
    from WM_Recepcion R left join WM_RecDetail Re on R.IdRecepcion=Re.IdRecepcion left join CM_Tanque T on Re.IdTanque=T.IDTanque
    left join CM_Alcohol A on Re.IdAlcohol=A.IdAlcohol left join PR_Lote L on Re.IdLote=L.IdLote order by ABS(REPLACE(R.EnvioNo,'-','.')) ";
    imprimir($tanques,$conn);
  }
}else{
  echo '..Error.. No tienes acceso a esta area';
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
