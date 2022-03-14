const getApi="get_VI_barriles.php";
const postApi="post_VI_barriles.php";
$( function() {
  var options={
    open: function() {
        $(this).closest(".ui-dialog")
        .find(".ui-dialog-titlebar-close")
        .removeClass("ui-dialog-titlebar-close").addClass("b-r-xl btn btn-danger");
    },
    autoOpen: false,
    height: 'auto',
    width: 'auto',
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
    maxHeight: 600,
    minWidth: 500,
  };

  dialogEditar = $( "#editarDialog" ).dialog(options);
  dialogMover = $( "#moverDialog" ).dialog(options);
  dialogAgregar = $( "#agregarDialog" ).dialog(options);
  dialogBuscar = $( "#buscarDialog" ).dialog(options);
$( ".cerrar" ).button().on( "click", function() {
  dialogEditar.dialog( "close" );
  dialogMover.dialog( "close" );
  dialogAgregar.dialog( "close" );
});


} );

function search(ele) {
    if(event.key === 'Enter') {
        BuscaConse(1);
    }
}
async function BuscaConse(caso){
  if($('#buscaConse').val()===''){
    alert('El consecutivo o etiqueta no puede estar vacío');
    return;
  }
  var valor=$('#buscaConse').val();
  valor=(valor.length!=10?valor:EtiquetaAConsecutivo(valor))
  if(caso==2){
    abrir('busquedaEtiqueta.php?consecutivo='+valor);
    dialogBuscar.dialog( "close" );
    return;
  }

  try {
    empezar();
    $('#checkUbi').attr('hidden',true);
    const url='RestApi/GET/'+getApi+'?consecutivoBus='+valor;
    var result = await conexion("GET",url,"");
    var parsed =JSON.parse(result);
    if(parsed.length>0){
      limpiarCampos('bodega');
      $("#planta").val('');
      if(parsed[0].PlantaID!==undefined){
        $("#planta").val(parsed[0].PlantaID);
        await getInfo(document.getElementById("planta"),'bodegas','Nombre','AlmacenId','bodega');
        $("#bodega").val(parsed[0].AlmacenID);
        await getInfo(document.getElementById("bodega"),'bodega','Costados','ID','Costado');
        $("#Costado").val(parsed[0].AreaId);
        await getInfo(document.getElementById("Costado"),'area','Filas','ID','Filas');
        $("#Filas").val(parsed[0].SeccionID);
        await getInfo(document.getElementById("Filas"),'fila','Torres','ID','Torres');
        $("#Torres").val(parsed[0].PosicionID);
        await getInfo(document.getElementById("Torres"),'torre','Niveles','RackLocID','Niveles');
      }else{
        $('#checkUbi').attr('hidden',false);
      }
      dialogBuscar.dialog( "close" );
      setTimeout(function() {
        $("#Niveles").val(parsed[0].RackLocID);
        CargarTabla(parsed[0].IdPallet,valor)
      }, 500);

    }else{
      alert('El consecutivo '+valor+' no existe en la base de datos, verificalo e intentalo de nuevo');
    }
  } catch (e) {
    mensajeError(e);
  } finally {
    parar();
  }

}

async function GuardarMover(){
  var motivo=document.getElementById("MotivoM").value;
  if(motivo===""){
    mensajeError("El motivo no debe estar vacío");
  }else if(document.getElementById("NivelesM").value===document.getElementById("Niveles").value){
    mensajeError("Este barril ya pertenece a esta bodega");
  }else if(document.getElementById("NivelesM").value===""){
    mensajeError("Por favor completa todos los datos");
  }else{
    var elements = document.getElementsByClassName("selected");
    try{
      var consecutivos=[];
      for (var i = elements.length-1; i >= 0; i--) {
        var eee=elements[i].querySelectorAll('td');
        consecutivos.push(EtiquetaAConsecutivo(eee[0].innerHTML));
      }
      var url='RestApi/POST/'+postApi;
      var params='evento=Mover&consecutivos='+consecutivos+"&IdPallet="+document.getElementById("NivelesM").value+"&motivo="+motivo;
      var result=await conexion("POST", url,params);
      await mensajeSimple(result);
      dialogMover.dialog( "close" );
      ObtenerNotificaciones();
    }catch(error){
      mensajeError(error);
      ObtenerNotificaciones();
    }
  }
}

async function GuardarEditar(){
  var elements = document.getElementsByClassName("selected");
  var eee=elements[0].querySelectorAll('td');
  var campos="";
  var motivo=document.getElementById("MotivoE").value;
  if(motivo===""){
    mensajeError("El motivo no debe estar vacío");
    return;
  }
  if(eee[10].innerHTML !=='Vacío (Plantel)' && document.getElementById("estado").options[document.getElementById("estado").selectedIndex].text==='Vacío (Plantel)'){
    if(await mensajeOpcional('El barril se encuentra lleno, si lo quieres cambiar a vacío generará los datos de un barril vacío ¿Quieres continuar?')){
      try{
        var params='evento=Editar&consecutivo='+EtiquetaAConsecutivo(eee[0].innerHTML)+'&IdPallet='+document.getElementById("Niveles").value+'&restablecer=vacio&motivo='+motivo;
        var result=await conexion("POST", 'RestApi/POST/'+postApi,params);
        setTimeout(async function() {
          await mensajeSimple(result);
          dialogEditar.dialog( "close" );
        }, 500);
        ObtenerNotificaciones();

      }catch(error){
        setTimeout(async function() {
          mensajeError(error);
        }, 500);
      }
    }
    return;

  }else if(eee[10].innerHTML ==='Vacío (Plantel)' && document.getElementById("estado").options[document.getElementById("estado").selectedIndex].text==='Lleno (Bodega)'){
    if(await mensajeOpcional('El barril se encuentra vacío, si lo quieres cambiar a lleno generará los datos del estado anterior ¿Quieres continuar?')){
      try{
        var params='evento=Editar&consecutivo='+EtiquetaAConsecutivo(eee[0].innerHTML)+'&IdPallet='+document.getElementById("Niveles").value+'&restablecer=pasado&motivo='+motivo;
        var result=await conexion("POST", 'RestApi/POST/'+postApi,params);
        setTimeout(async function() {
          await mensajeSimple(result);
          dialogEditar.dialog( "close" );
        }, 500);
        ObtenerNotificaciones();
      }catch(error){
        setTimeout(async function() {
          mensajeError('Error, no se encontraron registros de éste barril');
        }, 500);
      }
    }
    return;
  }else{
    if(eee[5].innerHTML !== document.getElementById("tapa").value){
      try{
        await conexion("GET", 'RestApi/GET/'+getApi+'?tapa='+document.getElementById("tapa").value+"&year="+document.getElementById("Aalcohol").value,'');

        campos=campos+"NoTapa="+document.getElementById("tapa").value+"&";
      }catch(error){
        //Tapa ya existe y mostrará el error aquí
        mensajeError(error);
        return;
      }
    }
    if(document.getElementById("alcohol").value==="" && document.getElementById("estado").value!=='2'){
      mensajeError("Selecciona una opción de recepción y alcohol");
      return;
    }
    try{
      var result =await conexion("GET", 'RestApi/GET/'+getApi+'?ConsecutivoLoteB='+EtiquetaAConsecutivo(eee[0].innerHTML),'');
      var ConsecutivoLoteB =JSON.parse(result);
      if(document.getElementById("alcohol").value!=ConsecutivoLoteB[0].IdLoteBarrica){
        campos=campos+"IdLoteBarica="+document.getElementById("alcohol").value+"&";
      }
      if(document.getElementById("uso").options[document.getElementById("uso").selectedIndex].text!=eee[6].innerHTML || document.getElementById("edad").options[document.getElementById("edad").selectedIndex].text!=eee[7].innerHTML){
        campos=campos+"uso="+document.getElementById("uso").value+"&edad="+document.getElementById("edad").value+"&";
      }
      if(document.getElementById("litros").value!=eee[1].innerHTML){
        campos=campos+"Capacidad="+document.getElementById("litros").value+"&";
      }
      if(document.getElementById("revisado").value!=eee[2].innerHTML && document.getElementById("revisado").value!=null){
        campos=campos+"Revisado="+FormatDate(document.getElementById("revisado").value)+"&";
      }
      if(document.getElementById("relleno").value!=eee[3].innerHTML && document.getElementById("relleno").value!=null){
        campos=campos+"Relleno="+FormatDate(document.getElementById("relleno").value)+"&";
      }
      if(document.getElementById("estado").options[document.getElementById("estado").selectedIndex].text!=eee[10].innerHTML){
        campos=campos+"IdEstado="+document.getElementById("estado").value+"&";
      }

      if(campos!=""){
        var params='evento=Editar&consecutivo='+EtiquetaAConsecutivo(eee[0].innerHTML)+'&IdPallet='+document.getElementById("Niveles").value+"&motivo="+motivo+'&restablecer=no&'+campos;
        result=await conexion("POST", 'RestApi/POST/'+postApi,params);
        await mensajeSimple(result);
        dialogEditar.dialog( "close" );
        ObtenerNotificaciones();
      }else{
        mensajeError("Cambia algún dato antes de guardar");
      }

    }catch(error){
      mensajeError(error);
    }
  }
}
async function GuardarAgregar(){
  if(document.getElementById('etiquetaA').value!=="" && document.getElementById("MotivoA").value!==""){
    if(!document.getElementById("ubicacionA").value.includes(document.getElementById("Niveles").value)){
      var url='RestApi/POST/'+postApi;
      var params='evento=Agregar&consecutivos='+document.getElementById("concecutivoA").value+
      "&IdPallet="+document.getElementById("Niveles").value+"&motivo="+document.getElementById("MotivoA").value;
      try{
        var result=await conexion("POST", url,params);
        await mensajeSimple(result);
        dialogAgregar.dialog( "close" );
        ObtenerNotificaciones();
      }catch(error){
        mensajeError(error);
      }
    }else{
      mensajeError("Este barril ya se encuentra en esta posición");
    }
  }else {
    mensajeError("Primero debes de buscar el barril");
  }

}

function ObtenerCamposAgregarDialog(){
  return [['etiquetaA','tapaA','litrosA','revisadoA','rellenoA','aBarricaA','ubicacionA','estadoA','usoA','edadA','alcoholA','recepcionA'],
         ['Consecutivo','NoTapa','Capacidad','Revisado','Relleno','Año','Ubicación','Estado','Uso','Edad','Alcohol','Recepcion']];
}
function BorrarCamposAgregarDialog(){
  var campos=ObtenerCamposAgregarDialog();
  for (var i = 0; i < campos[0].length; i++) {
    document.getElementById(campos[0][i]).value="";
  }
}
async function BuscarBarril(){
  if(document.getElementById('concecutivoA').value!=""){
    try {
      const url='RestApi/GET/'+getApi+'?consecutivo='+document.getElementById('concecutivoA').value;
      var result = await conexion("GET",url,"");
      var parsed =JSON.parse(result);
      if(parsed.length>0){
        document.getElementById('etiquetaA').value=GenerarEtiqueta(parsed[0]['Consecutivo'],'01');
        var campos=ObtenerCamposAgregarDialog();
        for (var i = 1; i < campos[0].length; i++) {
          document.getElementById(campos[0][i]).value=parsed[0][campos[1][i]];
        }
      }else{
        mensajeError("La busqueda no arrojo resultados");
      }
    } catch(error) {
      mensajeError(error);
    }

  }else{
    mensajeError("El campo consecutivo no debe estar vacío");
  }
}
//Inicializa y abre dialogo de agregarBarril
function Agregar(){
  dialogAgregar.dialog( "open" );
  BorrarCamposAgregarDialog();
}
function RevisarVacioActivo(valor){
  var check=(valor==='Vacío (Plantel)');
  $( "#litros" ).prop( "disabled", check );
  $( "#revisado" ).prop( "disabled", check );
  $( "#relleno" ).prop( "disabled", check );
  $( "#LAlcohol" ).prop( "disabled", check );
  $( "#alcohol" ).prop( "disabled", check );
  $( "#tapa" ).prop( "disabled", check );
  if(check)
    $("#calendario").css("pointer-events", "none");
  else
    $("#calendario").css("pointer-events", "auto");
  revisarTema();
}
//Inicializa y abre dialogo de editar
 async function Editar(){
  var elements = document.getElementsByClassName("selected");
  if(elements.length==1){
    var eee=elements[0].querySelectorAll('td');
    RevisarVacioActivo(eee[10].innerHTML);
    document.getElementById("etiqueta").value =eee[0].innerHTML;
    setSelectedValue(document.getElementById("uso"), eee[6].innerHTML);
    setSelectedValue(document.getElementById("edad"), eee[7].innerHTML);
    document.getElementById("tapa").value=eee[5].innerHTML;
    document.getElementById("litros").value=eee[1].innerHTML;
    document.getElementById("revisado").valueAsDate=new Date(NormalToPicker(eee[2].innerHTML));
    document.getElementById("relleno").valueAsDate=new Date(NormalToPicker(eee[3].innerHTML));
    document.getElementById("Abarrica").value=eee[4].innerHTML;
    setSelectedValue(document.getElementById("estado"), eee[10].innerHTML);
    document.getElementById("Aalcohol").value=eee[8].innerHTML;
    var result =await conexion("GET", 'RestApi/GET/'+getApi+'?ConsecutivoLoteB='+EtiquetaAConsecutivo(eee[0].innerHTML),'');
    var ConsecutivoLoteB =JSON.parse(result);

    //document.getElementById("LAlcohol").value=ConsecutivoLoteB[0].Lote;
    $("#data_1 .input-group.date").datepicker().datepicker("setDate", ConsecutivoLoteB[0].Lote);
    PonerOpcAlcohol(ConsecutivoLoteB[0].Lote,ConsecutivoLoteB[0].IdLoteBarrica);
    dialogEditar.dialog( "open" );
  }else if(elements.length===0){
    mensajeError("Primero debes seleccionar un barril");
  }else if(elements.length>1){
    mensajeError("Solo puedes editar un barril a la vez");
  }
}
async function PonerOpcAlcohol(fecha,valor){
  var result =await conexion("GET", 'RestApi/GET/'+getApi+'?fechaLoteB='+fecha,'');
  var fechaLoteB =JSON.parse(result);
  $("#alcohol").empty();
  for (i = 0; i < fechaLoteB.length; i++) {
    var x = document.createElement("OPTION");
    x.setAttribute("value", fechaLoteB[i]["IdLoteBarica"]);
    var t = document.createTextNode(fechaLoteB[i]["Recepcion"]);
    x.appendChild(t);
    document.getElementById("alcohol").appendChild(x);
  }
  document.getElementById("alcohol").value=valor;
  if(valor==0){
    document.getElementById("alcohol").options[0].selected = true;
  }
  PonerAAlcohol();

}
function CambiarLote(){
  document.getElementById("Aalcohol").value="";
  PonerOpcAlcohol(document.getElementById("LAlcohol").value,0);
}
function PonerAAlcohol(){
  var year=document.getElementById("alcohol").options[document.getElementById("alcohol").selectedIndex].text;
  document.getElementById("Aalcohol").value=year.substring(0, 4);
}
//Inicializa y abre dialogo de mover
function Mover(){
  var elements = document.getElementsByClassName("selected");
  if(elements.length>=1){
    $('#avisoM').attr('hidden',true);
    $('#guardarMover').attr('disabled',true);
    $("#moverBarriles > tbody").empty();
    $("#plantaM").empty();
    $("#bodegaM").empty();
    $("#CostadoM").empty();
    $("#FilasM").empty();
    $("#TorresM").empty();
    $("#NivelesM").empty();
    $('#planta').find('option').clone().appendTo('#plantaM');
    $('#bodega').find('option').clone().appendTo('#bodegaM');
    $('#Costado').find('option').clone().appendTo('#CostadoM');
    $('#Filas').find('option').clone().appendTo('#FilasM');
    $('#Torres').find('option').clone().appendTo('#TorresM');
    $('#Niveles').find('option').clone().appendTo('#NivelesM');
    document.getElementById("plantaM").value=document.getElementById("planta").value;
    document.getElementById("bodegaM").value=document.getElementById("bodega").value;
    document.getElementById("CostadoM").value=document.getElementById("Costado").value;
    document.getElementById("FilasM").value=document.getElementById("Filas").value;
    document.getElementById("TorresM").value=document.getElementById("Torres").value;
    document.getElementById("NivelesM").value=document.getElementById("Niveles").value;
    var table = document.getElementById("moverBarriles");
    for (var i = 0; i < elements.length; i++) {
      var eee=elements[i].querySelectorAll('td');
      $(table).find('tbody').append("<tr><td>"+eee[0].innerHTML+"</td></tr>");
    }
    dialogMover.dialog( "open" );
  }else {
    mensajeError("Selecciona al menos un barril");
  }
}

//Obtiene los valores de los selects
async function getInfo(sel,tipo,etiqueta,valor,boton){
  if(sel.value!=""){
    try{
      empezar();
      const url='RestApi/GET/'+getApi+'?'+tipo+'='+sel.value;
      var result = await conexion("GET",url,"");
      var parsed =JSON.parse(result);
      limpiarCampos(boton);
      //se agregan los elementos
      llenarSelect('#'+boton,valor,etiqueta,parsed);
      parar();
    } catch(error) {
      mensajeError(error);
    }
  }else{
    limpiarCampos(boton);
  }
}
//Obtiene los lugares disponibles de un Nivel
async function getSpots(self){
  if(self.value!=='' && $('#bodegaM').val()!=13){
    try {
      const url='RestApi/GET/'+getApi+'?lugaresDisRack='+self.value;
      var result = await conexion("GET",url,"");
      var parsed =JSON.parse(result);
      $('#avisoM').attr('hidden',false);
      if(parsed.length>0){
        $('#avisoM').text(parsed[0].Dispon>0?parsed[0].Dispon+' lugar(es) disponibles':'Sin lugares disponibles').css("color", $('#moverBarriles tbody tr').length>parsed[0].Dispon?"red":"black");
        $('#guardarMover').attr('disabled',($('#moverBarriles tbody tr').length>parsed[0].Dispon));
      }else{
        $('#avisoM').text('Sin lugares disponibles');
      }
    } catch (e) {
      mensajeError('Hubo un problema al obtener los lugares disponibles de este nivel')
    }
  }else if($('#bodegaM').val()==13){
    $('#avisoM').attr('hidden',false);
    $('#avisoM').text('Nota: Bodega para barriles vaciados').css("color", "orange");
    $('#guardarMover').attr('disabled',false);
  }

}
//Se limpian los campos cuando el usario decide buscar en otros selects
function limpiarCampos(select) {
  switch (select) {
    case 'bodega':
      $("#bodega").empty();
      $('#checkUbi').attr('hidden',true);
    case 'Costado':
      $("#Costado").empty();
    case 'Filas':
      $("#Filas").empty();
    case 'Torres':
      $("#Torres").empty();
    case 'Niveles':
      $('#totalB').text('');
      OcultarBotones();
      $("#Niveles").empty();
    break;
    case 'bodegaM':
      $("#bodegaM").empty();
    case 'CostadoM':
      $("#CostadoM").empty();
    case 'FilasM':
        $("#FilasM").empty();
    case 'TorresM':
        $("#TorresM").empty();
    case 'NivelesM':
      $("#NivelesM").empty();
      $('#guardarMover').attr('disabled',true);
      $('#avisoM').attr('hidden',true);
    break;

    default:

  }
}

//Obtiene los barriles que pertenecen a esta area
async function CargarTabla(valor,consecutivo){

  if(valor!=""){
    try {
      empezar();
      const url='RestApi/GET/'+getApi+'?Rack='+valor+'&isSerching='+(consecutivo!==''?1:0);
      var result = await conexion("GET",url,"");
      var parsed =JSON.parse(result);
      $("#Agregar").hide();
      $("#Mover").hide();
      $("#Editar").hide();
      if(tabla!=null){
        tabla.destroy();
      }
      if(parsed.length>10000){
        if(await mensajeOpcional('Se encontraron '+parsed.length+' barriles en esta ubicación, lo que provocará que tome hasta varios minutos en cargar ¿Deseas continuar?')){
          setTimeout(function() {recargarTabla(parsed,parsed.length,consecutivo);}, 500);
        }
      }else{
        recargarTabla(parsed,parsed.length,consecutivo);
      }
      //parar();
    } catch(error) {
      mensajeError(error);
    }finally{
      parar();
    }
  }else{
    OcultarBotones();
  }
}
var tabla=null;
function recargarTabla(parsed,tamano,consecutivo){
  crearTablaJson(parsed,'#barriles');
  var pos=1;
  if(consecutivo!==''){
    consecutivo=GenerarEtiqueta(consecutivo,'01');
    $('#barriles > tbody  > tr').each(function(index, tr) {
      if(consecutivo===$(tr).children('td:first').text()){
        $(tr).toggleClass('selected');
        $(tr).children('td').toggleClass('seleccionado');
        pos=index;
        return;
      }
    });
  }
  $('#totalB').text(tamano+' barriles encontrados');
  tabla=$('#barriles').DataTable({
    responsive: true,
    "bPaginate": false,
    "paging":   (tamano>100),
    "ordering": false,
    "info":     false,
    "lengthMenu": [ 100, 200, 300, 500],
    searching: (tamano>100),
    "language": {
      "lengthMenu":     "Mostrar _MENU_ resultados",
      "emptyTable": "No se encontraron barriles en ésta posición",
      searchPlaceholder: "Busca algún dato aquí",
      search: "",
      "paginate": {
        "first":      "Primera",
        "last":       "Última",
        "next":       "Siguiente",
        "previous":   "Anterior"
      },
    }
  });
  if(tamano>100 && consecutivo!==''){
    var page=parseInt(pos/100);
    tabla.page(page).draw(false);
  }

  MostrarBotones();
}
//Se muestran los botones cuando el usuario haya buscado una tabla
function MostrarBotones(){
  if((document.getElementById("barriles").rows.length<=9 && $('#checkUbi').attr('hidden')) || $('#bodega :selected').text().includes('EMBARRILADO')){
    $("#Agregar").show();
  }
  if(document.getElementById("barriles").rows.length>1){
    $("#Mover").show();
    $("#Editar").show();
  }
  $("#scrollingtable").show();

  MostrarEfecto('bounceInLeft','bounceOutRight');
}
//Crea el efecto esperado, y elimina el efecto que tenia anteriormente
function MostrarEfecto(efecto,efecto2){
  $('#scrollingtable').removeClass(efecto2);
  $('#scrollingtable').addClass(efecto);
  $('#Agregar').removeClass(efecto2);
  $("#Agregar").addClass(efecto);
  $('#Quitar').removeClass(efecto2);
  $("#Quitar").addClass(efecto);
  $('#Mover').removeClass(efecto2);
  $("#Mover").addClass(efecto);
  $('#Editar').removeClass(efecto2);
  $("#Editar").addClass(efecto);
}
//Se ocultan los botones cuando el usuario no tenga los selects seleccionados
function OcultarBotones(){
  setTimeout(function() {
    $("#scrollingtable").hide();
    $("#Agregar").hide();
    $("#Mover").hide();
    $("#Editar").hide();
  }, 500);
  MostrarEfecto('bounceOutRight','bounceInLeft');
}

$('#barriles tbody').on( 'click', 'tr', function () {
  $(this).toggleClass('selected');
  $(this).children('td').toggleClass('seleccionado');
});



async function cargaPlantas(){
  try {
    var url='RestApi/GET/'+getApi+'?plantas=true';
    var result = await conexion("GET",url,"");
    var parsed =JSON.parse(result);
    llenarSelect('#planta',"PlantaID","Nombre",parsed);
    $("#planta").val("1").change();
  } catch (e) {
    mensajeError(e);
  }
}

async function cargaMotivos(campo,tipo){
  try {
    var url='RestApi/GET/'+getApi+'?razones='+tipo;
    var result = await conexion("GET",url,"");
    var parsed =JSON.parse(result);
    llenarSelect(campo,"IdRazon","Descripcion",parsed);
  } catch (e) {
    mensajeError(e);
  }
}

$(document).ready(function(){
  //Por default los botones y la tabla deben de estar desactivado
  permisos(["2"]);
  cargaMotivos('#MotivoA','1');
  cargaMotivos('#MotivoM','3');
  cargaMotivos('#MotivoE','2');
  const url='RestApi/GET/'+getApi+'?fechasLotes=true';
  cargaPlantas();

  ActualizarFechasLotes(url);
  $("#scrollingtable").hide();
  $("#Agregar").hide();
  $("#Quitar").hide();
  $("#Mover").hide();
  $("#Editar").hide();


});
