<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>TBRE</title>
      <link href="css/jquery.dataTables.min.css" rel="stylesheet">
      <link href="css/bootstrap.min.css" rel="stylesheet">
      <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
      <link rel="icon" href="img\TBRE.ico">
      <link rel="stylesheet" href="css/jquery-ui.css">
      <link href="css/animate.css" rel="stylesheet">
      <link href="css/style.css" rel="stylesheet">
      <link rel="stylesheet" href="/resources/demos/style.css">
      <link href="css/plugins/sweetalert/sweetalert.css" rel="stylesheet">
      <link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">
   </head>
   <body >
      <script src="js/revisar_sesion.js"></script>
      <div id="wrapper">
         <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
               <ul class="nav metismenu" id="side-menu">
                  <li class="nav-header">
                     <div class="dropdown profile-element">
                        <img alt="image" class="rounded-circle perfil" id="perfil1"/>
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                           <span class="block m-t-xs font-bold">
                              <script>
                                 document.write(localStorage['nombre'] || 'Sin nombre');
                                 var foto=localStorage['nombre'] || 'Sin nombre';
                                 foto=foto.charAt(0).concat(".png").toLowerCase();
                                 document.getElementById("perfil1").src="img/letras/"+foto;
                              </script>
                           </span>
                           <span class="text-muted text-xs block">
                              <script>
                                 document.write(localStorage['perfil'] || 'Sin perfil');
                              </script><b class="caret"></b>
                           </span>
                        </a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
                           <li><a class="dropdown-item" href="perfil.html">Perfil</a></li>
                           <li class="dropdown-divider"></li>
                           <li><a class="dropdown-item" onclick="limpiar();">Cerrar sesión</a></li>
                        </ul>
                     </div>
                     <div class="logo-element">
                        TBRE
                     </div>
                  </li>
                  <li class="once">
                     <a href="index.php"><i class="fa fa-area-chart"></i> <span class="nav-label">Operación del día</span></a>
                  </li>
                  <li class="diez">
                     <a href="#"><i class="fa fa-table"></i> <span class="nav-label">Reportes</span><span class="fa arrow"></span></a>
                     <ul class="nav nav-second-level">
                        <li><a href="inventario.html">Inventario</a></li>
                        <li><a href="barriles_plantel.html">Barriles en plantel</a></li>
                        <li><a href="llenados.html">Barriles llenados</a></li>
                        <li><a href="rellenados.html">Barriles rellenados</a></li>
                        <li><a href="trasiego.html">Barriles trasegados</a></li>
                        <li><a href="RGerencia.html">Reporte de Gerencia</a></li>
                     </ul>
                  </li>
                  <li class="active dos-cinco">
                     <a href="#"><i class="fa fa-desktop"></i> <span class="nav-label">TBRE</span><span class="fa arrow"></span></a>
                     <ul class="nav nav-second-level">
                        <li class="active dos"><a href="validar_informacion.php">Validar información</a></li>
                        <li class="cinco"><a href="impresion.html">Impresión de etiquetas</a></li>
                     </ul>
                  </li>
                  <li class="seis">
                     <a href="#"><i class="fa fa-briefcase"></i> <span class="nav-label">Órdenes de trabajo</span><span class="fa arrow"></span></a>
                     <ul class="nav nav-second-level">
                       <li>
                           <a href="#">Llenado<span class="fa arrow"></span></a>
                           <ul class="nav nav-third-level">
                               <li><a href="OLlenado.html">Orden Llenado</a></li>
                               <li><a href="RLlenadoLlenada.html">Reporte de Llenado</a></li>
                               <li><a href="RLlenadoMantenimineto.html">Reporte de Mantenimiento</a></li>
                               <li><a href="RLlenadoRevisado.html">Reporte de Revisado</a></li>
                           </ul>
                       </li>
                       <li>
                           <a href="#">Relleno<span class="fa arrow"></span></a>
                           <ul class="nav nav-third-level">
                               <li><a href="ORelleno.html">Orden Relleno</a></li>
                               <li><a href="RRellenoOperacion.html">Reporte de Operación</a></li>
                           </ul>
                       </li>
                       <li><a href="revision.html">Revisión</a></li>
                       <li>
                           <a href="#">Trasiego<span class="fa arrow"></span></a>
                           <ul class="nav nav-third-level">
                                <li><a href="OTrasiego.html">Orden Trasiego</a></li>
                                <li><a href="RTrasiegoHojaAnalisis.html">Hoja de Análisis de Trasiego</a></li>
                                <li><a href="RTrasiegoVaciados.html">Barriles vaciados por trasiego</a></li>
                                <li><a href="RTrasiegoRemision.html">Remisión blending</a></li>
                           </ul>
                       </li>
                     </ul>
                  </li>
                  <li class="ocho">
                     <a href="#"><i class="fa fa-inbox"></i> <span class="nav-label">Recepción</span><span class="fa arrow"></span></a>
                     <ul class="nav nav-second-level collapse">
                        <li><a href="RAlcohol.html">Alcohol</a></li>
                        <li><a href="RBarriles.html">Barriles</a></li>
                     </ul>
                  </li>
                  <li class="uno-dos">
                     <a href="#"><i class="fa fa-bell"></i> <span class="nav-label">Solicitudes</span><span class="fa arrow"></span><span class="label label-primary float-right uno" style="margin-right:15px;"id="letrero1">0</span></a>
                     <ul class="nav nav-second-level collapse">
                        <li><a href="pendientes.html">Pendientes <span class="label label-primary float-right uno" id="letrero2">0</span></a></li>
                        <li><a href="aceptadas.html">Aceptadas</a></li>
                        <li><a href="rechazadas.html">Rechazadas</a></li>
                        <li><a href="canceladas.html">Canceladas</a></li>
                     </ul>
                  </li>
                  <li class="siete">
                     <a href="disenoAlmacen.html"><i class="fa fa-building"></i> <span class="nav-label">Diseñar almacenes</span></a>
                  </li>
                  <li class="nueve">
                     <a href="AjustesInventario.html"><i class="fa fa-edit"></i> <span class="nav-label">Ajustes de inventario</span></a>
                  </li>
                  <li class="tres-cuatro">
                     <a href="#"><i class="fa fa-gear"></i> <span class="nav-label">Administración</span><span class="fa arrow"></span></a>
                     <ul class="nav nav-second-level">
                        <li class="tres"><a href="usuarios.html">Usuarios</a></li>
                        <li class="cuatro"><a href="configuracion.html">Configuración</a></li>
                     </ul>
                  </li>
               </ul>
            </div>
         </nav>
         <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom">
               <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                  <div class="navbar-header">
                     <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                  </div>
                  <h2 class="nav navbar-top-links titulo welcome-message">Validar información</h2>
                  <ul class="nav navbar-top-links navbar-right">
                     <li class="dropdown uno">
                        <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                        <i class="fa fa-bell"></i>  <span class="label label-primary" id="letrero3">0</span>
                        </a>
                        <ul class="dropdown-menu dropdown-alerts">
                           <li>
                              <a href="pendientes.html" class="dropdown-item">
                                 <div id="letrero4">
                                    <i class="fa fa-envelope fa-fw" ></i> Tienes 0 solicitudes de cambios
                                 </div>
                              </a>
                           </li>
                        </ul>
                     </li>
                     <li>
                        <a onclick="limpiar();">
                        <i class="fa fa-sign-out"></i> Cerrar sesión
                        </a>
                     </li>
                  </ul>
               </nav>
            </div>
            <?php
               include'general_connection.php';
               $bodegas = "Select AlmacenId,Nombre from AA_Almacen order by Nombre";
               $stmtBodegas = sqlsrv_query( $conn , $bodegas);
               ?>
            <div class="row wrapper border-bottom white-bg page-heading " >
               <div class="col-md-12 text-center  d-flex justify-content-center " >
                  <div class="form-inline text-center d-flex justify-content-center"  >
                    <h5>
                       <label>  Planta:  </label>
                       <select class="form-control b-r-xl" onchange="getInfo(this,'bodegas','Nombre','AlmacenId','bodega');" id="planta"></select>
                    </h5>
                     <h5>
                        <label>  Bodega:  </label>
                        <select class="form-control b-r-xl" onchange="getInfo(this,'bodega','Costados','ID','Costado');" id="bodega">
                        </select>
                     </h5>
                     <h5><label>  Costado:   </label>
                        <select class="form-control b-r-xl" name="Costado"  id="Costado" onchange="getInfo(this,'area','Filas','ID','Filas');">
                        </select>
                     </h5>
                     <h5><label>  Filas:   </label>
                        <select class="form-control b-r-xl" name="Filas" id="Filas" onchange="getInfo(this,'fila','Torres','ID','Torres');">
                        </select>
                     </h5>
                     <h5><label>  Torres:   </label>
                        <select class="form-control b-r-xl" name="Torres"  id="Torres" onchange="getInfo(this,'torre','Niveles','RackLocID','Niveles');">
                        </select>
                     </h5>
                     <h5><label>  Niveles:   </label>
                        <select class="form-control b-r-xl" name="Niveles" id="Niveles" onchange="CargarTabla(this);">
                        </select>
                     </h5>
                  </div>
               </div>
            </div>
            <div class="col-md-12 d-flex justify-content-center text-center ">
               <button class="button6 btn btn-primary animated b-r-xl" onclick="Agregar();" id="Agregar">Agregar</button>
               <button class="button6 btn btn-primary animated b-r-xl" onclick="Mover();" id="Mover">Mover</button>
               <button class="button6 btn btn-primary animated b-r-xl" onclick="Editar();" id="Editar">Editar</button>
            </div>
            <div class="col-md-12 d-flex justify-content-center table-responsive text-center " id="table-wrapper">
               <div class="table-responsive col-md-12 centro scrollingtable animated" id="scrollingtable">
                  <table class="table table-bordered table-hover text-nowrap display" id="barriles">
                     <thead>
                        <tr>
                           <th>Consecutivo</th>
                           <th>Capacidad</th>
                           <th>Revisado</th>
                           <th>Relleno</th>
                           <th>Año</th>
                           <th>NoTapa</th>
                           <th>Uso</th>
                           <th>Edad</th>
                           <th>Recepción</th>
                           <th>Alcohol</th>
                           <th>Estado</th>
                        </tr>
                     </thead>
                     <tbody></tbody>
                  </table>
               </div>
            </div>
            <div class="sk-spinner sk-spinner-three-bounce">
               <div class="sk-bounce1"></div>
               <div class="sk-bounce2"></div>
               <div class="sk-bounce3"></div>
            </div>
            <div class="footer">
               <div>
                  <strong>Todos los derechos reservados | </strong>SER Licorera 2014
               </div>
            </div>
         </div>
      </div>
      <div id="agregarDialog" title="Agregar barril" class="animated">
         <div>
            <div class="col-md-12 text-center  d-flex justify-content-center" >
               <h5><label>  Ingresa el consecutivo del barril:   </label>
                  <input type="text" class="form-control" required="true" name="concecutivoA" id="concecutivoA">
               </h5>
               <h5><button class="btn btn-primary button3" name="search" id="search" onclick="BuscarBarril();"><span class="glyphicon glyphicon-search"></span></button></h5>
            </div>
            <div class="col-md-12 text-center  d-flex justify-content-center" >
               <div class="col-md-4 text-center  d-flex justify-content-center" >
                  <h5><label>  Etiqueta:   </label>
                     <input type="text" class="form-control" required="true" name="etiquetaA" id="etiquetaA" disabled>
                  </h5>
               </div>
               <div class="col-md-4 text-center  d-flex justify-content-center" >
                  <h5><label>  Tapa:   </label>
                     <input type="text" class="form-control" required="true" name="tapaA" id="tapaA" disabled>
                  </h5>
               </div>
               <div class="col-md-4 text-center  d-flex justify-content-center" >
                  <h5><label>  Litros:   </label>
                     <input type="text" class="form-control" required="true" name="litrosA" id="litrosA" disabled>
                  </h5>
               </div>
            </div>
            <div class="col-md-12 text-center  d-flex justify-content-center" >
               <div class="col-md-4 text-center  d-flex justify-content-center" >
                  <h5><label>  Revisado:   </label>
                     <input type="text" class="form-control" required="true" name="revisadoA" id="revisadoA" disabled>
                  </h5>
               </div>
               <div class="col-md-4 text-center  d-flex justify-content-center" >
                  <h5><label>  Relleno:   </label>
                     <input type="text" class="form-control" required="true" name="rellenoA" id="rellenoA" disabled>
                  </h5>
               </div>
               <div class="col-md-4 text-center  d-flex justify-content-center" >
                  <h5><label>  Año barrica:   </label>
                     <input type="text" class="form-control" required="true" name="aBarricaA" id="aBarricaA" disabled>
                  </h5>
               </div>
            </div>
            <div class="col-md-12 text-center  d-flex justify-content-center" >
               <div class="col-md-4 text-center  d-flex justify-content-center" >
                  <h5><label>  Ubicación:   </label>
                     <input rows="4" type="text" class="form-control" required="true" name="ubicacionA" id="ubicacionA" disabled>
                  </h5>
               </div>
               <div class="col-md-4 text-center  d-flex justify-content-center" >
                  <h5><label>  Estado:   </label>
                     <input type="text" class="form-control" required="true" name="estadoA" id="estadoA" disabled>
                  </h5>
               </div>
               <div class="col-md-4 text-center  d-flex justify-content-center" >
                  <h5><label>  Uso:   </label>
                     <input type="text" class="form-control" required="true" name="usoA" id="usoA" disabled>
                  </h5>
               </div>
            </div>
            <div class="col-md-12 text-center  d-flex justify-content-center" >
               <div class="col-md-4 text-center  d-flex justify-content-center" >
                  <h5><label>  Edad:   </label>
                     <input type="text" class="form-control" required="true" name="edadA" id="edadA" disabled>
                  </h5>
               </div>
               <div class="col-md-4 text-center  d-flex justify-content-center" >
                  <h5><label>  Alcohol:   </label>
                     <input type="text" class="form-control" required="true" name="alcoholA" id="alcoholA" disabled>
                  </h5>
               </div>
               <div class="col-md-4 text-center  d-flex justify-content-center" >
                  <h5><label>  Recepción:   </label>
                     <input type="text" class="form-control" required="true" name="recepcionA" id="recepcionA" disabled>
                  </h5>
               </div>
            </div>
            <div class="col-md-12 text-center  d-flex justify-content-center" >
               <div class="col-md-4 text-center">
                  <h5>
                     <label>  Motivo:   </label>
                     <select class="form-control" required="true" id="MotivoA" >
                     </select>
                  </h5>
               </div>
            </div>
            <div class="col-md-12 text-center  d-flex justify-content-center" >
               <button class="button2 btn btn-primary cerrar b-r-xl" >Cancelar</button>
               <button class="button2 btn btn-primary b-r-xl" onclick="GuardarAgregar();" >Guardar</button>
            </div>
         </div>
      </div>
      <div id="moverDialog" title="Mover barriles" class="animated">
         <div>
            <div class="col-md-12 text-center  d-flex justify-content-center" >
               <h5><label>  Bodega:  </label>
                  <select class="form-control" id="bodegaM" onchange="getInfo(this,'bodega','Costados','ID','CostadoM');">
                  </select>
               </h5>
               <h5><label>  Costado:   </label>
                  <select class="form-control " name="CostadoM"  id="CostadoM" onchange="getInfo(this,'area','Filas','ID','FilasM');">
                  </select>
               </h5>
            </div>
            <div class="col-md-12 text-center  d-flex justify-content-center" >
               <h5><label>  Filas:   </label>
                  <select class="form-control " name="FilasM" id="FilasM" onchange="getInfo(this,'fila','Torres','ID','TorresM');">
                  </select>
               </h5>
               <h5><label>  Torres:   </label>
                  <select class="form-control " name="TorresM"  id="TorresM" onchange="getInfo(this,'torre','Niveles','RackLocID','NivelesM');">
                  </select>
               </h5>
               <h5><label>  Niveles:   </label>
                  <select class="form-control" name="NivelesM" id="NivelesM" onchange="">
                  </select>
               </h5>
            </div>
            <div class="col-md-12 text-center  d-flex justify-content-center" >
               <div class="col-md-10 text-center">
                  <h5>
                     <label>  Motivo:   </label>
                     <select class="form-control" required="true" id="MotivoM" >
                     </select>
                  </h5>
               </div>
            </div>
            <div class="col-md-12 text-center  d-flex justify-content-center" >
               <div class="table-responsive col-md-12 centro" >
                  <table class="table table-bordered table-hover display" id="moverBarriles">
                     <thead>
                        <tr>
                           <th>Etiqueta(s) a mover</th>
                        </tr>
                     </thead>
                     <tbody></tbody>
                  </table>
               </div>
            </div>
            <div class="col-md-12 text-center  d-flex justify-content-center" >
               <button class="button2 btn btn-primary cerrar b-r-xl">Cancelar</button>
               <button class="button2 btn btn-primary b-r-xl" onclick="GuardarMover();">Guardar</button>
            </div>
         </div>
      </div>
      <div id="editarDialog" title="Editar barril" class="animated">
         <div class="col-md-12 text-center  d-flex justify-content-center" >
            <div class="col-md-4 text-center " >
               <h5><label>  Etiqueta:   </label>
                  <input type="text" class="form-control" required="true" name="etiqueta" id="etiqueta" disabled>
               </h5>
            </div>
            <div class="col-md-4 text-center " >
               <h5>
                  <label>  Uso:   </label>
                  <select class="form-control" required="true" name="uso" id="uso">
                     <?php
                        $uso = "select IdCodificacion,Codigo from CM_Codificacion";
                        $stmtUso = sqlsrv_query( $conn , $uso);
                        while( $row = sqlsrv_fetch_array( $stmtUso, SQLSRV_FETCH_NUMERIC))
                        {
                        ?>
                     <option value="<?php echo $row[0]?>"><?php echo utf8_encode($row[1])?></option>
                     <?php
                        }
                        sqlsrv_free_stmt( $stmtUso);
                        ?>
                  </select>
               </h5>
            </div>
            <div class="col-md-4 text-center " >
               <h5>
                  <label>  Edad:   </label>
                  <select class="form-control" required="true" name="edad" id="edad">
                     <?php
                        $edad = "select * from CM_Edad";
                        $stmtEdad = sqlsrv_query( $conn , $edad);
                        while( $row = sqlsrv_fetch_array( $stmtEdad, SQLSRV_FETCH_NUMERIC))
                        {
                        ?>
                     <option value="<?php echo $row[0]?>"><?php echo utf8_encode($row[1])?></option>
                     <?php
                        }
                        sqlsrv_free_stmt( $stmtEdad);
                        ?>
                  </select>
               </h5>
            </div>
         </div>
         <div class="col-md-12 text-center  d-flex justify-content-center" >
            <div class="col-md-4 text-center " >
               <h5><label>  Tapa:   </label>
                  <input type="text" class="form-control" required="true" name="tapa" id="tapa">
               </h5>
            </div>
            <div class="col-md-4 text-center " >
               <h5><label>  Litros:   </label>
                  <input type="text" class="form-control" required="true" name="litros" id="litros">
               </h5>
            </div>
            <div class="col-md-4 text-center " >
               <h5><label>  Revisado:   </label>
                  <input type="date" class="form-control" required="true" name="revisado" id="revisado">
               </h5>
            </div>
         </div>
         <div class="col-md-12 text-center  d-flex justify-content-center" >
            <div class="col-md-4 text-center " >
               <h5><label>  Relleno:   </label>
                  <input type="date" class="form-control" required="true" name="relleno" id="relleno">
               </h5>
            </div>
            <div class="col-md-4 text-center " >
               <h5><label>  Año barrica:   </label>
                  <input type="year" class="form-control" required="true" name="Abarrica" id="Abarrica" disabled>
               </h5>
            </div>
            <div class="col-md-4 text-center " >
               <h5>
                  <label>  Estado:   </label>
                  <select class="form-control" required="true" name="estado" id="estado">
                     <?php
                        $estados = "select IdEstado, Descripcion from CM_Estado";
                        $stmtEstados = sqlsrv_query( $conn , $estados);
                        while( $row = sqlsrv_fetch_array( $stmtEstados, SQLSRV_FETCH_NUMERIC))
                        {
                        ?>
                     <option value="<?php echo $row[0]?>"><?php echo utf8_encode($row[1])?></option>
                     <?php
                        }
                        sqlsrv_free_stmt( $stmtEstados);
                        ?>
                  </select>
               </h5>
            </div>
         </div>
         <div class="col-md-12 text-center  d-flex justify-content-center" >
            <div class="col-md-4 text-center" id="data_1">
               <h5>
                  <label>  Fecha de llenado:   </label>
                  <div class="input-group date">
                     <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control" name="LAlcohol" id="LAlcohol" onchange="CambiarLote();" data-date-format='yy-mm-dd'>
                  </div>
               </h5>
            </div>
            <div class="col-md-4 text-center" >
               <h5><label>  LLenado y Alcohol:   </label>
                  <select class="form-control" required="true" name="alcohol" id="alcohol" onchange="PonerAAlcohol();">
                  </select>
               </h5>
            </div>
            <div class="col-md-4 text-center" >
               <h5><label>  Año alcohol:   </label>
                  <input type="text" class="form-control" required="true" name="Aalcohol" id="Aalcohol" disabled>
               </h5>
            </div>
         </div>
         <div class="col-md-12 text-center  d-flex justify-content-center" >
            <div class="col-md-4 text-center">
               <h5>
                  <label>  Motivo:   </label>
                  <select class="form-control" required="true" id="MotivoE" >
                  </select>
               </h5>
            </div>
         </div>
         <div class="col-md-12 text-center" >
            <button class="button2 btn btn-primary cerrar b-r-xl" onclick="" id="cerrar">Cancelar</button>
            <button class="button2 btn btn-primary b-r-xl" onclick="GuardarEditar();">Guardar</button>
         </div>
      </div>
      </div>
      <!-- Mainly scripts -->
      <script src="js/jquery-3.1.1.min.js"></script>
      <script src="js/jquery-ui.js"></script>
      <script src="js/jquery.dataTables.min.js"></script>
      <script src="js/plugins/dataTables/datatables.min.js"></script>
      <script src="js/plugins/dataTables/dataTables.bootstrap4.min.js"></script>
      <script src="js/json2html.min.js"></script>
      <script src="js/popper.min.js"></script>
      <script src="js/bootstrap.js"></script>
      <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
      <script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
      <!-- Custom and plugin javascript -->
      <script src="js/inspinia.js"></script>
      <script src="js/plugins/pace/pace.min.js"></script>
      <script src="js/plugins/peity/jquery.peity.min.js"></script>
      <script src="js/plugins/rickshaw/vendor/d3.v3.js"></script>
      <script src="js/plugins/rickshaw/rickshaw.min.js"></script>
      <script src="js/plugins/datapicker/bootstrap-datepicker.js"></script>
      <script src="js/plugins/sweetalert/sweetalert.min.js"></script>
      <!-- Page-Level Scripts -->
      <script src="js/funciones_generales.js"></script>
      <script src="js/funciones_validar_informacion.js"></script>
   </body>
</html>
