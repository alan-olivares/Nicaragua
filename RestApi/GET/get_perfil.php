<?php
include '../general_connection.php';
if(ISSET($_GET['perfil'])){
  $user = "SELECT u.Nombre, u.Clave, g.Nombre as IdGrupo, p.Descripcion,cast(DECRYPTBYPASSPHRASE ('Pims.2011',Pass) as varchar(200)) as Password,u.Tema
            from CM_Usuario_WEB u
            left join CM_Perfil p on u.IdPerfil=p.IdPerfil
            left join CM_Grupo g on u.IdGrupo=g.IdGrupo
            where Clave='$usuario'";
  imprimir($user,$conn);
}else if(ISSET($_GET['veriuser'])){
  $veriuser=$_GET["veriuser"];
  $user = "SELECT COUNT(*) from CM_Usuario_WEB where Clave='$veriuser'";
  if(ObtenerCantidad($user,$conn)!=0){
    echo '..Error.. El usuario ya existe, intenta con otro nombre de usario';
  }
}
sqlsrv_close($conn); //Close the connnectiokn first
?>
