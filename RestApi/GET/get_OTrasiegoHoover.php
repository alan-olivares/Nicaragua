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
    if(ISSET($_GET['lotes'])){
      $lotes = "exec sp_LoteTrasiego";
      imprimir($lotes,$conn);

    }else if(ISSET($_GET['fechasLotes'])){//Obtenemos informacion de las ordenes por la fecha
      $fecha = "SELECT DISTINCT(convert(varchar, Fecha, 23)) as Fecha from PR_Orden where IdTipoOp=7 order by Fecha";
      imprimir($fecha,$conn);
    }else if(ISSET($_GET['fechaOrdenes'])){//Obtenemos las ordenes dadas por una fecha en especifico
      $fecha=$_GET['fechaOrdenes'];
      $ordenes = "SELECT Distinct	O.IdOrden,AL.AlmacenID,Al.Nombre as Bodega,isnull((Select Top 1 Cantidad from PR_Op Where IdOrden = O.IdOrden),0) as CantBarriles,
      isnull((Select Case when (CAST(Sum(Cantidad) as int)%9)>0 then (CAST(Sum(Cantidad) as int)/9)+1 else (Sum(Cantidad)/9) end from PR_Op Where IdOrden = O.IdOrden),0) as Paletas,
      A.Descripcion as Alcohol,T.NoSerie as Tanque,OP.Descripcion,O.idOperario ,isnull(U1.Nombre,'') as Op,O.IdOperarioMon,isnull(U2.Nombre,'') as OpeMonta,
      O.IdSupervisor,isnull(U3.Nombre,'') as Supervidor,Case
      when (O.idOperario = 0 ) or (O.IdOperarioMon = 0) or (o.Estatus = 0) then 'Por Asignar'
      when (O.idOperario <> 0) and (O.IdOperarioMon <> 0) and (o.Estatus = 0) then 'Por Iniciar'
      when (O.idOperario <> 0) and (O.IdOperarioMon <> 0) and (o.Estatus = 1) then 'Asignada'
      when (O.idOperario <> 0) and (O.IdOperarioMon <> 0) and (o.Estatus = 2) then 'Iniciada'
      when (O.idOperario <> 0) and (O.IdOperarioMon <> 0) and (o.Estatus = 3) then 'Terminada'
      when (O.idOperario = 0) and (O.IdOperarioMon = 0) and (o.Estatus = 3) then 'Cancelada'
      when (O.idOperario <> 0) and (O.IdOperarioMon <> 0) and (o.Estatus = 4) then 'Terminada Inconpleta'
      when (O.idOperario <> 0) and (O.IdOperarioMon <> 0) and (o.Estatus = 5) then 'Detenida' end as Estatus ,COALESCE(CONVERT(VARCHAR(255), O.Fecha), ''), O.Estatus as IdEstatus,O.IdUsuario
      from PR_Orden O inner join CM_TipoOp OP on OP.IdTipoOP = O.IdTipoOp LEFT join PR_Lote L on L.IdLote = O.IdLote
      Left Join CM_Recurso R on L.IdRecurso = R.IdRecurso inner Join AA_Almacen Al on Al.AlmacenID = O.IdAlmacen
      left Join AA_Area Ar on Ar.AlmacenId = Al.AlmacenID and Ar.AreaId = o.idArea left Join AA_Seccion S on S.AreaId = Ar.AreaId and s.SeccionID = o.IdSeccion
      Left Join CM_Usuario U1 on U1.idUsuario = O.idOperario Left Join CM_Usuario U2 on U2.idUsuario = O.IdOperarioMon Left Join CM_Usuario U3 on U3.idUsuario = O.IdSupervisor
      inner Join PR_Op OPe on OPe.IdOrden = O.IdOrden inner Join WM_Tanques T on T.IDTanque = Ope.IdTanque Left Join CM_Alcohol A on A.IdAlcohol = Ope.IdAlcohol--L.IdAlcohol
      where O.IdTipoOP=7 and convert(varchar(10), O.Fecha,120) like '$fecha' order by O.IdOrden";
      imprimir($ordenes,$conn);

    }else if(ISSET($_GET['operador'])){//Operadores dado a un grupo
      $operador=$_GET['operador'];
      $usuarios = "SELECT Nombre,IdUsuario as Id from CM_Usuario where IdGrupo=$operador";
      imprimir($usuarios,$conn);

    }else if(ISSET($_GET['tanques'])){//Operadores dado a un grupo
      $usuarios = "SELECT * from WM_Tanques";
      imprimir($usuarios,$conn);
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
    echo '..Error.. Hubo un error al obtener los datos, intenta de nuevo más tarde '.$query;
  }
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
