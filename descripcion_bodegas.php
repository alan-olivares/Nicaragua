<?php
$almacen=$_GET["almacen"];
$area=$_GET["area"];
$codificacion=$_GET["codificacion"];
include 'general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){
  include 'RestApi/revisar_permisos.php';
  if(strpos($permisos,',10,') !== false){
    $BodegaNombre=getValor("SELECT Nombre from AA_Almacen where AlmacenID=".$almacen,$conn);
    $AreaNombre=getValor("SELECT Nombre from AA_Area where AreaId=".$area,$conn);
    ?>
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
    <body class="gray-bg">
      <script src="js/revisar_sesion.js"></script>
      <div class="row wrapper border-bottom white-bg page-heading" >
        <div class="col-md-12 text-center d-flex justify-content-center" >
          <div class="col-md-6">
            <h5><h4>Bodega: <h4 class="subrrayado"><?php echo $BodegaNombre; ?></h4></h4></h5>
          </div>
          <div class="col-md-6">
            <h5><h4>Area: <h4 class="subrrayado"><?php echo $AreaNombre; ?></h4></h4></h5>
          </div>
        </div>
        <div class="table-responsive">
          <h3 style="margin-top:20px;margin-left:10px;" id="edad">Edades</h3>
        <table class="table table-striped table-bordered table-hover" id="tabla1">
        <thead>


        <?php
        $tsql = "exec sp_ListaEdad";
        $stmt = sqlsrv_query( $conn , $tsql);
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC))
        {
        ?>
        <tr>
        <th style="text-align: center;" ><?php echo utf8_encode($row[0])?></th>
        <th style="text-align: center;" ><?php echo utf8_encode($row[1])?></th>
        <th style="text-align: center;" ><?php echo utf8_encode($row[2])?></th>
        <th style="text-align: center;" ><?php echo utf8_encode($row[3])?></th>
        <th style="text-align: center;"><?php echo utf8_encode($row[4])?></th>
        <th style="text-align: center;"><?php echo utf8_encode($row[5])?></th>
        <th style="text-align: center;"><?php echo utf8_encode($row[6])?></th>
        </tr>
        <?php
        }

        /* Free statement and connection resources. */
        sqlsrv_free_stmt( $stmt);
        ?>
        </thead>
        <tbody>
        </tbody>
        </table>
        </div>

        <div class="table-responsive col-md-12">
        <table class="funciones tabla"  id="tabla2">
        <thead>
          <tr>
          <th style="text-align: center;">Número</th>
          <th style="text-align: center;">Barril</th>
          <th style="text-align: center;">Año</th>
          <th style="text-align: center;">Fecha</th>
          <th style="text-align: center;">Dias barril</th>
          <th style="text-align: center;">Edad</th>
          <th style="text-align: center;">Estado</th>
          <th style="text-align: center;">Etiqueta</th>
          </tr>
          </thead>
          <tbody>
            <?php
            $tsql = "exec sp_BarrilPlantelDetalle '$almacen','$area','$codificacion'";

            $stmt = sqlsrv_query( $conn , $tsql);

            while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC))
            {
            ?>
            <tr>
              <td style="text-align: center;"><?php echo (int)$row[0]?></td>
              <td style="text-align: center;"><?php echo $row[3]?></td>
              <td style="text-align: center;"><?php echo $row[4]?></td>
              <td style="text-align: center;"><?php echo $row[5]?></td>
              <td style="text-align: center;"><?php echo (int)$row[6]?></td>
              <td style="text-align: center;"><?php echo $row[7]?></td>
              <td style="text-align: center;"><?php echo utf8_encode($row[8])?></td>
              <td style="text-align: center;"><?php echo $row[9]?></td>
            </tr>
            <?php
            }

            /* Free statement and connection resources. */
            sqlsrv_free_stmt( $stmt);
            sqlsrv_close( $conn);
            ?>
            </tbody>
            <tfoot>
              <tr>
                <th style="text-align: center;">Total barriles:</th>
                <th style="text-align: right;"></th>
              </tr>
            </tfoot>
            </table>
            </div>
            <a href="#" class="aDes floatDes btnDes" id="menu-share">
              <i class="fa fa-download my-float"></i>
            </a>
            <ul class="ulDes">
              <li><a href="javascript:generar('jpg','foto');" class="btnDes aDes">
                <i class="fa fa-file-image-o my-float" id="foto"></i>
                <div class="label-container">
                  <div class="label-text">JPG</div>
                  <i class="fa fa-play label-arrow"></i>
                </div>
              </a></li>
              <li><a href="javascript:generar('pdf','pdf');" class="btnDes aDes">
                <i class="fa fa-file-pdf-o my-float" id="pdf"></i>
                <div class="label-container">
                  <div class="label-text">PDF</div>
                  <i class="fa fa-play label-arrow"></i>
                </div>
              </a></li>
              <li><a href="javascript:generar('xlsx','excel');" class="btnDes aDes" >
                <i class="fa fa-file-excel-o my-float" id="excel"></i>
                <div class="label-container">
                  <div class="label-text">Excel</div>
                  <i class="fa fa-play label-arrow"></i>
                </div>
              </a></li>
            </ul>
      </div>

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
      <script src="js/exportar_csv.js"></script>
      <script src="js/funciones_generales.js"></script>

      <script src="js/plugins/ladda/spin.min.js"></script>
      <script src="js/plugins/ladda/ladda.min.js"></script>
      <script src="js/plugins/ladda/ladda.jquery.min.js"></script>

      <script>
      async function generar(tipo,boton){
        if($('#tabla2 tbody tr').length > 0){
          var l = $('#'+boton).ladda();
          l.ladda('start');
          var url=getNode+"?reporte=detalleBarrilPlantel&tipo="+tipo+"<?php echo "&almacen=$almacen&area=$area&cod=$codificacion";?>";
          var valor=await conexion("GET",url,"");
          let link = document.createElement("a");
          link.download = "Detalle de barril por plantel."+tipo;
          link.href = valor;
          link.click();
          l.ladda('stop');

        }else{
          mensajeError("No hay datos que exportar");
        }
      }
      function generate() {


         var doc = new jsPDF('p', 'pt', 'letter');
         var res1,res0;
         if($('#tabla1 tr').length > 0 || $('#tabla2 tr').length > 1){
         // first table
          res0 = doc.autoTableHtmlToJson(document.getElementById('tabla1'));
         //get the columns & rows for first table
         doc.text("Edades",50,70);
         doc.autoTable(res0.columns, res0.data, {tableWidth: 'auto',  columnWidth: 'auto',margin: {top: 80},styles: {overflow: 'linebreak'}});
         // second table
          res1 = doc.autoTableHtmlToJson(document.getElementById('tabla2'));
          var options = {
            tableWidth: 'auto',
            columnWidth: 'auto',
            margin: {
              top: 80
            },
            styles: {
              overflow: 'linebreak'
            },
            fontSize:6,
            startY: doc.autoTableEndPosY() + 20
           };
          doc.autoTable(res1.columns, res1.data, options);
          doc.save("Reporte Detalle Bodega.pdf");
         }else{
           window.alert("Las tablas estan vacias");
         }
       }
          $(document).ready(function(){
            $('#tabla2').DataTable( {"footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;

              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                  return typeof i === 'string' ?
                      i.replace(/[\$,]/g, '')*1 :
                      typeof i === 'number' ?
                          i : 0;
              };
              total= api.column( 1, { page: 'current'} ).data().reduce( function (a) {
                  return a+1;
              }, 0 );
              // Update footer

              $( api.column(1).footer() ).html(total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
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
              {extend: 'csv',text:'CSV',title: 'Reporte Detalle Bodega',footer:true,customize: function (csv) {
                var buffer = new ArrayBuffer(3);
                var dataView = new DataView(buffer);
                dataView.setUint8(0, 0xef);
                dataView.setUint8(1, 0xbb);
                dataView.setUint8(2, 0xbf);
                var read = new Uint8Array(buffer);
                var blob = new Blob([read, csv], {type: 'text/csv;charset=utf-8'});
                return blob;
              }},
              {extend: 'excel',text:'Excel', title: 'Reporte Detalle Bodega',footer:true, customize: function( xlsx ) {

                var sheet = xlsx.xl.worksheets['sheet1.xml'];
                //list of columns that should get a 1000 separator depending on local Excel installation
                $('row c[r^="A"]', sheet).attr( 's', '' );
                $('row c[r^="E"]', sheet).attr( 's', '' );
                $('row c[r^="G"]', sheet).attr( 's', '' );
                $('row c[r^="J"]', sheet).attr( 's', '' );
              }},
              {extend: 'pdf',text:'PDF', title: function(){ return "Reporte Detalle Bodega";},
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
    <?php
  }else{
    echo 'No tienes permisos para acceder a esta area';
  }
}else{
  echo 'La autenticación del usuario no se ha podido procesar';
}
function getValor($query,$conn){
  $stmt = sqlsrv_query( $conn , $query);
  $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
  return $row[0];
}
?>
