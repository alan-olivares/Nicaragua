<?php
$usuario=ISSET($_GET['usuario'])?$_GET['usuario']:"null";
$pass=ISSET($_GET['pass'])?$_GET['pass']:"null";
include'general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){
  include'revisar_permisos.php';
  if(strpos($permisos,',3,') !== false){
    if(ISSET($_GET['perfiles'])){
      $user = "select IdPerfil,Descripcion from CM_Perfil";
      $stmtUser = sqlsrv_query( $conn , $user);
      if($stmtUser){
        $result = array();
        do {
          while ($row = sqlsrv_fetch_array($stmtUser, SQLSRV_FETCH_ASSOC)){
            $result[] = array_map("utf8_encode",$row);
          }
        } while (sqlsrv_next_result($stmtUser));

        sqlsrv_free_stmt($stmtUser);
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
      }
    }else if(ISSET($_GET['permisos'])){
      $user = "select IdPermiso,Descripcion from CM_Permiso";
      $stmtUser = sqlsrv_query( $conn , $user);
      if($stmtUser){
        $result = array();
        do {
          while ($row = sqlsrv_fetch_array($stmtUser, SQLSRV_FETCH_ASSOC)){
            $result[] = array_map("utf8_encode",$row);
          }
        } while (sqlsrv_next_result($stmtUser));

        sqlsrv_free_stmt($stmtUser);
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
      }
    }else if(ISSET($_GET['PerfilPermisos'])){
      $PerfilPermisos=$_GET["PerfilPermisos"];
      $user = "select p.IdPerfil,p.Descripcion as PerfilDes,per.IdPermiso,per.Descripcion as PermisoDes from CM_Perfil p
              left join CM_PerfilPermiso pp on pp.IdPerfil=p.IdPerfil
              left join CM_Permiso per on pp.IdPermiso=per.IdPermiso where p.IdPerfil='$PerfilPermisos'";
      $stmtUser = sqlsrv_query( $conn , $user);
      if($stmtUser){
        $result = array();
        do {
          while ($row = sqlsrv_fetch_array($stmtUser, SQLSRV_FETCH_ASSOC)){
            $result[] = array_map("utf8_encode",$row);
          }
        } while (sqlsrv_next_result($stmtUser));

        sqlsrv_free_stmt($stmtUser);
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
      }
    }else if(ISSET($_GET['usuarios'])){
      $veriuser=$_GET["veriuser"];
      $user = "select COUNT(*) from CM_Usuario where Clave='$veriuser'";
      $stmtUser = sqlsrv_query( $conn , $user);
      $row = sqlsrv_fetch_array( $stmtUser, SQLSRV_FETCH_NUMERIC);
      if($row[0]!=0){
        echo '..Error.. El usuario ya existe, intenta con otro nombre de usario';
      }
    }else if(ISSET($_GET['tablaUsuarios'])){
      $users = "select u.Nombre,u.Clave,g.Nombre as Grupo,CASE WHEN u.IdFacultad=1 THEN 'Si' ELSE 'No' END as Acceso,
				        CASE WHEN u.Estatus=1 THEN 'Activo' ELSE 'Desactivado' END as Estado, p.Descripcion as Perfil
                from CM_Usuario u left join CM_Perfil p on p.IdPerfil=u.IdPerfil
                left join CM_Grupo g on u.IdGrupo=g.IdGrupo
                where u.Clave IS NOT NULL";
      $stmtUsers = sqlsrv_query( $conn , $users);
      if($stmtUsers){
        $result = array();
        do {
          while ($row = sqlsrv_fetch_array($stmtUsers, SQLSRV_FETCH_ASSOC)){
            $result[] = array_map("utf8_encode",$row);
          }
        } while (sqlsrv_next_result($stmtUsers));

        sqlsrv_free_stmt($stmtUsers);
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
      }
    }
  }else{
    echo '..Error.. No tienes acceso a esta area';
  }

}else{
  echo '..Error.. Acceso no autorizado';
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
