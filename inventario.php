<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reportes</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link rel="icon" href="img\TBRE.ico">

    <link href="css/plugins/dataTables/datatables.min.css" rel="stylesheet">

    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
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
                            <span class="block m-t-xs font-bold"><script type="text/javascript">
                              document.write(localStorage['nombre'] || 'Sin nombre');
                              var foto=localStorage['nombre'] || 'Sin nombre';
                              foto=foto.charAt(0).concat(".png").toLowerCase();
                              document.getElementById("perfil1").src="img/letras/"+foto;
                            </script></span>
                        </a>
                    </div>
                    <div class="logo-element">
                        TBRE
                    </div>
                </li>
                <li>
                    <a href="index.php"><i class="fa fa-th-large"></i> <span class="nav-label">Operación del día</span></a>
                </li>
                <li class="active">
                    <a href="#"><i class="fa fa-table"></i> <span class="nav-label">Reportes</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li class="active"><a href="inventario.php">Inventario</a></li>
                        <li><a href="barriles_plantel.php">Barriles en plantel</a></li>
                        <li><a href="llenados.php">Barriles llenados</a></li>
                        <li><a href="rellenados.php">Barriles rellenados</a></li>
                        <li><a href="trasiego.php">Barriles trasegados</a></li>
                        <li><a href="reparacion.php">Barriles reparados</a></li>
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
            <h2 class="nav navbar-top-links navbar-right">Inventario</h2>
            <ul class="nav navbar-top-links navbar-right">
                <li>
                    <a onclick="javascript:limpiar();">
                        <i class="fa fa-sign-out"></i> Cerrar sesión
                    </a>
                </li>
            </ul>

        </nav>
        </div>
        <?php
        include'general_connection.php';
        $bodegas = "Select AlmacenId,Nombre from AA_Almacen order by Nombre";
        $Alcohol = "Select IdAlcohol,Descripcion from CM_Alcohol Where IdAlcohol <> 4 order by Descripcion";
        $Allenada = "select Distinct YEAR(Recepcion) from PR_Lote order by YEAR(Recepcion)";
        $Uso = "Select IdCodificacion,Codigo from CM_Codificacion order by Codigo";
        $stmtBodegas = sqlsrv_query( $conn , $bodegas);
        $stmtAlcohol = sqlsrv_query( $conn , $Alcohol);
        $stmtAllenada = sqlsrv_query( $conn , $Allenada);
        $stmtUso = sqlsrv_query( $conn , $Uso);
        ?>
            <div class="row wrapper border-bottom white-bg page-heading" >
              <div class="col-md-8 text-center  d-flex justify-content-center" >
              <form class="form-inline text-center d-flex justify-content-center" method="POST" action="">
                <h5><label>  Bodega:  </label>
                  <select class="form-control" name="bodega" id="bodega">
                    <option value="">Todos</option>
                    <?php
                    while( $row = sqlsrv_fetch_array( $stmtBodegas, SQLSRV_FETCH_NUMERIC))
                    {
                    ?>
                    <option value="<?php echo $row[0]?>"><?php echo $row[1]?></option>
                    <?php
                    }
                    sqlsrv_free_stmt( $stmtBodegas);
                    ?>
                  </select></h5>
                <h5><label>  Alcohol:   </label>
                  <select class="form-control" name="alcohol" id="alcohol">
                    <option value="">Todos</option>
                    <?php
                    while( $row = sqlsrv_fetch_array( $stmtAlcohol, SQLSRV_FETCH_NUMERIC))
                    {
                    ?>
                    <option value="<?php echo $row[0]?>"><?php echo $row[1]?></option>
                    <?php
                    }
                    sqlsrv_free_stmt( $stmtAlcohol);
                    ?>
                  </select></h5>
                  <h5><label>  Año llenada:   </label>
                    <select class="form-control" name="llenada" id="llenada">
                      <option value="">Todos</option>
                      <?php
                      while( $row = sqlsrv_fetch_array( $stmtAllenada, SQLSRV_FETCH_NUMERIC))
                      {?>
                      <option value="<?php echo $row[0]?>"><?php echo $row[0]?></option>
                      <?php
                      }
                      sqlsrv_free_stmt( $stmtAllenada);
                      ?>
                    </select></h5>
                    <h5><label>  Uso:   </label>
                      <select class="form-control" name="uso" id="uso">
                        <option value="">Todos</option>
                        <?php
                        while( $row = sqlsrv_fetch_array( $stmtUso, SQLSRV_FETCH_NUMERIC))
                        {?>
                        <option value="<?php echo $row[0]?>"><?php echo $row[1]?></option>
                        <?php
                        }
                        sqlsrv_free_stmt( $stmtUso);
                        sqlsrv_close( $conn);
                        ?>
                      </select></h5>
                <button class="btn btn-primary button3" name="search"><span class="glyphicon glyphicon-search"></span></button>
              </form>
              </div>
              <div class="col-md-4 text-center" >
                <button class="button2 btn btn-primary" onclick="javascript:generate();">PDF</button>
                <button class="button2 btn btn-primary" onclick="exportTableToCSV('Inventario.csv')">CSV</button>
              </div>
              <?php include'connection_inventario.php'?>
            </div>



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

    <script src="js/plugins/dataTables/datatables.min.js"></script>
    <script src="js/plugins/dataTables/dataTables.bootstrap4.min.js"></script>
    <!-- Custom and plugin javascript -->
    <script src="js/inspinia.js"></script>
    <script src="js/plugins/pace/pace.min.js"></script>


    <!-- Page-Level Scripts -->
    <script>

    function limpiar() {
      localStorage['sesion_timer']="";
      window.location.replace("login.php");
    }

        $(document).ready(function(){
            $('#tabla2').DataTable({
                responsive: true,
                "bPaginate": false,
                "paging":   false,
                "ordering": false,
                "info":     false,
                dom: '<"html5buttons"B>lTfgitp',
                language: {
                  searchPlaceholder: "Busca algún dato aquí",
                  search: "",
                },
                buttons: [
                    { extend: 'copy',text:'Copiar'},
                    {extend: 'csv',text:'CSV',title: 'Inventario',customize: function (csv) {
                      var buffer = new ArrayBuffer(3);
                      var dataView = new DataView(buffer);
                      dataView.setUint8(0, 0xef);
                      dataView.setUint8(1, 0xbb);
                      dataView.setUint8(2, 0xbf);
                      var read = new Uint8Array(buffer);
                      var blob = new Blob([read, csv], {type: 'text/csv;charset=utf-8'});
                      return blob;
                    }},
                    {extend: 'excel',text:'Excel', title: 'Inventario', customize: function( xlsx ) {

                      var sheet = xlsx.xl.worksheets['sheet1.xml'];
                      //list of columns that should get a 1000 separator depending on local Excel installation
                      $('row c[r^="A"]', sheet).attr( 's', '' );
                    }},
                    {extend: 'pdf',text:'PDF', title: function(){ return "Inventario";},
                    footer:true,customize:function(doc){
                      doc.styles.tableHeader.fillColor='#1ab394';
                      doc.styles.title.alignment='left';
                      doc.styles.tableFooter.fillColor='#1ab394';
                      doc.styles.tableBodyEven ={alignment: 'center'};
                      doc.styles.tableBodyOdd ={alignment: 'center'};
                    }},

                    {extend: 'print',text:'Imprimir',
                     customize: function (win){
                            $(win.document.body).addClass('white-bg');
                            $(win.document.body).css('font-size', '10px');

                            $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', 'inherit');
                    }
                    }
                ]

            });
            $('#tabla3').DataTable( {"footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;

              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                  return typeof i === 'string' ?
                      i.replace(/[\$,]/g, '')*1 :
                      typeof i === 'number' ?
                          i : 0;
              };
              a = api.column( 6, { page: 'current'} ).data().reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 );
              b= api.column( 7, { page: 'current'} ).data().reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 );
              c = api.column( 8, { page: 'current'} ).data().reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 );
              d= api.column( 9, { page: 'current'} ).data().reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 );
              rc = api.column( 10, { page: 'current'} ).data().reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 );
              e= api.column( 11, { page: 'current'} ).data().reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 );
              f = api.column( 12, { page: 'current'} ).data().reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 );
              totalbarriles= api.column( 13, { page: 'current'} ).data().reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 );
              totalLitros= api.column( 14, { page: 'current'} ).data().reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 );
              // Update footer
              $( api.column(6).footer() ).html(a.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
              $( api.column(7).footer() ).html(b.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
              $( api.column(8).footer() ).html(c.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
              $( api.column(9).footer() ).html(d.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
              $( api.column(10).footer() ).html(rc.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
              $( api.column(11).footer() ).html(e.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
              $( api.column(12).footer() ).html(f.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
              $( api.column(13).footer() ).html(totalbarriles.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
              $( api.column(14).footer() ).html((Math.round(totalLitros * 100) / 100).toFixed(3).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
          },
          responsive: true,
          "bPaginate": false,
          "paging":   false,
          "ordering": false,
          "info":     false,
          dom: '<"html5buttons"B>lTfgitp',
          language: {
            searchPlaceholder: "Busca algún dato aquí",
            search: "",
          },
          buttons: [
              { extend: 'copy',text:'Copiar'},
              {extend: 'csv',text:'CSV',title: 'Detalle Inventario',footer:true,customize: function (csv) {
                var buffer = new ArrayBuffer(3);
                var dataView = new DataView(buffer);
                dataView.setUint8(0, 0xef);
                dataView.setUint8(1, 0xbb);
                dataView.setUint8(2, 0xbf);
                var read = new Uint8Array(buffer);
                var blob = new Blob([read, csv], {type: 'text/csv;charset=utf-8'});
                return blob;
              }},
              {extend: 'excel',text:'Excel', title: 'Detalle Inventario',footer:true, customize: function( xlsx ) {

                var sheet = xlsx.xl.worksheets['sheet1.xml'];
                //list of columns that should get a 1000 separator depending on local Excel installation
                $('row c[r^="A"]', sheet).attr( 's', '' );
                $('row c[r^="D"]', sheet).attr( 's', '' );
              }},
              {extend: 'pdf',text:'PDF', title: function(){ return "Detalle Inventario";},
              footer:true,customize:function(doc){
                doc.styles.tableHeader.fillColor='#1ab394';
                doc.styles.title.alignment='left';
                doc.styles.tableFooter.fillColor='#1ab394';
                doc.styles.tableBodyEven ={alignment: 'center'};
                doc.styles.tableBodyOdd ={alignment: 'center'};
              }},

              {extend: 'print',text:'Imprimir',
               customize: function (win){
                      $(win.document.body).addClass('white-bg');
                      $(win.document.body).css('font-size', '10px');

                      $(win.document.body).find('table')
                              .addClass('compact')
                              .css('font-size', 'inherit');
              }
              }
          ]
      } );

        });


    </script>

</body>

</html>
