<?php
header('Content-Type: application/json; charset=utf-8');
$usuario=$_GET["usuario"];
$pass=$_GET["pass"];
include'general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){
  if(ISSET($_GET['perfil'])){
    $user = "select u.Nombre, u.Clave, u.IdGrupo, p.Descripcion,cast(DECRYPTBYPASSPHRASE ('Pims.2011',Pass) as varchar(200)) as Password
              from CM_Usuario u
              left join CM_Perfil p on u.IdPerfil=p.IdPerfil
              where Clave='$usuario'";
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
