<?php
//$usuario=ISSET($_GET['usuario'])?$_GET['usuario']:"null";
//$pass=ISSET($_GET['pass'])?$_GET['pass']:"null";
include '../general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){
  if(ISSET($_GET['perfil'])){
    $user = "select u.Nombre, u.Clave, g.Nombre as IdGrupo, p.Descripcion,cast(DECRYPTBYPASSPHRASE ('Pims.2011',Pass) as varchar(200)) as Password
              from CM_Usuario u
              left join CM_Perfil p on u.IdPerfil=p.IdPerfil
              left join CM_Grupo g on u.IdGrupo=g.IdGrupo
              where Clave='$usuario'";
    $stmtUser = sqlsrv_query( $conn , $user);
    if($stmtUser){
      $result = array();
      do {
        while ($row = sqlsrv_fetch_array($stmtUser, SQLSRV_FETCH_ASSOC)){
          $result[] = $row;
        }
      } while (sqlsrv_next_result($stmtUser));

      sqlsrv_free_stmt($stmtUser);
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }
  }else if(ISSET($_GET['veriuser'])){
    $veriuser=$_GET["veriuser"];
    $user = "select COUNT(*) from CM_Usuario where Clave='$veriuser'";
    $stmtUser = sqlsrv_query( $conn , $user);
    $row = sqlsrv_fetch_array( $stmtUser, SQLSRV_FETCH_NUMERIC);
    if($row[0]!=0){
      echo '..Error.. El usuario ya existe, intenta con otro nombre de usario';
    }
  }

}else{
  echo '..Error.. Acceso no autorizado';
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
