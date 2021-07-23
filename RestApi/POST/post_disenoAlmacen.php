<?php
//$usuario=ISSET($_POST['usuario'])?$_POST['usuario']:"null";
//$pass=ISSET($_POST['pass'])?$_POST['pass']:"null";
include '../general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){
  include '../revisar_permisos.php';
  if(strpos($permisos,',7,') !== false){
    if(isJson($_POST["data"])){
      $tipo=$_POST["tipo"];
      $id=$_POST["id"];
      //echo $_POST["data"];
      $data = json_decode($_POST["data"], true);
      foreach ($data as $campo) {
        $query=operaciones($tipo,$campo,$id);
        //echo $query;
        sqlsrv_query( $conn , $query);
      }
      echo $tipo.' actualizados correctamiente';
    }else{
      echo '..Error.. Se produjo un problema al actualizar los datos';
    }
  }else{
    echo '..Error.. No tienes permisos para procesar cambios';
  }

}else{
  echo '..Error.. Acceso no autorizado';
}
sqlsrv_close($conn); //Close the connnectiokn first

function operaciones($tipo,$campo,$id) {
  $query="";
  if($tipo==='bodega'){
    if($campo['borrar'] && $campo['id']!=-1){
      $query="DELETE from AA_Almacen where AlmacenID=".$campo['id'];
    }else if($campo['id']!=-1){
      $query="UPDATE AA_Almacen set Nombre='".$campo['Nombre']."',Descripcion='".$campo['Nombre']."',X='".
      $campo['x']."',Y='".$campo['y']."',Alto='".$campo['height']."',Largo='".$campo['width']."' where AlmacenID=".$campo['id'];
    }else if($campo['id']==-1 && $campo['borrar']==false){
      $query="INSERT into AA_Almacen (AlmacenID,PlantaID,Nombre,Descripcion,Consecutivo,X,Y,Alto,Largo)
      values((select isnull((select top 1 AlmacenID+1 from AA_Almacen order by AlmacenID desc),0)+1),$id,'".
      $campo['Nombre']."','".$campo['Nombre']."',(select isnull((select top 1 Consecutivo from AA_Almacen where PlantaID=$id order by Consecutivo desc),0)+1),
      '".$campo['x']."','".$campo['y']."','".$campo['height']."','".$campo['width']."')";
    }
  }else if($tipo==='Costado'){
    if($campo['borrar'] && $campo['id']!=-1){
      $query="DELETE from AA_Area where AreaId=".$campo['id'];
    }else if($campo['id']!=-1){
      $query="UPDATE AA_Area set Nombre='".$campo['Nombre']."',X='".$campo['x']."',Y='".$campo['y']."',Alto='".$campo['height']."',Largo='".$campo['width']."' where AreaId=".$campo['id'];
    }else if($campo['id']==-1 && $campo['borrar']==false){
      $query="INSERT into AA_Area (AreaId,AlmacenId,Nombre,Consecutivo,X,Y,Alto,Largo)
      values((select isnull((select top 1 AreaId+1 from AA_Area order by AreaId desc),0)+1),$id,'"
      .$campo['Nombre']."',(select isnull((select top 1 Consecutivo from AA_Area where AlmacenId=$id order by Consecutivo desc),0)+1),
      '".$campo['x']."','".$campo['y']."','".$campo['height']."','".$campo['width']."')";
    }
  }else if($tipo==='Filas'){
    if($campo['borrar'] && $campo['id']!=-1){
      $query="DELETE from AA_Seccion where SeccionID=".$campo['id'];
    }else if($campo['id']!=-1){
      $query="UPDATE AA_Seccion set Nombre='".$campo['Nombre']."',X='".$campo['x']."',Y='".$campo['y']."',Alto='".$campo['height']."',Largo='".$campo['width']."' where SeccionID=".$campo['id'];
    }else if($campo['id']==-1 && $campo['borrar']==false){
      $query="INSERT into AA_Seccion (SeccionID,AreaId,Nombre,Consecutivo,X,Y,Alto,Largo)
      values((select isnull((select top 1 SeccionID+1 from AA_Seccion order by SeccionID desc),0)+1),$id,'".$campo['Nombre']."',
      (select isnull((select top 1 Consecutivo from AA_Seccion where AreaId=$id order by Consecutivo desc),0)+1),
      '".$campo['x']."','".$campo['y']."','".$campo['height']."','".$campo['width']."')";
    }
  }else if($tipo==='Torres'){
    if($campo['borrar'] && $campo['id']!=-1){
      $query="DELETE from AA_Posicion where PosicionID=".$campo['id'];
    }else if($campo['id']!=-1){
      $query="UPDATE AA_Posicion set Nombre='".$campo['Nombre']."' where PosicionID=".$campo['id'];
    }else if($campo['id']==-1 && $campo['borrar']==false){
      $query="INSERT into AA_Posicion (PosicionID,SeccionID,Nombre,Consecutivo)
      values((select isnull((select top 1 PosicionID+1 from AA_Posicion order by PosicionID desc),0)+1),$id,'".$campo['Nombre']."',
      (select isnull((select top 1 Consecutivo from AA_Seccion where SeccionID=$id order by Consecutivo desc),0)+1))";
    }

  }else if($tipo==='Niveles'){
    if($campo['borrar'] && $campo['id']!=-1){
      $query="exec sp_NivelOpe ".$campo['id'].",'', 2";//Operacion 2 elimina
    }else if($campo['id']!=-1){
      $query="UPDATE AA_Nivel set Nombre='".$campo['Nombre']."' where NivelID=".$campo['id'];
    }else if($campo['id']==-1 && $campo['borrar']==false){
      $query="exec sp_NivelOpe $id, '".$campo['Nombre']."', 1";//Operacion 1 inserta
    }
  }
  return $query;
}

function isJson($string) {
 json_decode($string);
 return (json_last_error() == JSON_ERROR_NONE);
}
?>
