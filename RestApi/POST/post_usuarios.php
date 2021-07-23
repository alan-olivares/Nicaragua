<?php
//$usuario=ISSET($_POST['usuario'])?$_POST['usuario']:"null";
//$pass=ISSET($_POST['pass'])?$_POST['pass']:"null";
include '../general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){
  include '../revisar_permisos.php';
  if(strpos($permisos,',3,') !== false){
    if(ISSET($_POST['nombrePerfilC'])){//Perfil a crear
      $nombrePerfil=$_POST["nombrePerfilC"];
      $queryCons="SELECT COUNT(*) from CM_Perfil where Descripcion='$nombrePerfil'";
      if(ObtenerCantidad($queryCons,$conn)==0){
        $queryCons="INSERT into CM_Perfil (Descripcion) values('$nombrePerfil')";
        $resultCons = sqlsrv_query( $conn , $queryCons);
        if($resultCons){
          echo 'Perfil creado con exito';
        }else{
          echo '..Error.. hubo un problema al crear el perfil, por favor intenta de nuevo más tarde';
        }
      }else{
        echo '..Error.. Ya existe un perfil con este nombre';
      }
    }else if(ISSET($_POST['idPerfilB'])){//Perfil a borrar
      $idPerfilB=$_POST["idPerfilB"];
      $queryCons="DELETE from CM_Perfil where IdPerfil='$idPerfilB'";
      $resultCons = sqlsrv_query( $conn , $queryCons);
      if($resultCons){
        $queryCons="DELETE from CM_PerfilPermiso where IdPerfil='$idPerfilB'";//Si se borro el perfil, entonces borramos los registros de CM_PerfilPermiso
        $resultCons = sqlsrv_query( $conn , $queryCons);
        echo 'Perfil borrado con exito';
      }else{
        echo '..Error.. hubo un problema al borrar el perfil, este perfil esta activo en algún usuario, intenta desasociar todos lo usuarios con este perfil primero e intenta de nuevo';
      }
    }else if(ISSET($_POST['permisos']) && ISSET($_POST['idPerfil'])){//Perfil a modificar
      $permisos=$_POST["permisos"];
      $idPerfil=$_POST["idPerfil"];
      if($idPerfil!=="1"){
        $queryborr="DELETE from CM_PerfilPermiso where IdPerfil='$idPerfil'";//Borramos los permisos que tenia registrados
        $resultCons = sqlsrv_query( $conn , $queryborr);
        if($resultCons && $permisos!=="null"){
          $arrayPermisos = explode(",", $permisos);//EL frontend nos manda un string separando a cada permiso con una coma, aqui lo separamos
          $queryPermiso="";
          foreach ($arrayPermisos as $permiso) {
            $queryPermiso= $queryPermiso." ('$idPerfil','$permiso'),";
          }
          $queryPermiso = substr($queryPermiso, 0, -1);//Quitamos la ultima coma de la consulta
          $queryCons="INSERT INTO CM_PerfilPermiso (IdPerfil,IdPermiso) VALUES ".$queryPermiso;
          $resultCons = sqlsrv_query( $conn , $queryCons);
          if($resultCons){
            echo "El perfil se ha actualizado con exito";
          }else{
            echo '..Error.. hubo un problema al actualizar el perfil';
          }
        }else{
          echo 'Los permisos se han borrado exitosamente';
        }
      }else{
        echo '..Error.. el perfil de administrador no puede ser modificado';
      }

    }/*else if(ISSET($_POST['borrarIdUser'])){//Usuario a borrar
      $borrarIdUser=$_POST["borrarIdUser"];

      $queryborr="DELETE from CM_Usuario where Clave='$borrarIdUser'";
      $resultCons = sqlsrv_query( $conn , $queryborr);
      if($resultCons){
        echo "El usuario $borrarIdUser se ha borrado con exito";
      }else{
        echo '..Error.. hubo un problema al intentar hacer la solucitud, por favor intenta más tarde';
      }
    }*/else if(ISSET($_POST['nombre'])){//Agregar nuevo usuario
      $passn=$_POST["passn"];
      $user=$_POST["user"];
      $perfil=$_POST["perfil"];
      $nombre=$_POST["nombre"];
      $activo=$_POST["activo"];
      $grupo=$_POST["grupo"];
      $acceso=$_POST["acceso"];
      $query="if not exists (select * from CM_Usuario where Clave='$user') "
      if($perfil!==""){
        $query=$query."INSERT into CM_Usuario (IdFacultad, IdGrupo,Nombre, Estatus,Clave,Pass,IdPerfil) values ($acceso,'$grupo','$nombre',$activo,'$user',ENCRYPTBYPASSPHRASE('Pims.2011','$passn'),'$perfil')";
      }else{
        $query=$query."INSERT into CM_Usuario (IdFacultad,IdGrupo,Nombre, Estatus,Clave,Pass) values ($acceso,'$grupo','$nombre',$activo,'$user',ENCRYPTBYPASSPHRASE('Pims.2011','$passn'))";
      }
      $resultCons = sqlsrv_query( $conn , $query);
      if($resultCons){
        echo "El usuario se ha registrado con exito";
      }else{
        echo '..Error.. hubo un problema al intentar hacer la solucitud, por favor intenta más tarde';
      }
    }else if(ISSET($_POST['userEdit'])){//Agregar nuevo usuario
      $passn=$_POST["passn"];
      $userEdit=$_POST["userEdit"];
      $perfil=$_POST["perfil"];
      $activo=$_POST["activo"];
      $grupo=$_POST["grupo"];
      $acceso=$_POST["acceso"];
      if($perfil!=="" && $passn!=""){
        $query="UPDATE CM_Usuario set IdFacultad=$acceso, IdGrupo='$grupo',Estatus=$activo, IdPerfil=$perfil,
        Pass=ENCRYPTBYPASSPHRASE('Pims.2011','$passn') where Clave='$userEdit'";
      }else if($perfil!==""){
        $query="UPDATE CM_Usuario set IdFacultad=$acceso, IdGrupo='$grupo',Estatus=$activo, IdPerfil=$perfil where Clave='$userEdit'";
      }else if($passn!==""){
        $query="UPDATE CM_Usuario set IdFacultad=$acceso, IdGrupo='$grupo',Estatus=$activo,
        IdPerfil=NULL,Pass=ENCRYPTBYPASSPHRASE('Pims.2011','$passn') where Clave='$userEdit'";
      }else if($perfil===""){
        $query="UPDATE CM_Usuario set IdFacultad=$acceso, IdGrupo='$grupo',Estatus=$activo, IdPerfil=NULL where Clave='$userEdit'";
      }
      $resultCons = sqlsrv_query( $conn , $query);
      if($resultCons){
        echo "El usuario se ha modificado exitosamente";
      }else{
        echo '..Error.. hubo un problema al intentar hacer la solucitud, por favor intenta más tarde';
      }
    }

  }else{
    echo '..Error.. No tienes permisos para procesar cambios';
  }

}else{
  echo '..Error.. Acceso no autorizado';
}
sqlsrv_close($conn); //Close the connnectiokn first

function ObtenerCantidad($queryCons,$conn){
  $resultCons = sqlsrv_query( $conn , $queryCons);
  $row = sqlsrv_fetch_array( $resultCons, SQLSRV_FETCH_NUMERIC);
  return (int)$row[0];
}
?>
