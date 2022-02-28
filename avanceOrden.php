<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Avance orden</title>
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
        <div class="table-responsive col-md-12">
           <table class="table table-striped table-bordered table-hover"  style="margin-top:50px;" id="tabla">
              <thead>
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
        const getApi="get_ORelleno.php";

        async function ObtenerDatos(orden){
          try {
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
              for(var i=0;i<parsed.length;i++){
                delete parsed[i]['Alcohol'];
                delete parsed[i]['Fecha_LL'];
                delete parsed[i]['Uso'];
                delete parsed[i]['Cantidad'];
                delete parsed[i]['Bodega'];
                delete parsed[i]['Tanque'];
              }
              crearTablaJson(parsed,'#tabla');
              url='RestApi/GET/'+getApi+'?avanceDeta='+orden;
              result = await conexion("GET",url,"");
              parsed =JSON.parse(result);
              crearTablaJson(parsed,'#tabla2');
              $('#avance').text(fomatoNumero((parsed.length/total)*100)+'%');
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
            mensajeError(e);
          } finally {

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
