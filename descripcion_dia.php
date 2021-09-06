<?php
$dia=$_GET["dia"];
$hora1=$_GET["hora1"];
$hora2=$_GET["hora2"];
$evento=$_GET["evento"];
$tipo=$_GET["tipo"];
include 'general_connection.php';
$tsql = "exec sp_getAcceso '$usuario' , '$pass'";
$stmt = sqlsrv_query( $conn , $tsql);
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
if($row[0]=='1'){
  include 'RestApi/revisar_permisos.php';
  if(strpos($permisos,',11,') !== false){
?>
<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reporte del día <?php echo $dia?></title>

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
    <div class="table-responsive col-md-12">
      <h3 style="margin-top:20px;margin-left:10px;" id="fecha">Fecha: <?php echo $dia?></h3>
      <h4 style="margin-top:20px;margin-left:10px;" id="evento">Evento: <?php echo $tipo?></h4>
    <table class="funciones tabla " id="tabla1">
      <?php
      if($evento!=10){
      ?>
    <thead>
      <tr>
        <th style="text-align: center;">Hora</th>
        <th style="text-align: center;">N° Orden</th>
        <th style="text-align: center;">Etiqueta</th>
        <th style="text-align: center;">Tapa</th>
        <th style="text-align: center;">Uso</th>
        <th style="text-align: center;">Litros</th>
        <th style="text-align: center;">Alcohol</th>
        <th style="text-align: center;">Año Alcohol</th>
      </tr>
      </thead>
      <tbody>
        <?php
        $tsql = "select SUBSTRING(CONVERT(CHAR(16), lo.fecha, 120),12,5),
        IdOrden,'01' + '01' +(right('000000' + convert(varChar(6),Consecutivo ),6)) as Etiqueta,
        NoTapa, C.Codigo, Capacidad, Al.Descripcion as Alcohol, Year(L.recepcion) as [Año Alcohol]
        from adm_logregbarril lo
        inner join PR_RegBarril r on r.idregbarril=lo.IdregBarril
        inner Join CM_CodEdad CE on CE.IdCodEdad = r.IdCodedad
        inner Join CM_Codificacion C on C.IdCodificacion = CE.IdCodificicacion
        inner Join WM_LoteBarrica LB on LB.IdLoteBarica = r.IdLoteBarrica
        left Join PR_Lote L on L.Idlote = LB.IdLote
        inner Join CM_Alcohol Al on Al.IdAlcohol = L.IdAlcohol
        where lo.fecha between '$hora1' and '$hora2' and r.TipoReg in ($evento) order by SUBSTRING(CONVERT(CHAR(16), lo.fecha, 120),12,5)";

        $stmt = sqlsrv_query( $conn , $tsql);

        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC))
        {
        ?>
        <tr>
          <td style="text-align: center;"><?php echo $row[0]?></td>
          <td style="text-align: center;"><?php echo $row[1]?></td>
          <td style="text-align: center;"><?php echo $row[2]?></td>
          <td style="text-align: center;"><?php echo $row[3]?></td>
          <td style="text-align: center;"><?php echo $row[4]?></td>
          <td style="text-align: right;"><?php echo number_format((float)str_replace(",","",$row[5]), 3, '.', ',')?></td>
          <td style="text-align: center;"><?php echo $row[6]?></td>
          <td style="text-align: center;"><?php echo $row[7]?></td>
        </tr>
        <?php
        }
        ?>
        </tbody>
        <tfoot>
          <tr>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <th style="text-align: center;">Total Litros:</th>
            <th style="text-align: right;"></th>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
          </tr>
        </tfoot>
        <?php
      }else{
        ?>
        <thead>
          <tr>
            <th style="text-align: center;">Hora</th>
            <th style="text-align: center;">Etiqueta</th>
            <th style="text-align: center;">Uso</th>
            <th style="text-align: center;">Operador</th>
            <th style="text-align: center;">Reparación</th>
          </tr>
          </thead>
          <tbody>
            <?php
            $tsql = "select SUBSTRING(CONVERT(CHAR(16), L.fecha, 120),  12, 5),
            isnull((('01' + right('00' + convert(varChar(2),1),2) + right('000000' + convert(varChar(6),B.Consecutivo),6))),'Sin Asignar') as Etiqueta,
            C.Codigo as Uso,
            U.Nombre as Operador,
            Case M.IdTipoMant When 1 then 'Cambio de Aro' When 2 Then 'Reparación Gral' end as 'Reparación'
            from PR_Mantenimiento M
            inner join WM_Barrica B on B.IdBarrica = M.IdBarrica
            INNER JOIN ADM_logMantenimiento L ON M.IdMantenimiento=L.IdMantenimiento
            inner Join CM_CodEdad CE on CE.IdCodEdad = B.IdCodificacion
            inner join CM_Codificacion C on C.IdCodificacion = CE.IdCodificicacion
            inner join CM_Usuario U on U.IdUsuario = M.IdUsuario
            Where L.TipoOp='I' and L.Fecha between '$hora1' and '$hora2'
            order by SUBSTRING(CONVERT(CHAR(16), L.fecha, 120),  12, 5)";

            $stmt = sqlsrv_query( $conn , $tsql);

            while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC))
            {
            ?>
            <tr>
              <td style="text-align: center;"><?php echo $row[0]?></td>
              <td style="text-align: center;"><?php echo $row[1]?></td>
              <td style="text-align: center;"><?php echo $row[2]?></td>
              <td style="text-align: center;"><?php echo utf8_encode($row[3])?></td>
              <td style="text-align: center;"><?php echo utf8_encode($row[4])?></td>
            </tr>
            <?php
            }
            ?>
            </tbody>
        <?php
      }
      sqlsrv_free_stmt( $stmt);
      sqlsrv_close( $conn);
        ?>
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
  var dia='<?php echo $dia?>';
  var evento='<?php echo $tipo?>';
      $(document).ready(function(){
        $('#tabla1').DataTable( {
          "footerCallback": function ( row, data, start, end, display ) {
          if(document.getElementById('tabla1').rows[0].cells.length>=6){
          var api = this.api(), data;

          // Remove the formatting to get integer data for summation
          var intVal = function ( i ) {
              return typeof i === 'string' ?
                  i.replace(/[\$,]/g, '')*1 :
                  typeof i === 'number' ?
                      i : 0;
          };
          pageTotal = api.column( 5, { page: 'current'} ).data().reduce( function (a, b) {
              return intVal(a) + intVal(b);
          }, 0 );
          // Update footer

            $( api.column(5).footer() ).html((Math.round(pageTotal * 100) / 100).toFixed(3).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
          }

      },
      responsive: true,
      "bPaginate": false,
      "paging":   false,
      "ordering": false,
      "info":     false,
      language: {
        searchPlaceholder: "Busca algún dato aquí",
        search: "",
      },
      dom: '<"html5buttons"B>lTfgitp',
      buttons: [
          { extend: 'copy',text:'Copiar'},
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

      /*var doc = {
    pageSize: config.pageSize,
    pageOrientation: config.orientation,
    content: [
        {
            table: {
                headerRows: 1,
                body: rows
            },
            layout: 'noBorders'
        }
    ],
    styles: {
        tableHeader: {
            bold: true,
            fontSize: 11,
            color: 'white',
            fillColor: '#2d4154',
            alignment: 'center'
        },
        tableBodyEven: {},
        tableBodyOdd: {
            fillColor: '#f3f3f3'
        },
        tableFooter: {
            bold: true,
            fontSize: 11,
            color: 'white',
            fillColor: '#2d4154'
        },
        title: {
            alignment: 'center',
            fontSize: 15
        },
        message: {}
    },
    defaultStyle: {
        fontSize: 10
    }
};*/

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
?>
