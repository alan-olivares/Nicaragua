<?php

if(ISSET($_POST['date1'])){
  $orgDate = str_replace('/', '-', $_POST['date1']);
$date1 = date("Y-m-d", strtotime($orgDate));
include'general_connection.php';
$tsql = "select Case M.IdTipoMant When 1 then 'Cambio de Aro' When 2 Then 'Reparacion Gral' end as 'Reparación',
         C.Codigo as Uso,
         count(M.IdtipoMant) as Total
         from PR_Mantenimiento M inner join WM_Barrica B on B.IdBarrica = M.IdBarrica
         inner Join CM_CodEdad CE on CE.IdCodEdad = B.IdCodificacion
         inner join CM_Codificacion C on C.IdCodificacion = CE.IdCodificicacion
         inner join CM_Usuario U on U.IdUsuario = M.IdUsuario
         Where Convert(Date,M.Fecha) = Convert(Date,'$date1')
         group by M.IdTipoMant, C.Codigo";

$stmt = sqlsrv_query( $conn , $tsql);

?>
<script type="text/javascript">
document.getElementById("date1").valueAsDate = new Date('<?php echo $date1;?>');
</script>
<div class="table-responsive col-md-7 centro">
<table class="table table-striped table-bordered table-hover funciones" id="tabla1">
<thead>
<tr>
<th style="text-align: center;">Reparación</th>
<th style="text-align: center;">Uso</th>
<th style="text-align: center;">Total</th>
</tr>
</thead>
<tbody>


<?php
while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC))
{
?>
<tr>
<td style="text-align: center;" ><?php echo utf8_encode($row[0])?></td>
<td style="text-align: center;" ><?php echo $row[1]?></td>
<td style="text-align: right;"><?php echo (int)$row[2]?></td>
</tr>
<?php
}

sqlsrv_free_stmt( $stmt);
?>
</tbody>
</table>
</div>

<div class="table-responsive col-md-9 centro">
<table class=" funciones tabla" id="tabla2" >
<thead>
<tr>
<th style="text-align: center;">Etiqueta</th>
<th style="text-align: center;">Uso</th>
<th style="text-align: center;">Operador</th>
<th style="text-align: center;">Reparación</th>
</tr>
</thead>
<tbody>
<?php
$tsql = "select isnull((('01' + right('00' + convert(varChar(2),1),2) + right('000000' + convert(varChar(6),B.Consecutivo),6))),'Sin Asignar') as Etiqueta,
         C.Codigo as Uso,
         U.Nombre as Operador,
         Case M.IdTipoMant When 1 then 'Cambio de Aro' When 2 Then 'Reparacion Gral' end as 'Reparación'
         from PR_Mantenimiento M inner join WM_Barrica B on B.IdBarrica = M.IdBarrica
         inner Join CM_CodEdad CE on CE.IdCodEdad = B.IdCodificacion
         inner join CM_Codificacion C on C.IdCodificacion = CE.IdCodificicacion
         inner join CM_Usuario U on U.IdUsuario = M.IdUsuario
         Where Convert(Date,M.Fecha) = Convert(Date,'$date1')";

$stmt = sqlsrv_query( $conn , $tsql);

while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC))
{
?>
<tr>
  <td style="text-align: center;"><?php echo $row[0]?></td>
  <td style="text-align: center;"><?php echo $row[1]?></td>
  <td style="text-align: center;"><?php echo utf8_encode($row[2])?></td>
  <td style="text-align: center;"><?php echo utf8_encode($row[3])?></td>
</tr>
<?php
}
sqlsrv_free_stmt( $stmt);
sqlsrv_close( $conn);
?>

</tbody>
</table>
</div>

<?php
}
?>

<script src="js/jspdf.min.js"></script>
<script src="js/jspdf.plugin.autotable.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="js/exportar_csv.js"></script>

<script>
var dia=document.getElementById("date1").value;
var res = dia.split("-");
dia=res[0]+"-"+res[1]+"-"+res[2];
function generate() {

   var doc = new jsPDF('p', 'pt', 'letter');
   var res1,res0;
   if($('#tabla1 tr').length > 0 || $('#tabla2 tr').length > 1){
   // first table
    res0 = doc.autoTableHtmlToJson(document.getElementById('tabla1'));
   //get the columns & rows for first tabl
    doc.text("Fecha: "+dia,40,70);
    doc.autoTable(res0.columns, res0.data, {theme: 'grid',margin: {top: 80},columnStyles: {
      2: {
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
      startY: doc.autoTableEndPosY() + 20
     };
    doc.autoTable(res1.columns, res1.data, options);
    doc.save("Barriles reparados "+dia+".pdf");
   }else{
     window.alert("Haz una busqueda antes de exportar a PDF");
   }

}
</script>
