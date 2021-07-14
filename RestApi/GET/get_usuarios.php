<?php
//$usuario=ISSET($_GET['usuario'])?$_GET['usuario']:"null";
//$pass=ISSET($_GET['pass'])?$_GET['pass']:"null";
include '../general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){
  include '../revisar_permisos.php';
  if(strpos($permisos,',3,') !== false){
    if(ISSET($_GET['perfiles'])){
      $user = "select IdPerfil,Descripcion from CM_Perfil";
      imprimir($user,$conn);
    }else if(ISSET($_GET['permisos'])){
      $user = "select IdPermiso,Descripcion from CM_Permiso";
      imprimir($user,$conn);

    }else if(ISSET($_GET['PerfilPermisos'])){
      $PerfilPermisos=$_GET["PerfilPermisos"];
      $user = "select p.IdPerfil,p.Descripcion as PerfilDes,per.IdPermiso,per.Descripcion as PermisoDes from CM_Perfil p
              left join CM_PerfilPermiso pp on pp.IdPerfil=p.IdPerfil
              left join CM_Permiso per on pp.IdPermiso=per.IdPermiso where p.IdPerfil='$PerfilPermisos'";
      imprimir($user,$conn);
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
      imprimir($users,$conn);
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
    echo '..Error.. Hubo un error al obtener los datos, intenta de nuevo más tarde';
  }
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
