const getApi="get_VI_hoover.php";
const postApi="post_VI_hoover.php";
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
$( ".cerrar" ).button().on( "click", function() {
  dialogEditar.dialog( "close" );
  dialogMover.dialog( "close" );
  dialogAgregar.dialog( "close" );
});


} );

async function GuardarMover(){
  var motivo=document.getElementById("MotivoM").value;
  if(motivo===""){
    mensajeError("El motivo no debe estar vacío");
  }else if(document.getElementById("NivelesM").value===document.getElementById("Niveles").value){
    mensajeError("Este tanque ya pertenece a esta bodega");
  }else if(document.getElementById("NivelesM").value===""){
    mensajeError("Por favor completa todos los datos");
  }else{
    var elements = document.getElementsByClassName("selected");
    var check="";
    try{
      for (var i = elements.length-1; i >= 0; i--) {
        var eee=elements[i].querySelectorAll('td');
        var url='RestApi/POST/'+postApi;
        var params='evento=Mover&NoSerie='+eee[0].innerHTML+"&IdPallet="+document.getElementById("NivelesM").value+"&motivo="+motivo;
        var result=await conexion("POST", url,params);
        check=result;
      }
      ObtenerNotificaciones();
      await mensajeSimple(check);
      dialogMover.dialog( "close" );
    }catch(error){
      mensajeError(error);
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
  if(eee[4].innerHTML !=='Vacío (Plantel)' && document.getElementById("estado").options[document.getElementById("estado").selectedIndex].text==='Vacío (Plantel)'){
    if(await mensajeOpcional('El tanque se encuentra lleno, si lo quieres cambiar a vacío generará los datos de un tanque vacío ¿Quieres continuar?')){
      try{
        var params='evento=Editar&NoSerie='+eee[0].innerHTML+'&restablecer=vacio&motivo='+motivo;
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

  }else if(eee[4].innerHTML ==='Vacío (Plantel)' && document.getElementById("estado").options[document.getElementById("estado").selectedIndex].text==='Lleno (Bodega)'){
    if(await mensajeOpcional('El tanque se encuentra vacío, si lo quieres cambiar a lleno generará los datos del estado anterior ¿Quieres continuar?')){
      try{
        var params='evento=Editar&NoSerie='+eee[0].innerHTML+'&restablecer=pasado&motivo='+motivo;
        var result=await conexion("POST", 'RestApi/POST/'+postApi,params);
        setTimeout(async function() {
          await mensajeSimple(result);
          dialogEditar.dialog( "close" );
        }, 500);
        ObtenerNotificaciones();
      }catch(error){
        setTimeout(async function() {
          mensajeError('Error, no se encontraron registros de éste tanque');
        }, 500);
      }
    }
    return;
  }else{
    try{
      if(document.getElementById("litros").value!=eee[2].innerHTML){
        campos=campos+"Litros="+document.getElementById("litros").value+"&";
      }
      if(document.getElementById("llenado").value!=eee[3].innerHTML && document.getElementById("llenado").value!=null){
        campos=campos+"Llenado="+FormatDate(document.getElementById("llenado").value)+"&";
      }
      if(document.getElementById("estado").options[document.getElementById("estado").selectedIndex].text!=eee[4].innerHTML){
        campos=campos+"IdEstado="+document.getElementById("estado").value+"&";
      }
      if(campos!=""){
        var params='evento=Editar&NoSerie='+eee[0].innerHTML+'&motivo='+motivo+'&restablecer=no&'+campos;
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
      var params='evento=Agregar&NoSerie='+document.getElementById("concecutivoA").value+
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
      mensajeError("Este tanque ya se encuentra en esta posición");
    }
  }else {
    mensajeError("Primero debes de buscar el tanque");
  }

}

function ObtenerCamposAgregarDialog(){
  return [['etiquetaA','litrosA','capacidadA','llenadoA','ubicacionA','estadoA'],
         ['NoSerie','Litros','Capacidad','llenado','Ubicacion','Estado']];
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
      const url='RestApi/GET/'+getApi+'?id='+document.getElementById('concecutivoA').value;
      var result = await conexion("GET",url,"");
      var parsed =JSON.parse(result);
      if(parsed.length>0){
        document.getElementById('etiquetaA').value=GenerarEtiqueta(parsed[0]['NoSerie'],'02');
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
//Inicializa y abre dialogo de tanque
function Agregar(){
  dialogAgregar.dialog( "open" );
  BorrarCamposAgregarDialog();
}
function RevisarVacioActivo(valor){
  var check=(valor==='Vacío (Plantel)');
  $( "#litros" ).prop( "disabled", check );
  $( "#llenado" ).prop( "disabled", check );
  revisarTema();
}

//Inicializa y abre dialogo de editar
 async function Editar(){
  var elements = document.getElementsByClassName("selected");
  if(elements.length==1){
    var eee=elements[0].querySelectorAll('td');
    RevisarVacioActivo(eee[4].innerHTML);
    document.getElementById("etiqueta").value =GenerarEtiqueta(eee[0].innerHTML,'02');
    document.getElementById("litros").value=eee[2].innerHTML;
    document.getElementById("capacidad").value=eee[1].innerHTML;
    document.getElementById("llenado").valueAsDate=new Date(eee[3].innerHTML);
    setSelectedValue(document.getElementById("estado"), eee[4].innerHTML);
    dialogEditar.dialog( "open" );
  }else if(elements.length===0){
    mensajeError("Primero debes seleccionar un tanque");
  }else if(elements.length>1){
    mensajeError("Solo puedes editar un tanque a la vez");
  }
}

//Inicializa y abre dialogo de mover
function Mover(){
  var elements = document.getElementsByClassName("selected");
  if(elements.length>=1){
    $("#moverBarriles > tbody").empty();
    $("#bodegaM").empty();
    $("#CostadoM").empty();
    $("#FilasM").empty();
    $("#TorresM").empty();
    $("#NivelesM").empty();
    $('#bodega').find('option').clone().appendTo('#bodegaM');
    $('#Costado').find('option').clone().appendTo('#CostadoM');
    $('#Filas').find('option').clone().appendTo('#FilasM');
    $('#Torres').find('option').clone().appendTo('#TorresM');
    $('#Niveles').find('option').clone().appendTo('#NivelesM');
    document.getElementById("bodegaM").value=document.getElementById("bodega").value;
    document.getElementById("CostadoM").value=document.getElementById("Costado").value;
    document.getElementById("FilasM").value=document.getElementById("Filas").value;
    document.getElementById("TorresM").value=document.getElementById("Torres").value;
    document.getElementById("NivelesM").value=document.getElementById("Niveles").value;
    var table = document.getElementById("moverBarriles");
    for (var i = 0; i < elements.length; i++) {
      var eee=elements[i].querySelectorAll('td');
      $(table).find('tbody').append("<tr><td>"+GenerarEtiqueta(eee[0].innerHTML,'02')+"</td></tr>");
    }
    dialogMover.dialog( "open" );
  }else {
    mensajeError("Selecciona al menos un tanque");
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
//Se limpian los campos cuando el usario decide buscar en otros selects
function limpiarCampos(select) {
  switch (select) {
    case 'bodega':
      $("#bodega").empty();
      $("#Costado").empty();
      $("#Filas").empty();
      $("#Torres").empty();
      OcultarBotones();
      $("#Niveles").empty();
      break;
    case 'Costado':
      $("#Costado").empty();
      $("#Filas").empty();
      $("#Torres").empty();
      OcultarBotones();
      $("#Niveles").empty();
      break;
    case 'Filas':
      $("#Filas").empty();
      $("#Torres").empty();
      OcultarBotones();
      $("#Niveles").empty();
    break;
    case 'Torres':
      $("#Torres").empty();
      OcultarBotones();
      $("#Niveles").empty();
    break;
    case 'Niveles':
      OcultarBotones();
      $("#Niveles").empty();
    break;
    case 'CostadoM':
      $("#CostadoM").empty();
      $("#FilasM").empty();
      $("#TorresM").empty();
      $("#NivelesM").empty();
      break;
    case 'FilasM':
        $("#FilasM").empty();
        $("#TorresM").empty();
        $("#NivelesM").empty();
    break;
    case 'TorresM':
        $("#TorresM").empty();
        $("#NivelesM").empty();
    break;
    case 'NivelesM':
      $("#NivelesM").empty();
    break;

    default:

  }
}

//Obtiene los tanques que pertenecen a esta area
async function CargarTabla(sel){
  if(sel.value!=""){
    try {
      empezar();
      const url='RestApi/GET/'+getApi+'?Rack='+sel.value;
      var result = await conexion("GET",url,"");
      var parsed =JSON.parse(result);
      $("#Agregar").hide();
      $("#Mover").hide();
      $("#Editar").hide();
      crearTablaJson(parsed,'#barriles');
      MostrarBotones();
      parar();
    } catch(error) {
      mensajeError(error);
    }
  }else{
    OcultarBotones();
  }
}
//Se muestran los botones cuando el usuario haya buscado una tabla
function MostrarBotones(){
  if($('#barriles tbody td').length==0 || $('#bodega :selected').text().includes('EMBARRILADO')){
    $("#Agregar").show();
  }
  if($('#barriles tbody td').length>0){
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
  cargaPlantas();
  $("#scrollingtable").hide();
  $("#Agregar").hide();
  $("#Quitar").hide();
  $("#Mover").hide();
  $("#Editar").hide();
  $('#barriles').DataTable({
    responsive: true,
    "bPaginate": false,
    "paging":   false,
    "ordering": false,
    "info":     false,
    searching: false,
  });

});
