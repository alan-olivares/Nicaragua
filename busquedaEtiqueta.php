<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Busquedas de etiquetas</title>
      <link href="css/bootstrap.min.css" rel="stylesheet">
      <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
      <link rel="icon" href="img\TBRE.ico">
      <link href="css/plugins/dataTables/datatables.min.css" rel="stylesheet">
      <link href="css/animate.css" rel="stylesheet">
      <link href="css/style.css" rel="stylesheet">
   </head>
   <body class="gray-bg">
      <script src="js/revisar_sesion.js"></script>
      <div class="row wrapper border-bottom white-bg page-heading text-center" >
        <div class="table-responsive col-md-12"   style="margin-top:40px;">
          <h3 id="ubicacion"></h3>
          <div class="col-md-12 d-flex justify-content-center text-center">
           <div class="table-responsive col-md-12 centro animated">
             <table class="table table-striped table-bordered table-hover" id="tabla">
                <thead>
                </thead>
                <tbody>
                </tbody>
             </table>
           </div>
          </div>
          <h3 id="totalB"></h3>
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
        const getApi="get_VI_barriles.php";

        async function ObtenerDatos(consecutivo){
          try {
            var url='RestApi/GET/'+getApi+'?consecutivoBus='+consecutivo;
            var result = await conexion("GET",url,"");
            var parsed =JSON.parse(result);
            if(parsed.length>0){
              $('#ubicacion').text(parsed[0].Ubicacion);
              url='RestApi/GET/'+getApi+'?Rack='+parsed[0].IdPallet+'&isSerching=1';
              result = await conexion("GET",url,"");
              parsed =JSON.parse(result);
              $('#totalB').text(parsed.length+' barriles encontrados');
              crearTablaJson(parsed,'#tabla');
              $('#tabla').DataTable({
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
            }else{
              alert('El consecutivo '+consecutivo+' no existe dentro de la base de datos')
            }

          } catch (e) {
            alert(e);
          } finally {

          }

        }

        $(document).ready(function(){
          var etiqueta='<?php echo ISSET($_GET['consecutivo'])?$_GET['consecutivo']:'' ?>';
          ObtenerDatos(etiqueta)
          //ObtenerDatos();
        });

      </script>
   </body>
</html>
