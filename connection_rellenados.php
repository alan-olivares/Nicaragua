<?php

if(ISSET($_POST['date1'])){
  $orgDate = str_replace('/', '-', $_POST['date1']);
  $orgDate2 = str_replace('/', '-', $_POST['date2']);
$date1 = date("Y-m-d", strtotime($orgDate));
$date2 = date("Y-m-d", strtotime($orgDate2));
include'general_connection.php';
$tsql = "exec sp_RepOPRellenoEncWeb '$date1' , '$date2'";

$stmt = sqlsrv_query( $conn , $tsql);

?>
<script type="text/javascript">
document.getElementById("date1").valueAsDate = new Date('<?php echo $date1;?>');
document.getElementById("date2").valueAsDate = new Date('<?php echo $date2;?>');
</script>
<div class="col-md-12 d-flex justify-content-center table-responsive text-center">
<div class="table-responsive col-md-9 centro">
<table class="table table-striped table-bordered table-hover funciones" id="tabla1">
<thead>
<tr>
<th style="text-align: center;">N° Orden</th>
<th style="text-align: center;">Fecha ODT</th>
<th style="text-align: center;">Alcohol</th>
<th style="text-align: center;">Año Alcohol</th>
<th style="text-align: center;">Uso</th>
<th style="text-align: center;">Tipo</th>
<th style="text-align: center;">Total</th>
<th style="text-align: center;">Total litros</th>
</tr>
</thead>
<tbody>


<?php
while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC))
{
?>
<tr>
<td  style="text-align: center;"><?php echo $row[0]?></td>
<td style="text-align: center;" ><?php if($row[1]==null){echo '';}else{echo $row[1]->format('Y-m-d');}?></td>
<td style="text-align: center;" ><?php echo $row[2]?></td>
<td style="text-align: center;" ><?php echo $row[3]?></td>
<td style="text-align: center;"><?php echo $row[4]?></td>
<td style="text-align: center;"><?php echo $row[5]?></td>
<td style="text-align: right;"><?php echo (int)$row[6]?></td>
<td style="text-align: right;"><?php echo number_format((float)str_replace(",","",$row[7]), 3, '.', ',')?></td>
</tr>
<?php
}

/* Free statement and connection resources. */
sqlsrv_free_stmt( $stmt);
?>
</tbody>
</table>
</div>
</div>
<div class="table-responsive">
<table class=" funciones tabla" id="tabla2" >
<thead>
<tr>
<th style="text-align: center;">Fecha</th>
<th style="text-align: center;">N° Orden</th>
<th style="text-align: center;">Año</th>
<th style="text-align: center;">Alcohol</th>
<th style="text-align: center;">Tipo</th>
<th style="text-align: center;">Uso</th>
<th style="text-align: center;">Etiqueta</th>
<th style="text-align: center;">Litros</th>
<th style="text-align: center;">Merma</th>
<th style="text-align: center;">Último relleno</th>
<th style="text-align: center;">Relleno actual</th>
</tr>
</thead>
<tbody>
<?php
$tsql = "exec sp_RepOPRellenoWeb '$date1' , '$date2'";
$stmt = sqlsrv_query( $conn , $tsql);

while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC))
{
?>
<tr>
  <td style="text-align: center;"><?php if($row[0]==null){echo '';}else{echo $row[0]->format('Y-m-d');}?></td>
  <td style="text-align: center;"><?php echo $row[1]?></td>
  <td style="text-align: center;"><?php echo $row[2]?></td>
  <td style="text-align: center;"><?php echo $row[3]?></td>
  <td style="text-align: center;"><?php echo $row[4]?></td>
  <td style="text-align: center;"><?php echo $row[5]?></td>
  <td style="text-align: center;"><?php echo $row[6]?></td>
  <td style="text-align: right;"><?php echo number_format((float)str_replace(",","",$row[7]), 3, '.', ',')?></td>
  <td style="text-align: right;"><?php echo number_format((float)str_replace(",","",$row[8]), 3, '.', ',')?></td>
  <td style="text-align: center;"><?php echo $row[9]->format('Y-m-d')?></td>
  <td style="text-align: center;"><?php if($row[10]==null){echo '';}else{echo $row[10]->format('Y-m-d');}?></td>
</tr>

<?php
}
?>

</tbody>
<tfoot>
  <?php
$tsql = "exec sp_RepOPRellenoTotalWeb '$date1' , '$date2'";
$stmt = sqlsrv_query( $conn , $tsql);

while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC))
{
?>
<tr>
  <td style="text-align: center;"><?php echo ''?></td>
  <td style="text-align: center;"><?php echo $row[0]?></td>
  <td style="text-align: center;"><?php echo $row[1]?></td>
  <td style="text-align: center;"><?php echo $row[2]?></td>
  <th style="text-align: center;"><?php echo $row[3]?></th>
  <th style="text-align: center;"><?php echo $row[4]?></th>
  <th style="text-align: center;"><?php echo $row[5]?></th>
  <th style="text-align: right;"><?php echo number_format((float)str_replace(",","",$row[6]), 3, '.', ',')?></th>
  <th style="text-align: center;"><?php echo $row[7]?></th>
  <th style="text-align: right;"><?php echo number_format((float)str_replace(",","",$row[8]), 3, '.', ',')?></th>
  <td style="text-align: center;"><?php echo ''?></td>
</tr>

<?php
}

/* Free statement and connection resources. */
sqlsrv_free_stmt( $stmt);
sqlsrv_close( $conn);

?>
</tfoot>
</table>
</div>
<p class="p-estilo"></p>
<?php
}
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.4.1/jspdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/2.3.5/jspdf.plugin.autotable.min.js"></script>
<script src="js/exportar_csv.js"></script>

<script>
function generate() {

   var doc = new jsPDF('p', 'pt', 'letter');
   var res1,res0;
   if($('#tabla1 tr').length > 0 || $('#tabla2 tr').length > 1){
   // first table
    res0 = doc.autoTableHtmlToJson(document.getElementById('tabla1'));
   //get the columns & rows for first table
    doc.autoTable(res0.columns, res0.data, {theme: 'grid',margin: {top: 80},columnStyles: {
      6: {
        fontStyle: 'bold',
        halign: 'right',
      },
      7: {
        fontStyle: 'bold',
        halign: 'right',
      }
    }});
   // second table
    res1 = doc.autoTableHtmlToJson(document.getElementById('tabla2'));
    var options = {
      theme: 'grid',
      tableWidth: 'auto',
      columnWidth: 'auto',
      margin: {
        top: 80
      },
      styles: {
        overflow: 'linebreak'
      },
      fontSize:9,
      startY: doc.autoTableEndPosY() + 20,
      columnStyles: {
        7: {
          fontStyle: 'bold',
          halign: 'right',
        },
        8: {
          fontStyle: 'bold',
          halign: 'right',
        }
      }
     };
    doc.autoTable(res1.columns, res1.data, options);
    doc.save("Barriles rellenados.pdf");
   }else{
     window.alert("Haz una busqueda antes de exportar a PDF");
   }

}
</script>
