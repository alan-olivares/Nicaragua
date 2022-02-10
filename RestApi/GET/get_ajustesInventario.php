<?php
include '../general_connection.php';
if(strpos($permisos,',6,') !== false){
  if(ISSET($_GET['tipo'])){
    if($_GET['tipo']==='1'){
      $barriles = "SELECT  B.Consecutivo,Al.Descripcion as Alcohol,Datepart(YYYY,L.Recepcion) as Año, C.Codigo as Uso,CONVERT(varchar(10),B.FechaRelleno, 120) as Relleno, B.Capacidad,
      CASE WHEN Am.Nombre is null THEN 'Barril sin ubicación' ELSE Am.Nombre+', '+ REPLACE(Ar.Nombre, 'COSTADO', 'Cos: ')+', '+REPLACE(Se.Nombre, 'FILA', 'F: ')+','+ REPLACE(Po.Nombre, 'TORRE', 'T: ') +','+ REPLACE(N.Nombre, 'NIVEL', 'N: ') END AS Ubicacion
      from WM_Barrica B inner Join WM_Pallet P on P.Idpallet = B.IdPallet
      left Join CM_CodEdad CE on CE.IdCodEdad = B.IdCodificacion left Join CM_Codificacion C on C.IdCodificacion = CE.IdCodificicacion
      left Join CM_Edad E on E.IdEdad = CE.IdEdad  left Join WM_LoteBarrica LB on Lb.IdLoteBarica = B.IdLoteBarrica
      left Join PR_Lote L on L.IdLote = LB.IdLote left Join CM_Alcohol Al on Al.IdAlcohol = L.IdAlcohol
      left Join CM_Estado ES on Es.IdEstado = B.IdEstado left join WM_RackLoc R on P.RackLocID=R.RackLocID
      inner Join AA_Nivel N on R.NivelID=N.NivelID inner Join AA_Posicion Po on N.PosicionId=Po.PosicionID
      inner Join AA_Seccion Se on Po.SeccionID=Se.SeccionID inner Join AA_Area Ar on Se.AreaId = Ar.AreaId
      inner Join AA_Almacen Am on Ar.AlmacenId=Am.AlmacenID where es.IdEstado=1 ";
      if($_GET['annio']!==''){
        $barriles=$barriles." and Datepart(YYYY,L.Recepcion)=".$_GET['annio'];
      }
      if($_GET['alcohol']!==''){
        $barriles=$barriles." and Al.IdAlcohol=".$_GET['alcohol'];
      }
      if($_GET['uso']!==''){
        $barriles=$barriles." and C.IdCodificacion=".$_GET['uso'];
      }
      if($_GET['fechas']!==''){
        $barriles=$barriles." and CONVERT(varchar(10),B.FechaRelleno, 120) ='".$_GET['fechas']."'";
      }

      imprimir($barriles,$conn);
    }else if($_GET['tipo']==='2'){
      $barriles = "SELECT  T.NoSerie,T.Capacidad,T.Litros,CONVERT(varchar(10),T.FechaLLenado,120) as 'Fecha Llenado',
      CASE WHEN Am.Nombre is null THEN 'Tanque sin ubicación' ELSE Am.Nombre+', '+ REPLACE(Ar.Nombre, 'COSTADO', 'Cos: ')+', '+REPLACE(Se.Nombre, 'FILA', 'F: ')+','+ REPLACE(Po.Nombre, 'TORRE', 'T: ') +','+ REPLACE(N.Nombre, 'NIVEL', 'N: ') END AS Ubicación
       from WM_Tanques T left Join WM_Pallet P on P.Idpallet = T.IdPallet left join WM_RackLoc R on P.RackLocID=R.RackLocID
      left Join AA_Nivel N on R.NivelID=N.NivelID left Join AA_Posicion Po on N.PosicionId=Po.PosicionID
      left Join AA_Seccion Se on Po.SeccionID=Se.SeccionID left Join AA_Area Ar on Se.AreaId = Ar.AreaId
      left Join AA_Almacen Am on Ar.AlmacenId=Am.AlmacenID where T.IdEstado=1 ";
      if($_GET['fechas']!==''){
        $barriles=$barriles."and CONVERT(varchar(10),T.FechaLLenado, 120) ='".$_GET['fechas']."'";
      }
      imprimir($barriles,$conn);
    }
  }else if(ISSET($_GET['relleno'])){//Tabla principal de ajustesRelleno
    $fecha=$_GET['fecha'];
    validaFecha($fecha,$conn);
    $consulta = "SELECT	O.IdOrden as Orden,RB.Consecutivo,Al.Descripcion as Alcohol,C.Codigo as Uso,RB.Capacidad,
    Case when Od.Estatus = 1 then 'Donador' When Od.Estatus = 2 then 'Relleno' when Od.Estatus = 3 then 'Resto' end Estatus
    from PR_Orden O inner Join PR_OP OP on OP.IdOrden = O.IdOrden inner Join PR_OperaDetail OD on Od.IdOperacion = Op.IdOperacion
    inner Join CM_Alcohol Al on Al.IdAlcohol = OP.IdAlcohol left Join PR_RegTanque RT on RT.IdOrden = O.IdOrden
    left Join CM_Tanque T on T.IDTanque = RT.IdTanque inner Join PR_RegBarril RB on RB.IdOrden = O.IdOrden and RB.IdBarrica = OD.IdBarrica
    left Join WM_LoteBarrica LB ON RB.IdloteBarrica=LB.IdLoteBarica left Join  PR_Lote L ON L.IdLote=LB.IdLote
    inner Join CM_CodEdad CE on CE.IdCodEdad = RB.IdCodEdad inner Join CM_Codificacion C on C.IdCodificacion = CE.IdCodificicacion
    Where O.IdTipoOp = 3 and L.Recepcion is not null and  Od.Estatus >0 and convert(varchar(10),O.Fecha,120)='$fecha' ";
    if($_GET['alcohol']!==''){
      $consulta=$consulta." and Al.IdAlcohol=".$_GET['alcohol'];
    }
    if($_GET['uso']!==''){
      $consulta=$consulta." and C.IdCodificacion=".$_GET['uso'];
    }
    imprimir($consulta,$conn);
  }else if(ISSET($_GET['llenado'])){//Tabla principal de ajustesRelleno
    $fecha=$_GET['fecha'];
    validaFecha($fecha,$conn);
    $consulta = "SELECT LB.IdLote as Lote,B.Consecutivo,	Al.Descripcion as Alcohol,	C.Codigo as Uso,	B.Capacidad ,	T.Codigo as Tanque
    from PR_RegBarril B inner Join WM_LoteBarrica LB on LB.IdLoteBarica = B.IdLoteBarrica
    inner join WM_Reg_Rep_llen Reg on Reg.IdBarrica=B.IdBarrica left Join PR_Lote L on L.Idlote = LB.IdLote
    inner Join CM_CodEdad CE on CE.IdCodEdad = B.IdCodEdad inner Join CM_Codificacion C on C.IdCodificacion = CE.IdCodificicacion
    inner Join WM_RecDetail RC on RC.IdLote = L.Idlote inner Join CM_Tanque T on T.Idtanque = RC.IdTanque
    inner Join CM_Alcohol Al on Al.IdAlcohol = L.IdAlcohol Where B.TipoReg=1 and convert(varchar(10),Reg.Fecha,120)='$fecha' ";
    if($_GET['alcohol']!==''){
      $consulta=$consulta." and Al.IdAlcohol=".$_GET['alcohol'];
    }
    if($_GET['uso']!==''){
      $consulta=$consulta." and C.IdCodificacion=".$_GET['uso'];
    }
    imprimir($consulta,$conn);
  }else if(ISSET($_GET['annio'])){
    $lotes = "SELECT distinct(Datepart(YYYY,Recepcion)) as annio from PR_Lote order by Datepart(YYYY,Recepcion) desc";
    imprimir($lotes,$conn);
  }else if(ISSET($_GET['alcohol'])){//Obtenemos informacion de las ordenes por la fecha
    $Alcohol = "SELECT IdAlcohol,Descripcion from CM_Alcohol";
    imprimir($Alcohol,$conn);

  }else if(ISSET($_GET['uso'])){//Obtenemos las ordenes dadas por una fecha en especifico
    $uso = "SELECT IdCodificacion,Codigo from CM_Codificacion";
    imprimir($uso,$conn);

  }else if(ISSET($_GET['fechasLotes'])){//Obtenemos informacion de los lotes por la fecha
    $fecha = "SELECT DISTINCT(convert(varchar, Fecha, 23)) as Fecha from WM_LoteBarrica";
    imprimir($fecha,$conn);
  }else if(ISSET($_GET['motivo'])){//Obtenemos informacion de los lotes por la fecha
    $razones = "SELECT IdRazon,Descripcion from ADM_Razones where IdCaso=4";
    imprimir($razones,$conn);
  }
}else{
  echo '..Error.. No tienes acceso a esta area';
}
function validaFecha($fecha,$conn){
  $date1=date_create($fecha);
  $date2=new DateTime();
  if(date_diff($date1,$date2)->format("%R%a")>1){
    terminarScript($conn,'..Error.. Solo se pueden editar registros de un día antes y del día actual');
  }
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
