<?php
include '../general_connection.php';
if(strpos($permisos,',8,') !== false){
  if(ISSET($_POST['Capac']) && ISSET($_POST['NoSerie']) && ISSET($_POST['Ref'])){//Crear nuevo tanque hoover
    $Capac=$_POST['Capac'];
    $NoSerie=$_POST['NoSerie'];
    $Ref=$_POST['Ref'];
    $area=$_POST['area'];
    $prov=$_POST['prov'];
    $tsql = "SELECT count(*) from WM_Tanques where NoSerie='$NoSerie'";
    $veri=ObtenerCantidad($tsql,$conn);
    if($veri>0){
      echo '..Error.. Este número de serie ya se encuentra registrado en la base de datos, verificalo y vuelve a intentarlo';
    }else if($Capac>0 && $NoSerie>0 && $NoSerie<999999){
      $tsql = "INSERT INTO WM_Tanques (NoSerie,IdPallet,Capacidad,Litros,FechaRecepcion,IdEstado) values('$NoSerie',(isnull((select Top 1IdPallet from CM_Areas A inner Join WM_Pallet P on P.RackLocID = A.RackLocId where A.IdArea = $area),0)),'$Capac',0,GETDATE(),2); SELECT SCOPE_IDENTITY();";
      $result = sqlsrv_query( $conn , $tsql);
      sqlsrv_next_result($result);
      sqlsrv_fetch($result);
      $IdTanque= (int)sqlsrv_get_field($result, 0);
      if($result){
        $tsql = "INSERT INTO WM_TanqProv (IdTanque,IdProveedor,OrdenCompra) values($IdTanque,$prov,'$Ref');";
        $result = sqlsrv_query( $conn , $tsql);
        if($result){
          echo 'Tanque registrado con exito';
        }else{
          echo '..Error.. Hubo un problema al registrar el tanque Error:1 ';
        }
      }else{
        echo '..Error.. Hubo un problema al registrar el tanque. Error:2';
      }
    }else{
      echo '..Error.. Los datos ingresados fueron erroneos';
    }

  }
}else{
  echo '..Error.. No tienes permisos para procesar cambios';
}
sqlsrv_close($conn); //Close the connnectiokn first

?>
