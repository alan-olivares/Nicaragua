<?php
include '../general_connection.php';
if(strpos($permisos,',11,') !== false){
  $fecha=$_GET["fecha"];
  if(ISSET($_GET['date'])){
    $orgDate = str_replace('/', '-', $fecha);
    $date1 = date("Y/m/d", strtotime($orgDate));
    $hora1= $date1." 00:00";
    $hora2= $date1." 23:59";
    if(ISSET($_GET['total'])){
      echo obtenerTotales($conn,$hora1,$hora2);
    }else{
      echo obtenerTiempos($conn,"SUBSTRING(CONVERT(CHAR(13), l.fecha, 120), 12, 2)",$hora1,$hora2,null);
    }
  }else if(ISSET($_GET['week'])){
    $date1 = date("Y-m-d", strtotime($fecha));
    $date2 = date('Y-m-d', strtotime($date1. ' + 6 days'));
    $hora1= $date1." 00:00";
    $hora2= $date2." 23:59";
    if(ISSET($_GET['total'])){
      echo obtenerTotales($conn,$hora1,$hora2);
    }else{
      $arrayDias = [
        (int)date('d', strtotime($date1)) => 1,
        (int)date('d', strtotime($date1. ' + 1 days')) => 2,
        (int)date('d', strtotime($date1. ' + 2 days')) => 3,
        (int)date('d', strtotime($date1. ' + 3 days')) => 4,
        (int)date('d', strtotime($date1. ' + 4 days')) => 5,
      ];
      echo obtenerTiempos($conn,"SUBSTRING(CONVERT(CHAR(10), L.fecha, 120),  9, 2)",$hora1,$hora2,$arrayDias);
    }
  }else if(ISSET($_GET['month'])){
    $date1 = date("Y-m", strtotime($fecha));
    $lastDay=cal_days_in_month(CAL_GREGORIAN, (int)date("m", strtotime($fecha)), (int)date("Y", strtotime($fecha)));
    $hora1= $date1."-01 00:00";
    $hora2= $date1."-".$lastDay." 23:59";
    if(ISSET($_GET['total'])){
      echo obtenerTotales($conn,$hora1,$hora2);
    }else{
      echo obtenerTiempos($conn,"SUBSTRING(CONVERT(CHAR(10), l.fecha, 120),  9, 2)",$hora1,$hora2,null);
    }
  }

}else{
  echo '..Error.. No tienes acceso a esta area';
}
sqlsrv_close($conn);

function obtenerTiempos($conn,$campo,$hora1,$hora2,$arrayDias){
  $llenados = "SELECT ".$campo.", count(distinct idbarrica) as Barriles
  from adm_logregbarril l inner join PR_RegBarril r on r.idregbarril=l.IdregBarril
  where l.fecha between '$hora1' and '$hora2' and r.TipoReg=1
  group by ".$campo." order by ".$campo;
  $rellenados ="SELECT ".$campo.", count(distinct idbarrica) as Barriles
  from adm_logregbarril l inner join PR_RegBarril r on r.idregbarril=l.IdregBarril
  where l.fecha between '$hora1' and '$hora2' and r.TipoReg in (2,4,5)
  group by ".$campo." order by ".$campo;
  $trasiegos="SELECT ".$campo.", count(distinct idbarrica) as Barriles
  from adm_logregbarril l inner join PR_RegBarril r on r.idregbarril=l.IdregBarril
  where l.fecha between '$hora1' and '$hora2' and r.TipoReg=3
  group by ".$campo." order by ".$campo;
  $reparados="SELECT ".$campo.", count(distinct M.idbarrica) as Barriles
  FROM PR_Mantenimiento M INNER JOIN ADM_logMantenimiento L ON M.IdMantenimiento=L.IdMantenimiento
  WHERE L.TipoOp='I' AND M.Fecha between '$hora1' and '$hora2'
  group by ".$campo." order by ".$campo;
  $hoover="SELECT ".$campo.", count(distinct IdTanque) as Tanques
  FROM WM_OperacionTQH l  WHERE fecha between '$hora1' and '$hora2'
  group by ".$campo." order by ".$campo;
  $salida="[{ \"label\": \"Barriles llenados\", \"data\": [".obtenerTiempoIndv($conn,$llenados,$arrayDias)."] },".
  "{ \"label\": \"Barriles rellenados\", \"data\": [".obtenerTiempoIndv($conn,$rellenados,$arrayDias)."] },".
  "{ \"label\": \"Barriles trasegados\", \"data\": [".obtenerTiempoIndv($conn,$trasiegos,$arrayDias)."] },".
  "{ \"label\": \"Barriles reparados\", \"data\": [".obtenerTiempoIndv($conn,$reparados,$arrayDias)."] },".
  "{ \"label\": \"Tanques Hoover llenados\", \"data\": [".obtenerTiempoIndv($conn,$hoover,$arrayDias)."] }]";
  return $salida;
}
function obtenerTotales($conn,$hora1,$hora2){
  $llenados="SELECT count(distinct idbarrica)
  from adm_logregbarril l inner join PR_RegBarril r on r.idregbarril=l.IdregBarril
  where l.fecha between '$hora1' and '$hora2' and r.TipoReg=1";
  $rellenados="SELECT count(distinct idbarrica)
  from adm_logregbarril l inner join PR_RegBarril r on r.idregbarril=l.IdregBarril
  where l.fecha between '$hora1' and '$hora2' and r.TipoReg in (2,4,5)";
  $trasiegos="SELECT count(distinct idbarrica)
  from adm_logregbarril l inner join PR_RegBarril r on r.idregbarril=l.IdregBarril
  where l.fecha between '$hora1' and '$hora2' and r.TipoReg=3";
  $reparados="SELECT count(distinct M.idbarrica)
  FROM PR_Mantenimiento M INNER JOIN ADM_logMantenimiento L ON M.IdMantenimiento=L.IdMantenimiento
  WHERE L.TipoOp='I' AND M.Fecha between '$hora1' and '$hora2'";
  $hoover="SELECT count(distinct IdTanque)
  FROM WM_OperacionTQH WHERE  fecha between '$hora1' and '$hora2'";
  $salida="[{\"llenados\":".ObtenerCantidad($llenados,$conn)." ,\"rellenados\": ".ObtenerCantidad($rellenados,$conn).",".
  "\"trasegados\":".ObtenerCantidad($trasiegos,$conn).",\"reparados\": ".ObtenerCantidad($reparados,$conn).",".
  "\"Hoover\": ".ObtenerCantidad($hoover,$conn)." }]";
  return $salida;
}
function obtenerTiempos2($conn,$campo,$hora1,$hora2,$arrayDias){
  $llenados = "SELECT ".$campo.", count(distinct idbarrica) as Barriles
  from PR_Orden l inner join PR_RegBarril r on r.IdOrden=l.IdOrden
  where l.fecha between '$hora1' and '$hora2' and r.TipoReg=1
  group by ".$campo." order by ".$campo;
  $rellenados ="SELECT ".$campo.", count(distinct idbarrica) as Barriles
  from PR_Orden l inner join PR_RegBarril r on r.IdOrden=l.IdOrden
  where l.fecha between '$hora1' and '$hora2' and r.TipoReg in (2,4,5)
  group by ".$campo." order by ".$campo;
  $trasiegos="SELECT ".$campo.", count(distinct idbarrica) as Barriles
  from PR_Orden l inner join PR_RegBarril r on r.IdOrden=l.IdOrden
  where l.fecha between '$hora1' and '$hora2' and r.TipoReg=3
  group by ".$campo." order by ".$campo;
  $reparados="SELECT ".$campo.", count(distinct M.idbarrica) as Barriles
  FROM PR_Mantenimiento M INNER JOIN ADM_logMantenimiento L ON M.IdMantenimiento=L.IdMantenimiento
  WHERE L.TipoOp='I' AND M.Fecha between '$hora1' and '$hora2'
  group by ".$campo." order by ".$campo;
  $hoover="SELECT ".$campo.", count(distinct IdTanque) as Tanques
  FROM WM_OperacionTQH l  WHERE fecha between '$hora1' and '$hora2'
  group by ".$campo." order by ".$campo;
  $salida="[{ \"label\": \"Barriles llenados\", \"data\": [".obtenerTiempoIndv($conn,$llenados,$arrayDias)."] },".
  "{ \"label\": \"Barriles rellenados\", \"data\": [".obtenerTiempoIndv($conn,$rellenados,$arrayDias)."] },".
  "{ \"label\": \"Barriles trasegados\", \"data\": [".obtenerTiempoIndv($conn,$trasiegos,$arrayDias)."] },".
  "{ \"label\": \"Barriles reparados\", \"data\": [".obtenerTiempoIndv($conn,$reparados,$arrayDias)."] },".
  "{ \"label\": \"Tanques Hoover llenados\", \"data\": [".obtenerTiempoIndv($conn,$hoover,$arrayDias)."] }]";
  return $salida;
}
function obtenerTotales2($conn,$hora1,$hora2){
  $llenados="SELECT count(distinct idbarrica)
  from PR_Orden l inner join PR_RegBarril r on r.IdOrden=l.IdOrden
  where l.fecha between '$hora1' and '$hora2' and r.TipoReg=1";
  $rellenados="SELECT count(distinct idbarrica)
  from PR_Orden l inner join PR_RegBarril r on r.IdOrden=l.IdOrden
  where l.fecha between '$hora1' and '$hora2' and r.TipoReg in (2,4,5)";
  $trasiegos="SELECT count(distinct idbarrica)
  from PR_Orden l inner join PR_RegBarril r on r.IdOrden=l.IdOrden
  where l.fecha between '$hora1' and '$hora2' and r.TipoReg=3";
  $reparados="SELECT count(distinct M.idbarrica)
  FROM PR_Mantenimiento M INNER JOIN ADM_logMantenimiento L ON M.IdMantenimiento=L.IdMantenimiento
  WHERE L.TipoOp='I' AND M.Fecha between '$hora1' and '$hora2'";
  $hoover="SELECT count(distinct IdTanque)
  FROM WM_OperacionTQH WHERE  fecha between '$hora1' and '$hora2'";
  $salida="[{\"llenados\":".ObtenerCantidad($llenados,$conn)." ,\"rellenados\": ".ObtenerCantidad($rellenados,$conn).",".
  "\"trasegados\":".ObtenerCantidad($trasiegos,$conn).",\"reparados\": ".ObtenerCantidad($reparados,$conn).",".
  "\"Hoover\": ".ObtenerCantidad($hoover,$conn)." }]";
  return $salida;
}
function obtenerTiempoIndv($conn,$consulta,$arrayDias){
  $salida="";
  $stmt = sqlsrv_query( $conn , $consulta);
  if($stmt!=null){
    if($arrayDias==null){
      while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC)){
        $salida=$salida."[\"".(int)$row[0]."\",".$row[1]."],";
      }
    }else{
      while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC)){
        $salida=$salida."[\"".$arrayDias[(int)$row[0]]."\",".$row[1]."],";
      }
    }

    $salida=substr($salida, 0, -1);
  }
  return $salida;

}
?>
