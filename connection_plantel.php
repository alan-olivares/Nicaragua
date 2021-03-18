<?php


include'general_connection.php';
$conn = sqlsrv_connect( $serverName, $connectionInfo);
$tsql = "exec sp_ListaEdad";

$stmt = sqlsrv_query( $conn , $tsql);

?>
<script type="text/javascript">
document.getElementById("bodega").value = '<?php echo $bodegas;?>';
document.getElementById("alcohol").value = '<?php echo $Alcohol;?>';
document.getElementById("llenada").value = '<?php echo $Allenada;?>';
document.getElementById("uso").value = '<?php echo $Uso;?>';
</script>
<div class="table-responsive">
  <h3 style="margin-top:20px;margin-left:10px;" id="edad">Edades</h3>
<table class="table table-striped table-bordered table-hover" id="tabla1">
<thead>


<?php
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

<div class="col-md-12 d-flex justify-content-center table-responsive text-center">
  <div class="table-responsive col-md-8 centro">
<table class="table table-striped table-bordered table-hover" id="tabla2" >
<thead>
<tr>
<th style="text-align: center;">Número</th>
<th style="text-align: center;">Plantel</th>
<th style="text-align: center;">Uso</th>
<th style="text-align: center;">A</th>
<th style="text-align: center;">B</th>
<th style="text-align: center;">C</th>
<th style="text-align: center;">D</th>
<th style="text-align: center;">F</th>
<th style="text-align: center;">RC</th>
<th style="text-align: center;">RF</th>
<th style="text-align: center;">Total</th>
</tr>
</thead>
<tbody>
<?php
$tsql = "exec sp_BarrilPlantel";
$stmt = sqlsrv_query( $conn , $tsql);

while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC))
{
?>
<tr>
  <td style="text-align: center;color:blue;text-decoration: underline;"><a onclick="javascript:abrir('descripcion_bodegas.php?almacen=<?php echo $row[1]?>&area=<?php echo $row[2]?>&codificacion=<?php echo $row[4]?>')"><?php echo $row[0]?></a></td>
  <td style="text-align: center;"><?php echo utf8_encode($row[5])?></td>
  <td style="text-align: center;"><?php echo $row[6]?></td>
  <td style="text-align: right;"><?php echo (int)$row[7]?></td>
  <td style="text-align: right;"><?php echo (int)$row[8]?></td>
  <td style="text-align: right;"><?php echo (int)$row[9]?></td>
  <td style="text-align: right;"><?php echo (int)$row[10]?></td>
  <td style="text-align: right;"><?php echo (int)$row[11]?></td>
  <td style="text-align: right;"><?php echo (int)$row[12]?></td>
  <td style="text-align: right;"><?php echo (int)$row[13]?></td>
  <td style="text-align: right;"><?php echo (int)$row[14]?></td>
</tr>
<?php

/* Free statement and connection resources. */
}
sqlsrv_free_stmt( $stmt);
?>
</tbody>
<tfoot>
  <?php
  $tsql = "exec sp_BarrilPlantelTotal";
  $stmt = sqlsrv_query( $conn , $tsql);

  while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC))
  {
  ?>
  <tr>
    <th style="text-align: center;"><?php echo $row[0]?></th>
    <th style="text-align: center;"><?php echo $row[1]?></th>
    <th style="text-align: center;"><?php echo $row[2]?></th>
    <th style="text-align: right;"><?php echo $row[3]?></th>
    <th style="text-align: right;"><?php echo $row[4]?></th>
    <th style="text-align: right;"><?php echo $row[5]?></th>
    <th style="text-align: right;"><?php echo $row[6]?></th>
    <th style="text-align: right;"><?php echo $row[7]?></th>
    <th style="text-align: right;"><?php echo $row[8]?></th>
    <th style="text-align: right;"><?php echo $row[9]?></th>
    <th style="text-align: right;"><?php echo $row[10]?></th>
  </tr>
    <?php

    /* Free statement and connection resources. */
    }
    sqlsrv_free_stmt( $stmt);
    sqlsrv_close( $conn);
    ?>
</tfoot>
</table>
</div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.4.1/jspdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/2.3.5/jspdf.plugin.autotable.min.js"></script>
<script src="https://www.YourSite.com/wp-content/JS/table-export.js"></script>

<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="js/exportar_csv.js"></script>
<script src="js/abrir_ventana.js"></script>

<script>

function generate() {

   var doc = new jsPDF('p', 'pt', 'letter');
   var res1,res0;
   if($('#tabla1 tr').length > 0 || $('#tabla2 tr').length > 1){
   // first table
    res0 = doc.autoTableHtmlToJson(document.getElementById('tabla1'));
   //get the columns & rows for first table
   doc.text("Edades",50,70);
    doc.autoTable(res0.columns, res0.data,  {theme: 'grid',tableWidth: 'auto',  columnWidth: 'auto',margin: {top: 80},styles: {overflow: 'linebreak'}});
   // second table
    res1 = doc.autoTableHtmlToJson(document.getElementById('tabla2'));
    var options = {
      tableWidth: 'auto',
      columnWidth: 'auto',
      theme: 'grid',
      margin: {
        top: 80
      },
      styles: {
        overflow: 'linebreak'
      },
      fontSize:9,
      startY: doc.autoTableEndPosY() + 20,
      columnStyles: {
        3: {
          halign: 'right',
        },
        4: {
          halign: 'right',
        },
        5: {
          halign: 'right',
        },
        6: {
          halign: 'right',
        },
        7: {
          halign: 'right',
        },
        8: {
          halign: 'right',
        },
        9: {
          halign: 'right',
        },
        10: {
          fontStyle: 'bold',
          halign: 'right',
        },
      }
     };
    doc.autoTable(res1.columns, res1.data, options);
    doc.save("Barriles en plantel.pdf");
   }else{
     window.alert("Haz una busqueda antes de exportar a PDF");
   }

}
</script>
