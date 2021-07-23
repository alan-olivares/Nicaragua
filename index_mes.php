<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title>Inicio</title>
      <link rel="icon" href="img\TBRE.ico">
      <link href="css/bootstrap.min.css" rel="stylesheet">
      <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
      <!-- Toastr style -->
      <link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">
      <!-- Gritter -->
      <link href="js/plugins/gritter/jquery.gritter.css" rel="stylesheet">
      <link href="css/animate.css" rel="stylesheet">
      <link href="css/style.css" rel="stylesheet">
   </head>
   <body>
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
               <li class="active">
                  <a href="index.php"><i class="fa fa-area-chart"></i> <span class="nav-label">Operación del día</span></a>
               </li>
               <li>
                  <a href="#"><i class="fa fa-table"></i> <span class="nav-label">Reportes</span><span class="fa arrow"></span></a>
                  <ul class="nav nav-second-level">
                     <li><a href="inventario.php">Inventario</a></li>
                     <li><a href="barriles_plantel.php">Barriles en plantel</a></li>
                     <li><a href="llenados.php">Barriles llenados</a></li>
                     <li><a href="rellenados.php">Barriles rellenados</a></li>
                     <li><a href="trasiego.php">Barriles trasegados</a></li>
                     <li><a href="reparacion.php">Barriles reparados</a></li>
                  </ul>
               </li>
               <li class="dos-cinco">
                  <a href="#"><i class="fa fa-desktop"></i> <span class="nav-label">TBRE</span><span class="fa arrow"></span></a>
                  <ul class="nav nav-second-level">
                     <li class="dos"><a href="validar_informacion.php">Validar información</a></li>
                     <li class="cinco"><a href="impresion.html">Impresión de etiquetas</a></li>
                  </ul>
               </li>
               <li class="seis">
                  <a href="#"><i class="fa fa-briefcase"></i> <span class="nav-label">Órdenes de trabajo</span><span class="fa arrow"></span></a>
                  <ul class="nav nav-second-level">
                     <li><a href="OLlenado.html">Llenado</a></li>
                     <li><a href="ORelleno.html">Relleno</a></li>
                     <li><a href="revision.html">Revisión</a></li>
                     <li><a href="OTrasiego.html">Trasiego</a></li>
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
               <h2 class="nav navbar-top-links titulo">TBRE - Operación del día</h2>
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
                     <a onclick="javascript:limpiar();">
                     <i class="fa fa-sign-out"></i> Cerrar sesión
                     </a>
                  </li>
               </ul>
            </nav>
         </div>
         <div class="row text-center border-bottom white-bg dashboard-header d-flex justify-content-center">
            <div class="" style="float:right;">
               <button class="button4 btn-outline btn btn-primary" onclick="window.location.href='index.php';">Día</button>
               <button class="button4 btn-outline btn btn-primary" onclick="window.location.href='index_semana.php';">Semana</button>
               <button class="button4 btn btn-primary" >Mes</button>
            </div>
            <div class="col-md-12 ">
               <div class="">
                  <form class="form-inline text-center d-flex justify-content-center" method="POST" action="">
                     <h5><label>  Mes:  </label>
                        <input class="form-control" type="month" placeholder="Selecciona el mes"  name="date1" id="date1"/>
                     </h5>
                     <button class="btn btn-primary button3" name="search"><span class="glyphicon glyphicon-search"></span></button>
                  </form>
               </div>
               <div class="flot-chart dashboard-chart ">
                  <div class="flot-chart-content " id="flot-dashboard-chart"></div>
               </div>
               <div class="row text-center">
                  <div class="col">
                     <div class=" m-l-md">
                        <span class="h5 font-bold m-t block" style="color:#1ab394" id="BLLT">0</span>
                        <h5 class="text-muted m-b block">Barriles llenados</h5>
                     </div>
                  </div>
                  <div class="col">
                     <span class="h5 font-bold m-t block" style="color:#1C84C6" id="BRT">0</span>
                     <h5 class="text-muted m-b block">Barriles rellenados</h5>
                  </div>
                  <div class="col">
                     <span class="h5 font-bold m-t block" style="color:#f8ac59" id="BTT">0</span>
                     <h5 class="text-muted m-b block">Barriles trasegados</h5>
                  </div>
                  <div class="col">
                     <span class="h5 font-bold m-t block" style="color:#ed5565" id="BRE">0</span>
                     <h5 class="text-muted m-b block">Barriles reparados</h5>
                  </div>
               </div>
            </div>
         </div>
         <div class="wrapper wrapper-content">
            <div class="footer">
               <div>
                  <strong>Todos los derechos reservados | </strong>SER Licorera 2014
               </div>
            </div>
         </div>
      </div>
      <!-- Mainly scripts -->
      <script src="js/jquery-3.1.1.min.js"></script>
      <script src="js/popper.min.js"></script>
      <script src="js/bootstrap.js"></script>
      <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
      <script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
      <!-- Flot -->
      <script src="js/plugins/flot/jquery.flot.js"></script>
      <script src="js/plugins/flot/jquery.flot.tooltip.min.js"></script>
      <script src="js/plugins/flot/jquery.flot.spline.js"></script>
      <script src="js/plugins/flot/jquery.flot.resize.js"></script>
      <script src="js/plugins/flot/jquery.flot.pie.js"></script>
      <!-- Toastr -->
      <script src="js/plugins/toastr/toastr.min.js"></script>
      <!-- Peity -->
      <script src="js/plugins/peity/jquery.peity.min.js"></script>
      <script src="js/demo/peity-demo.js"></script>
      <!-- Custom and plugin javascript -->
      <script src="js/inspinia.js"></script>
      <script src="js/plugins/pace/pace.min.js"></script>
      <!-- jQuery UI -->
      <script src="js/plugins/jquery-ui/jquery-ui.min.js"></script>
      <!-- GITTER -->
      <script src="js/plugins/gritter/jquery.gritter.min.js"></script>
      <!-- Sparkline -->
      <script src="js/plugins/sparkline/jquery.sparkline.min.js"></script>
      <!-- Sparkline demo data  -->
      <script src="js/demo/sparkline-demo.js"></script>
      <!-- ChartJS-->
      <script src="js/plugins/chartJs/Chart.min.js"></script>
      <script src="js/funciones_generales.js"></script>
      <script>var data1= []; var data2=[]; var data3=[];var data4=[];</script>
      <?php
         if(ISSET($_POST['date1'])){
           $date1 = date("Y-m", strtotime($_POST['date1']));
           $lastDay=cal_days_in_month(CAL_GREGORIAN, (int)date("m", strtotime($_POST['date1'])), (int)date("Y", strtotime($_POST['date1'])))
           ?>
      <script type="text/javascript">
         document.getElementById("date1").valueAsDate = new Date('<?php echo $date1;?>');
      </script>
      <?php
         }else{
           $date1 = date("Y-m");
           $lastDay=cal_days_in_month(CAL_GREGORIAN, (int)date("m"), (int)date("Y"))
           ?>
      <script type="text/javascript">
         document.getElementById("date1").valueAsDate = new Date();
         setTimeout(function() {
             toastr.options = {
                 closeButton: true,
                 progressBar: true,
                 showMethod: 'slideDown',
                 timeOut: 4000
             };
             var today  = new Date();
             var options = {hour: 'numeric',minute:'numeric' , year: "numeric", month: "long", day: "numeric"};
             toastr.success('Información actualizada del día '+today.toLocaleDateString("es-MX",options));

         }, 1300);
      </script>
      <?php
         }
         $hora1= $date1."-01 00:00";
         $hora2= $date1."-".$lastDay." 23:59";
         include'general_connection.php';
         $llenados = "select SUBSTRING(CONVERT(CHAR(10), l.fecha, 120), 9, 2) as dia, count(distinct idbarrica) as Barriles
         from adm_logregbarril l inner join PR_RegBarril r on r.idregbarril=l.IdregBarril
         where l.fecha between '$hora1' and '$hora2' and r.TipoReg=1
         group by SUBSTRING(CONVERT(CHAR(10), l.fecha, 120),  9, 2) order by SUBSTRING(CONVERT(CHAR(10), l.fecha, 120),  9, 2)";
         $rellenados ="select SUBSTRING(CONVERT(CHAR(10), l.fecha, 120),  9, 2) as dia, count(distinct idbarrica) as Barriles
         from adm_logregbarril l inner join PR_RegBarril r on r.idregbarril=l.IdregBarril
         where l.fecha between '$hora1' and '$hora2' and r.TipoReg in (2,4,5)
         group by SUBSTRING(CONVERT(CHAR(10), l.fecha, 120),  9, 2) order by SUBSTRING(CONVERT(CHAR(10), l.fecha, 120),  9, 2)";
         $trasiegos="select SUBSTRING(CONVERT(CHAR(10), l.fecha, 120),  9, 2) as dia, count(distinct idbarrica) as Barriles
         from adm_logregbarril l inner join PR_RegBarril r on r.idregbarril=l.IdregBarril
         where l.fecha between '$hora1' and '$hora2' and r.TipoReg=3
         group by SUBSTRING(CONVERT(CHAR(10), l.fecha, 120),  9, 2) order by SUBSTRING(CONVERT(CHAR(10), l.fecha, 120),  9, 2)";
         $reparados="SELECT SUBSTRING(CONVERT(CHAR(10), L.fecha, 120),  9, 2) as Hora, count(distinct M.idbarrica) as Barriles
         FROM PR_Mantenimiento M INNER JOIN ADM_logMantenimiento L ON M.IdMantenimiento=L.IdMantenimiento
         WHERE L.TipoOp='I' AND M.Fecha between '$hora1' and '$hora2'
         group by SUBSTRING(CONVERT(CHAR(10), L.fecha, 120),  9, 2) order by SUBSTRING(CONVERT(CHAR(10), l.fecha, 120),  9, 2)";

         $llenadosTotal="select count(distinct idbarrica)
         from adm_logregbarril l inner join PR_RegBarril r on r.idregbarril=l.IdregBarril
         where l.fecha between '$hora1' and '$hora2' and r.TipoReg=1";
         $rellenadosTotal="select count(distinct idbarrica)
         from adm_logregbarril l inner join PR_RegBarril r on r.idregbarril=l.IdregBarril
         where l.fecha between '$hora1' and '$hora2' and r.TipoReg in (2,4,5)";
         $trasiegosTotal="select count(distinct idbarrica)
         from adm_logregbarril l inner join PR_RegBarril r on r.idregbarril=l.IdregBarril
         where l.fecha between '$hora1' and '$hora2' and r.TipoReg=3";
         $reparadosTotal="SELECT count(distinct M.idbarrica)
         FROM PR_Mantenimiento M INNER JOIN ADM_logMantenimiento L ON M.IdMantenimiento=L.IdMantenimiento
         WHERE L.TipoOp='I' AND M.Fecha between '$hora1' and '$hora2'";

         $stmt = sqlsrv_query( $conn , $llenados);
         $stmt2 = sqlsrv_query( $conn , $rellenados);
         $stmt3 = sqlsrv_query( $conn , $trasiegos);
         $stmt4 = sqlsrv_query( $conn , $reparados);

         $stmtLlenadosTotal = sqlsrv_query( $conn , $llenadosTotal);
         $stmtRellenadosTotal = sqlsrv_query( $conn , $rellenadosTotal);
         $stmtTrasiegosTotal = sqlsrv_query( $conn , $trasiegosTotal);
         $stmtReparadosTotal = sqlsrv_query( $conn , $reparadosTotal);

         $TotalRe = sqlsrv_fetch_array( $stmtLlenadosTotal, SQLSRV_FETCH_NUMERIC);
         $TotalLL = sqlsrv_fetch_array( $stmtRellenadosTotal, SQLSRV_FETCH_NUMERIC);
         $TotalTr = sqlsrv_fetch_array( $stmtTrasiegosTotal, SQLSRV_FETCH_NUMERIC);
         $TotalRep = sqlsrv_fetch_array( $stmtReparadosTotal, SQLSRV_FETCH_NUMERIC);

         ?>
      <script>
         document.getElementById("BLLT").innerHTML = <?php echo $TotalRe[0]?>;
         document.getElementById("BRT").innerHTML = <?php echo $TotalLL[0]?>;
         document.getElementById("BTT").innerHTML = <?php echo $TotalTr[0]?>;
         document.getElementById("BRE").innerHTML = <?php echo $TotalRep[0]?>;
      </script>
      <?php
         while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC))
         {
           ?>
      <script> data1.push([<?php echo (int)$row[0]?>,<?php echo (int)$row[1]?>])</script>
      <?php
         }
         while( $row = sqlsrv_fetch_array( $stmt2, SQLSRV_FETCH_NUMERIC))
         {
           ?>
      <script> data2.push([<?php echo (int)$row[0]?>,<?php echo (int)$row[1]?>])</script>
      <?php
         }
         while( $row = sqlsrv_fetch_array( $stmt3, SQLSRV_FETCH_NUMERIC))
         {
           ?>
      <script> data3.push([<?php echo (int)$row[0]?>,<?php echo (int)$row[1]?>])</script>
      <?php
         }
         while( $row = sqlsrv_fetch_array( $stmt4, SQLSRV_FETCH_NUMERIC))
         {
           ?>
      <script> data4.push([<?php echo (int)$row[0]?>,<?php echo (int)$row[1]?>])</script>
      <?php
         }

         /* Free statement and connection resources. */
         sqlsrv_free_stmt( $stmt);
         sqlsrv_close( $conn);
         ?>
      <script src="js/abrir_ventana.js"></script>
      <script>
         var today2  = new Date();
         var options2 = {year: "numeric", month: "long"};
         //document.getElementById("fechaActual").innerHTML = today2.toLocaleDateString("es-MX",options2);

         $(document).ready(function() {

             var dataset = [
               { label: "Barriles llenados", data: data1 },
               { label: "Barriles rellenados", data: data2},
               { label: "Barriles trasegados", data: data3},
               { label: "Barriles reparados", data: data4},
             ];
             var ticks =[];
             var d = new Date(document.getElementById("date1").valueAsDate);
             for(var i=1;i<=new Date(d.getFullYear(), d.getMonth()+2, 0).getDate();i++){
               ticks.push([i,i]);
             }


             $("#flot-dashboard-chart").length && $.plot($("#flot-dashboard-chart"), dataset,
             {
               series: {
                 lines: {
                   show: false,
                   fill: true
                 },
                 splines: {
                   show: true,
                   tension: 0.4,
                   lineWidth: 1,
                   fill: 0.4
                 },
                 points: {
                   radius: 3,
                   show: true
                 },
                 shadowSize: 1,
               },
               grid: {
                 hoverable: true,
                 clickable: true,
                 tickColor: "#d5d5d5",
                 borderWidth: 1,
                 color: '#858786'
               },
               colors: ["#1ab394", "#1C84C6","#f8ac59","#ed5565"],
               xaxis:{
                 min:1,
                 max:new Date(d.getFullYear(), d.getMonth()+2, 0).getDate(),
                 ticks:ticks,
               },
               yaxis: {
                 ticks: 5
               },
               tooltip: true,
               tooltipOpts : {
                 content : function (label, x, y) {
                   return  x + " de "+(new Date(d.getFullYear(), d.getMonth()+2, 0).toLocaleDateString("es-MX",options2))+"- " + y+" "+label;
                 },
                 defaultTheme : true
               },
             }
           );
           $("#flot-dashboard-chart").on("plotclick",function(event,pos,item){
             if(item){
               var date = new Date(document.getElementById("date1").valueAsDate);
               date=date.addDays(item.series.data[item.dataIndex][0]);
               var dia=date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate();
               switch (item.series.label) {
                 case "Barriles llenados":
                   abrir("descripcion_dia.php?dia="+dia+"&hora1="+dia+" 00:00&hora2="+dia+" 23:59&evento=1&tipo=Barriles llenados");
                 break;
                 case "Barriles rellenados":
                   abrir("descripcion_dia.php?dia="+dia+"&hora1="+dia+" 00:00&hora2="+dia+" 23:59&evento=2,4,5&tipo=Barriles rellenados");
                 break;
                 case "Barriles trasegados":
                   abrir("descripcion_dia.php?dia="+dia+"&hora1="+dia+" 00:00&hora2="+dia+" 23:59&evento=3&tipo=Barriles trasegados");
                 break;
                 case "Barriles reparados":
                   abrir("descripcion_dia.php?dia="+dia+"&hora1="+dia+" 00:00&hora2="+dia+" 23:59&evento=10&tipo=Barriles reparados");
                 break;
                 default:

               }

             }
           });
           Date.prototype.addDays = function(days) {
             var date = new Date(this.valueOf());
             date.setDate(date.getDate() + days);
             return date;
           }
               /*var previousPoint = null;
         $("#flot-dashboard-chart").bind("plothover", function (event, pos, item) {
           if (item) {
               if (previousPoint != item.dataIndex) {
                   previousPoint = item.dataIndex;
                   $("#tooltip").remove();
                   var x = item.datapoint[0]-6;

                   showTooltip(item.pageX, item.pageY,
                                item.series.xaxis.ticks[x]['label'] + " - " + item.series.data[previousPoint][1] + " " + item.series.label);
               }
           }else {
               $("#tooltip").remove();
               previousPoint = null;
           }
         });
         function showTooltip(x, y, contents) {
           $('<div id="tooltip">' + contents + '</div>').css( {
               position: 'absolute',
               display: 'none',
               top: y + 10,
               left: x + 10,
               border: '1px solid #fdd',
               padding: '2px',
               'background-color': '#fff',
               'color': '#000',
               'border': '1px solid #333',
               opacity: 0.80
           }).appendTo("body").fadeIn(200);
         }
         */


           });
      </script>
   </body>
</html>
