<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Avance orden</title>
      <link href="css/jquery.dataTables.min.css" rel="stylesheet">
      <link href="css/bootstrap.min.css" rel="stylesheet">
      <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
      <link rel="icon" href="img\TBRE.ico">
      <link rel="stylesheet" href="css/jquery-ui.css">
      <link href="css/animate.css" rel="stylesheet">
      <link href="css/style.css" rel="stylesheet">
   </head>
   <body class="gray-bg">
      <script src="js/revisar_sesion.js"></script>
      <div class="row wrapper border-bottom white-bg page-heading" >
        <div class="col-md-12 text-center d-flex justify-content-center" style="margin-top:50px;" >
          <div class="col-md-4">
            <h5><h4>Alcohol: <h4 class="subrrayado" id="alcohol"></h4></h4></h5>
          </div>
          <div class="col-md-4">
            <h5><h4>Año Alcohol: <h4 class="subrrayado" id="annio"></h4></h4></h5>
          </div>
          <div class="col-md-4">
            <h5><h4>Uso: <h4 class="subrrayado" id="uso"></h4></h4></h5>
          </div>
        </div>
        <div class="col-md-12 text-center d-flex justify-content-center" style="margin-top:50px;" >
          <div class="col-md-3">
            <h5><h4>Bodega: <h4 class="subrrayado" id="bodega"></h4></h4></h5>
          </div>
          <div class="col-md-3">
            <h5><h4>Total barriles: <h4 class="subrrayado" id="barriles"></h4></h4></h5>
          </div>
          <div class="col-md-3">
            <h5><h4>Tanque: <h4 class="subrrayado" id="tanque"></h4></h4></h5>
          </div>
          <div class="col-md-3">
            <h5><h4>Avance: <h4 class="subrrayado" id="avance"></h4></h4></h5>
          </div>
        </div>
        <div class="sk-spinner sk-spinner-three-bounce">
           <div class="sk-bounce1"></div>
           <div class="sk-bounce2"></div>
           <div class="sk-bounce3"></div>
        </div>
        <div class="table-responsive col-md-12">
           <table class="table table-striped table-bordered table-hover"  style="margin-top:50px;" id="tabla">
              <thead>
                <tr>
                  <th>Costado</th>
                  <th>Fila</th>
                  <th>Barriles disponibles</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
           </table>
        </div>
         <div class="table-responsive col-md-12">
            <table class="table table-striped table-bordered table-hover"  style="margin-top:50px;" id="tabla2">
               <thead>
               </thead>
               <tbody>
               </tbody>
            </table>
         </div>
      </div>
      <div id="Dialog" title="Barriles disponibles" class="animated">
         <div>
            <div class="col-md-12 text-center  d-flex justify-content-center" >
               <div class="table-responsive col-md-12 centro" >
                  <table class="table table-striped table-bordered table-hover"  id="barrilesTab">
                     <thead>
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
         </div>
      </div>
      <script src="js/jquery-3.1.1.min.js"></script>
      <script src="js/jquery-ui.js"></script>
      <script src="js/popper.min.js"></script>
      <script src="js/bootstrap.js"></script>
      <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
      <script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
      <script src="js/jquery.dataTables.min.js"></script>
      <script src="js/plugins/dataTables/datatables.min.js"></script>
      <script src="js/plugins/dataTables/dataTables.bootstrap4.min.js"></script>
      <!-- Custom and plugin javascript -->
      <script src="js/inspinia.js"></script>
      <script src="js/plugins/pace/pace.min.js"></script>
      <script src="js/funciones_generales.js"></script>
      <script>
        const getApi="get_ORelleno.php";
        $( function() {
          var options={
            open: function() {
                $(this).closest(".ui-dialog")
                .find(".ui-dialog-titlebar-close")
                .removeClass("ui-dialog-titlebar-close").addClass("b-r-xl btn btn-danger");
            },
            autoOpen: false,
            height: 400,
            modal: true,
            closeOnEscape: true,
            show: {
              effect: "puff",
              duration: 800
            },
            hide: {
              effect: "drop",
              duration: 800
            },
            maxHeight: 400,
            minWidth: 550,
          };

          barriles = $( "#Dialog" ).dialog(options);
        $( ".cerrar" ).button().on( "click", function() {
          barriles.dialog( "close" );
        });


        } );
        var tablaBarr=null;

        async function buscarBarrDisponibles(fila,annio,cod,alcohol,tipo,cosNom,filaNom){
          if(tablaBarr!=null){
            tablaBarr.destroy();
          }
          $('#barrilesTab tbody').empty();
          barriles.dialog("option", "title", 'Barriles en '+$('#bodega').text()+', C: '+cosNom+', '+filaNom) ;
          empezar();
          barriles.dialog("open");
          try {
            var url='RestApi/GET/'+getApi+'?barrDispo=true&fila='+fila+'&annio='+annio+'&cod='+cod+'&alcohol='+alcohol+'&tipo='+tipo;
            var result = await conexion("GET",url,'');
            var parsed =JSON.parse(result);
            crearTablaJson(parsed,'#barrilesTab');
            tablaBarr=$('#barrilesTab').DataTable({
                responsive: true,
                "bPaginate": false,
                "paging":   false,
                "ordering": true,
                "info":     false,
                dom: '<"html5buttons"B>lTfgitp',
                language: {
                  searchPlaceholder: "Busca algún dato aquí",
                  search: "",
                },
                buttons: []

            });
          } catch (e) {
            alert(e)
          } finally {
            parar();
          }

        }
        function countBarriles(json){
          var barr=0;
          json.forEach((item, i) => {
            if(json[i]['Operación']!=='Sobrante'){
              barr++;
            }
          });
          return barr;
        }

        async function ObtenerDatos(orden){
          try {
            empezar();
            var url='RestApi/GET/'+getApi+'?avance='+orden;
            var result = await conexion("GET",url,"");
            var parsed =JSON.parse(result);
            if(parsed.length>0){
              $('#alcohol').text(parsed[0].Alcohol);
              $('#annio').text(parsed[0].Fecha_LL);
              $('#uso').text(parsed[0].Uso);
              $('#barriles').text(parsed[0].Cantidad);
              var total=parseInt(parsed[0].Cantidad)
              $('#bodega').text(parsed[0].Bodega);
              $('#tanque').text(parsed[0].Tanque);
              var content="";
              for(var i=0;i<parsed.length;i++){
                var disp=parsed[i]["Barriles disponibles"];
                content+='<tr><td>'+parsed[i].Costado+'</td><td>'+parsed[i].Fila+
                '</td>'+(disp>0?'<td style="color:blue;text-decoration: underline;"><a onclick="javascript:buscarBarrDisponibles(\''+parsed[i].FilaId+'\',\''+parsed[i].Fecha_LL+'\',\''+parsed[i].IdCodificacion+'\',\''+parsed[i].IdAlcohol+'\',\''+(parsed[i].Tanque==='N/A'?1:2)+'\',\''+parsed[i].Costado+
                '\',\''+parsed[i].Fila+'\')">'+disp+'</a></td>':'<td>0</td>')+'</tr>';
              }
              $('#tabla tbody').append(content);
              url='RestApi/GET/'+getApi+'?avanceDeta='+orden;
              result = await conexion("GET",url,"");
              parsed =JSON.parse(result);
              crearTablaJson(parsed,'#tabla2');
              $('#avance').text(parseInt((countBarriles(parsed)/total)*100)+'%');
              $('#tabla2').DataTable({
                  responsive: true,
                  "bPaginate": false,
                  "paging":   false,
                  "ordering": true,
                  "info":     false,
                  dom: '<"html5buttons"B>lTfgitp',
                  language: {
                    searchPlaceholder: "Busca algún dato aquí",
                    search: "",
                  },
                  buttons: [{ extend: 'copy',text:'Copiar'}]

              });
            }

          } catch (e) {
            alert(e);
          } finally {
            parar();
          }

        }

        $(document).ready(function(){
          var orden='<?php echo ISSET($_GET['orden'])?$_GET['orden']:'' ?>';
          ObtenerDatos(orden)
          //ObtenerDatos();
        });

      </script>
   </body>
</html>
