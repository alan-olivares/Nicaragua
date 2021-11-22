
<?php
$ObtPermisos = "SELECT per.IdPermiso from CM_Usuario_WEB u
    left join CM_Perfil p on p.IdPerfil=u.IdPerfil
    left join CM_PerfilPermiso pp on pp.IdPerfil=p.IdPerfil
    left join CM_Permiso per on per.IdPermiso=pp.IdPermiso
    where u.Clave='$usuario'";
$stmtObtPermisos = sqlsrv_query( $conn , $ObtPermisos);
$permisos="*,";
while( $row = sqlsrv_fetch_array( $stmtObtPermisos, SQLSRV_FETCH_NUMERIC) ) {
  $permisos=$permisos.$row[0].",";
}
$permisos=$permisos."*";
?>
