<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Inicio de sesión</title>
      <link rel="icon" href="img\TBRE.ico">
      <link href="css/bootstrap.min.css" rel="stylesheet">
      <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
      <link href="css/animate.css" rel="stylesheet">
      <link href="css/style.css" rel="stylesheet">
   </head>
   <body class="gray-bg">
      <script src="js/esta_activo.js"></script>
      <div class="text-center">
         <img src="img\logo-ser-licorera.jpg" class="logoLogin">
      </div>
      <div class="middle-box text-center loginscreen animated fadeInDown">
         <div>
            <p class="p-estilo">Ingresa tus credenciales para acceder al sistema</p>
            <p class="p-estilo" id="estado" style="color:#ed5565"></p>
            <form class="m-t" role="form" method="POST" action="">
               <div class="form-group">
                  <input type="text" class="form-control b-r-xl" placeholder="Usuario" required="true" name="usuario" id="usuario">
               </div>
               <div class="form-group">
                  <input type="password" class="form-control b-r-xl" placeholder="Contraseña" required="true" name="password" id="password">
               </div>
               <button type="submit" class="btn btn-primary block full-width m-b b-r-xl">Iniciar sesión</button>
            </form>
         </div>
      </div>
      <div class="middle-box text-center loginscreen animated fadeInDown">
         <h3><img src="img\TBRE.ico" class="icono">Trazabilidad de Barriles y Rones Envejecidos</h3>
      </div>
      <?php
         if(ISSET($_POST['usuario'])){
           $usuario=$_POST['usuario'];
           $contra=$_POST['password'];
           include 'general_connection.php';
           include 'RestApi/encriptacion.php';
           $tsql = "exec sp_getAcceso '$usuario' , '$contra'";
           $evento="";
           $stmt = sqlsrv_query( $conn , $tsql);
           while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC))
           {
             if($row[0]=='1'){
               $tsql2 = "select top 1 u.Nombre,p.Descripcion,per.Pagina from CM_Usuario u
               left join CM_Perfil p on p.IdPerfil=u.IdPerfil
               left join CM_PerfilPermiso pp on pp.IdPerfil=p.IdPerfil
               left join CM_Permiso per on per.IdPermiso=pp.IdPermiso where u.Clave='$usuario' order by per.Orden;";
               $stmt2 = sqlsrv_query( $conn , $tsql2);
               $row2 = sqlsrv_fetch_array( $stmt2, SQLSRV_FETCH_NUMERIC);
               if( $row2[2]!=null)
               {
                 ?>
      <script type="text/javascript">
         localStorage['nombre'] = '<?php echo utf8_encode($row2[0]);?>';
         localStorage['perfil'] = '<?php echo utf8_encode($row2[1]);?>';
         localStorage['paginaI'] = '<?php echo utf8_encode($row2[2]);?>';
         localStorage['usuario'] = '<?php echo PHP_AES_Cipher::encrypt("Pims.2021","fedcba9876543210",$usuario);?>';
         localStorage['password'] = '<?php echo PHP_AES_Cipher::encrypt("Pims.2021","fedcba9876543210",$contra);?>';
         localStorage['sesion_timer']=new Date();
         window.location.replace(localStorage['paginaI']);
      </script>
      <?php
    }else{
      ?>
   <script type="text/javascript">
      document.getElementById("estado").innerHTML = 'No tienes permisos para acceder al sitio';
   </script>
   <?php
    }
         sqlsrv_free_stmt( $stmt2);
         }else{
           ?>
      <script type="text/javascript">
         document.getElementById("estado").innerHTML = 'Usuario o contraseña incorrecta';
      </script>
      <?php
         }
         }

         /* Free statement and connection resources. */
         sqlsrv_free_stmt( $stmt);
         sqlsrv_close( $conn);
         }
         ?>
      <!-- Mainly scripts -->
      <script src="js/jquery-3.1.1.min.js"></script>
      <script src="js/popper.min.js"></script>
      <script src="js/bootstrap.js"></script>
   </body>
</html>
