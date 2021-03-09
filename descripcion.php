<?php
$almacen=$_GET["almacen"];
$area=$_GET["area"];
$seccion=$_GET["seccion"];
$alcohol=$_GET["alcohol"];
$fecha=$_GET["fecha"];
$codificacion=$_GET["codificacion"];

include'general_connection.php';
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
    <div class="col-md-12 text-center" >
      <button class="button2 btn btn-primary b-r-xl" onclick="javascript:generate();">PDF</button>
      <button class="button2 btn btn-primary b-r-xl" onclick="exportTableToCSV('Inventario.csv')">CSV</button>
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
    <table class="funciones tabla" id="tabla2">
    <thead>
      <tr>
      <th style="text-align: center;">Número</th>
      <th style="text-align: center;">Bodega</th>
      <th style="text-align: center;">Costado</th>
      <th style="text-align: center;">Fila</th>
      <th style="text-align: center;">Torre</th>
      <th style="text-align: center;">Nivel</th>
      <th style="text-align: center;">Lote</th>
      <th style="text-align: center;">Alcohol</th>
      <th style="text-align: center;">Días Alcohol</th>
      <th style="text-align: center;">Uso</th>
      <th style="text-align: center;">Fecha Barril</th>
      <th style="text-align: center;">Dias Barril</th>
      <th style="text-align: center;">Edad</th>
      <th style="text-align: center;">Fecha Revisado</th>
      <th style="text-align: center;">Fecha Relleno</th>
      <th style="text-align: center;">Tapa</th>
      <th style="text-align: center;">Capacidad Inicial</th>
      <th style="text-align: center;">Etiqueta</th>
      </tr>
      </thead>
      <tbody>
        <?php
        $tsql = "exec sp_InfoBarrilDetalle3 '$almacen','$area','$seccion','$alcohol','$codificacion','$fecha'";

        $stmt = sqlsrv_query( $conn , $tsql);

        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC))
        {
        ?>
        <tr>
          <td style="text-align: center;"><?php echo (int)$row[0]?></td>
          <td style="text-align: center;"><?php echo $row[1]?></td>
          <td style="text-align: center;"><?php echo $row[2]?></td>
          <td style="text-align: center;"><?php echo $row[3]?></td>
          <td style="text-align: right;"><?php echo (int)$row[4]?></td>
          <td style="text-align: center;"><?php echo (int)$row[5]?></td>
          <td style="text-align: center;"><?php echo (int)$row[6]?></td>
          <td style="text-align: center;"><?php echo $row[7]?></td>
          <td style="text-align: right;"><?php echo (int)$row[8]?></td>
          <td style="text-align: center;"><?php echo $row[9]?></td>
          <td style="text-align: center;"><?php if($row[10]==null){echo '';}else{echo $row[10]->format('Y-m-d');}?></td>
          <td style="text-align: right;"><?php echo $row[11]?></td>
          <td style="text-align: center;"><?php echo $row[12]?></td>
          <td style="text-align: center;"><?php if($row[13]==null){echo '';}else{echo $row[13]->format('Y-m-d');}?></td>
          <td style="text-align: center;"><?php if($row[14]==null){echo '';}else{echo $row[14]->format('Y-m-d');}?></td>
          <td style="text-align: center;"><?php echo $row[15]?></td>
          <td style="text-align: right;"><?php echo number_format((float)str_replace(",","",$row[16]), 3, '.', ',')?></td>
          <td style="text-align: center;"><?php echo $row[19]?></td>
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
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></th>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <th style="text-align: center;">Totales:</th>
            <th style="text-align: right;"></th>
            <td style="text-align: right;"></td>
            <th style="text-align: right;"></th>
            <td style="text-align: center;"></td>
          </tr>
        </tfoot>
        </table>
        </div>
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.4.1/jspdf.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/2.3.5/jspdf.plugin.autotable.min.js"></script>
  <script src="https://www.YourSite.com/wp-content/JS/table-export.js"></script>
  <script src="js/exportar_csv.js"></script>

  <script>
  function generate() {

     var doc = new jsPDF('p', 'pt', 'letter');
     var res1,res0;
     if($('#tabla1 tr').length > 0 || $('#tabla2 tr').length > 1){
     // first table
      res0 = doc.autoTableHtmlToJson(document.getElementById('tabla1'));
     //get the columns & rows for first table
     doc.text("Edades",50,70);
     doc.autoTable(res0.columns, res0.data, {theme: 'grid',tableWidth: 'auto',  columnWidth: 'auto',margin: {top: 80},styles: {overflow: 'linebreak'}});
     // second table
      res1 = doc.autoTableHtmlToJson(document.getElementById('tabla2'));
      var options = {
        tableWidth: 'auto',
        theme: 'grid',
        columnWidth: 'auto',
        margin: {
          top: 80
        },
        styles: {
          overflow: 'linebreak'
        },
        fontSize:6,
        startY: doc.autoTableEndPosY() + 20,
        columnStyles: {
          16: {
            fontStyle: 'bold',
            halign: 'right',
          }
        }
       };
      doc.autoTable(res1.columns, res1.data, options);
      doc.save("Inventario.pdf");
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
            totalLitros= api.column( 16, { page: 'current'} ).data().reduce( function (a, b) {
                return intVal(a) + intVal(b);
            }, 0 );
            total= api.column( 16, { page: 'current'} ).data().reduce( function (a) {
                return a+1;
            }, 0 );
            // Update footer

            $( api.column(14).footer() ).html(total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
            $( api.column(16).footer() ).html((Math.round(totalLitros * 100) / 100).toFixed(3).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
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
            {extend: 'csv',text:'CSV',title: 'Inventario',footer:true,customize: function (csv) {
              var buffer = new ArrayBuffer(3);
              var dataView = new DataView(buffer);
              dataView.setUint8(0, 0xef);
              dataView.setUint8(1, 0xbb);
              dataView.setUint8(2, 0xbf);
              var read = new Uint8Array(buffer);
              var blob = new Blob([read, csv], {type: 'text/csv;charset=utf-8'});
              return blob;
            }},
            {extend: 'excel',text:'Excel', title: 'Inventario',footer:true, customize: function( xlsx ) {

              var sheet = xlsx.xl.worksheets['sheet1.xml'];
              //Lista de columnas que se le quitan el formato en excel
              $('row c[r^="A"]', sheet).attr( 's', '' );
              $('row c[r^="D"]', sheet).attr( 's', '' );
              $('row c[r^="E"]', sheet).attr( 's', '' );
              $('row c[r^="F"]', sheet).attr( 's', '' );
              $('row c[r^="G"]', sheet).attr( 's', '' );
              $('row c[r^="H"]', sheet).attr( 's', '' );
              $('row c[r^="I"]', sheet).attr( 's', '' );
              $('row c[r^="L"]', sheet).attr( 's', '' );
              $('row c[r^="P"]', sheet).attr( 's', '' );
              $('row c[r^="R"]', sheet).attr( 's', '' );
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
    } );

      });


  </script>
</body>

</html>