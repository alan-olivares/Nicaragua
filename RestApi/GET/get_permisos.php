<?php
$usuario=$_GET["usuario"];
$pass=$_GET["pass"];
include'general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){
  $ObtPermisos = "select per.IdPermiso from CM_Usuario u
      left join CM_Perfil p on p.IdPerfil=u.IdPerfil
      left join CM_PerfilPermiso pp on pp.IdPerfil=p.IdPerfil
      left join CM_Permiso per on per.IdPermiso=pp.IdPermiso
      where u.Clave='$usuario'";
  $stmtObtPermisos = sqlsrv_query( $conn , $ObtPermisos);
  $permisos=",";
  while( $row = sqlsrv_fetch_array( $stmtObtPermisos, SQLSRV_FETCH_NUMERIC) ) {
    $permisos=$permisos.$row[0].",";
  }
  echo $permisos;
}


?>
