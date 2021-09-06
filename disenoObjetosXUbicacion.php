<?php
   include 'general_connection.php';
   $tsql = "exec sp_getAcceso '$usuario' , '$pass'";
   $stmt = sqlsrv_query( $conn , $tsql);
   $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
   if($row[0]=='1'){
     include 'RestApi/revisar_permisos.php';
     if(strpos($permisos,',7,') !== false){
       $planta=$_GET['plantas'];
       $bodega=$_GET['bodegas'];
       $area=$_GET['areas'];
       $fila=$_GET['filas'];
       $torres=$_GET['torres'];
       $niveles=$_GET['niveles'];
       $barriles = "SELECT isnull((select ('01' + right('00' + convert(varChar(2),1),2) + right('000000' + convert(varChar(6),B.Consecutivo),6))),'Sin Asignar') as Etiqueta,
       C.Codigo,E.Codigo,Es.Descripcion,B.Capacidad
       from AA_Plantas Pl left join AA_Almacen A on Pl.PlantaID=A.PlantaID inner Join AA_Area AA on AA.AlmacenId = A.AlmacenID
       inner Join AA_Seccion S on S.AreaId = AA.AreaId  inner join AA_Posicion P on P.SeccionID = S.SeccionID
       inner Join AA_Nivel N on N.PosicionId = P.PosicionID  inner Join WM_RackLoc RL on RL.NivelID = n.NivelID
       inner Join WM_Pallet Pa on Pa.RackLocId = RL.RackLocID inner Join WM_Barrica B on B.IdPallet = Pa.IdPallet
       Left join CM_CodEdad CE on CE.IdCodEdad= B.IdCodificacion LEft join CM_Codificacion C on C.IdCodificacion = CE.IdCodificicacion
       left join CM_Estado Es on Es.IdEstado=B.IdEstado
       Left Join CM_Edad E on E.IdEdad = CE.IdEdad Where Pl.PlantaID=$planta ";
       $tanques = "SELECT isnull((select ('01' + right('00' + convert(varChar(2),2),2) + right('000000' + convert(varChar(6),T.NoSerie),6))),'Sin Asignar') as Etiqueta,
       T.Capacidad,T.Litros,convert(varchar,T.FechaLLenado,105),Es.Descripcion
       from AA_Plantas Pl left join AA_Almacen A on Pl.PlantaID=A.PlantaID inner Join AA_Area AA on AA.AlmacenId = A.AlmacenID
       inner Join AA_Seccion S on S.AreaId = AA.AreaId  inner join AA_Posicion P on P.SeccionID = S.SeccionID
       inner Join AA_Nivel N on N.PosicionId = P.PosicionID  inner Join WM_RackLoc RL on RL.NivelID = n.NivelID
       inner Join WM_Pallet Pa on Pa.RackLocId = RL.RackLocID inner Join WM_Tanques T on T.IdPallet = Pa.IdPallet
       left join CM_Estado Es on Es.IdEstado=T.IdEstado Where Pl.PlantaID=$planta ";
       if($bodega!=="null"){
         $barriles=$barriles." and A.AlmacenID=".$bodega;
         $tanques=$tanques." and A.AlmacenID=".$bodega;
       }
       if($area!=="null"){
         $barriles=$barriles." and AA.AreaId=".$area;
         $tanques=$tanques." and AA.AreaId=".$area;
       }
       if($fila!=="null"){
         $barriles=$barriles." and S.SeccionID=".$fila;
         $tanques=$tanques." and S.SeccionID=".$fila;
       }
       if($torres!=="null"){
         $barriles=$barriles." and P.PosicionID=".$torres;
         $tanques=$tanques." and P.PosicionID=".$torres;
       }
       if($niveles!=="null"){
         $barriles=$barriles." and N.NivelID=".$niveles;
         $tanques=$tanques." and N.NivelID=".$niveles;
       }
       $barriles=$barriles." order by B.IdCodificacion";
       ?>
       <!DOCTYPE html>
       <html>
          <head>
             <meta charset="utf-8">
             <meta name="viewport" content="width=device-width, initial-scale=1.0">
             <title>Barriles y Tanques por ubicación</title>
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
                  <div class="col-md-4">
                    <h5><h4>Planta: <h4 class="subrrayado"><?php echo getValor("SELECT Nombre from AA_Plantas where PlantaID='$planta'",$conn); ?></h4></h4></h5>
                  </div>
                  <div class="col-md-4">
                    <h5><h4>Bodega: <h4 class="subrrayado"><?php echo getValor("SELECT Nombre from AA_Almacen where AlmacenID='$bodega'",$conn); ?></h4></h4></h5>
                  </div>
                  <div class="col-md-4">
                    <h5><h4>Area: <h4 class="subrrayado"><?php echo getValor("SELECT Nombre from AA_Area where AreaId='$area'",$conn); ?></h4></h4></h5>
                  </div>
                </div>
                <div class="col-md-12 text-center d-flex justify-content-center" >
                  <div class="col-md-4">
                    <h5><h4>Fila: <h4 class="subrrayado"><?php echo getValor("SELECT Nombre from AA_Seccion where SeccionID='$fila'",$conn); ?></h4></h4></h5>
                  </div>
                  <div class="col-md-4">
                    <h5><h4>Torre: <h4 class="subrrayado"><?php echo getValor("SELECT Nombre from AA_Posicion where PosicionID='$torres'",$conn); ?></h4></h4></h5>
                  </div>
                  <div class="col-md-4">
                    <h5><h4>Nivel: <h4 class="subrrayado"><?php echo getValor("SELECT Nombre from AA_Nivel where NivelID='$niveles'",$conn);  ?></h4></h4></h5>
                  </div>
                </div>
                <div class="table-responsive col-md-12">
                  <label>Barriles</label>
                   <table class="funciones tabla" id="tabla1">
                      <thead>
                         <tr>
                            <th style="text-align: center;">Etiqueta</th>
                            <th style="text-align: center;">Uso</th>
                            <th style="text-align: center;">Edad</th>
                            <th style="text-align: center;">Estado</th>
                            <th style="text-align: center;">Capacidad</th>
                         </tr>
                      </thead>
                      <tbody>
                         <?php
                            $stmt = sqlsrv_query( $conn , $barriles);
                            while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC))
                            {
                            ?>
                         <tr>
                            <td style="text-align: center;"><?php echo $row[0]?></td>
                            <td style="text-align: center;"><?php echo $row[1]?></td>
                            <td style="text-align: center;"><?php echo $row[2]?></td>
                            <td style="text-align: center;"><?php echo utf8_encode($row[3])?></td>
                            <td style="text-align: right;"><?php echo number_format((float)str_replace(",","",$row[4]), 3, '.', ',')?></td>
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
                            <th style="text-align: center;">Total:</th>
                            <th style="text-align: right;"></th>
                         </tr>
                      </tfoot>
                   </table>
                </div>
                <div class="table-responsive col-md-12">
                  <label>Tanques hoover</label>
                   <table class="funciones tabla" id="tabla2">
                      <thead>
                         <tr>
                            <th style="text-align: center;">Etiqueta</th>
                            <th style="text-align: center;">Capacidad</th>
                            <th style="text-align: center;">Litros</th>
                            <th style="text-align: center;">Fecha Llenado</th>
                            <th style="text-align: center;">Estado</th>
                         </tr>
                      </thead>
                      <tbody>
                         <?php
                         //echo $tanques;
                            $stmt = sqlsrv_query( $conn , $tanques);
                            while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC))
                            {
                            ?>
                         <tr>
                            <td style="text-align: center;"><?php echo $row[0]?></td>
                            <td style="text-align: right;"><?php echo number_format((float)str_replace(",","",$row[1]), 3, '.', ',')?></td>
                            <td style="text-align: right;"><?php echo number_format((float)str_replace(",","",$row[2]), 3, '.', ',')?></td>
                            <td style="text-align: center;"><?php echo $row[3]?></td>
                            <td style="text-align: center;"><?php echo utf8_encode($row[4])?></td>
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
                            <th></th>
                            <th>Total:</th>
                            <td style="text-align: right;"></td>
                            <td></td>
                            <td></td>
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
             <script src="js/funciones_generales.js"></script>
             <script>
                    $(document).ready(function(){
                        $('#tabla1').DataTable( {"footerCallback": function ( row, data, start, end, display ) {
                          var api = this.api(), data;

                          // Remove the formatting to get integer data for summation
                          var intVal = function ( i ) {
                              return typeof i === 'string' ?
                                  i.replace(/[\$,]/g, '')*1 :
                                  typeof i === 'number' ?
                                      i : 0;
                          };
                          totalLitros= api.column( 4, { page: 'current'} ).data().reduce( function (a, b) {
                              return intVal(a) + intVal(b);
                          }, 0 );
                          // Update footer
                          $( api.column(4).footer() ).html((Math.round(totalLitros * 100) / 100).toFixed(3).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                      },
                      responsive: true,
                      "bPaginate": true,
                      "paging":   $('#tabla1 tbody tr').length>100,
                      "ordering": false,
                      "info":     false,
                      "emptyTable": "No se encontraron barriles en ésta posición",
                      "lengthMenu": [ 100, 200, 300, 500],
                      dom: '<"html5buttons"B>lTfgitp',
                      language: {
                        searchPlaceholder: "Busca algún dato aquí",
                        search: "",
                        "lengthMenu":     "Mostrar _MENU_ resultados",
                        "emptyTable": "No se encontraron barriles en ésta posición",
                        "paginate": {
                          "first":      "Primera",
                          "last":       "Última",
                          "next":       "Siguiente",
                          "previous":   "Anterior"
                        },
                      },
                      buttons: [{ extend: 'copy',text:'Copiar'}]
                  } );
                  $('#tabla2').DataTable( {"footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;
                    };
                    totalLitros= api.column( 2, { page: 'current'} ).data().reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                    // Update footer
                    $( api.column(2).footer() ).html((Math.round(totalLitros * 100) / 100).toFixed(3).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                },
                responsive: true,
                "bPaginate": true,
                "paging":   $('#tabla2 tbody tr').length>100,
                "ordering": false,
                "info":     false,
                "lengthMenu": [ 100, 200, 300, 500],
                dom: '<"html5buttons"B>lTfgitp',
                language: {
                  searchPlaceholder: "Busca algún dato aquí",
                  search: "",
                  "lengthMenu":     "Mostrar _MENU_ resultados",
                  "emptyTable": "No se encontraron tanques en ésta posición",
                  "paginate": {
                    "first":      "Primera",
                    "last":       "Última",
                    "next":       "Siguiente",
                    "previous":   "Anterior"
                  },
                },
                buttons: [{ extend: 'copy',text:'Copiar'}]
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
     if(!$stmt)
      return '';
     $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC);
     return utf8_encode($row[0]);
   }
   ?>
