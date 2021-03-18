const getApi="get_validar_informacion.php";
const postApi="post_validar_informacion.php";
$( function() {
  var options={
    open: function() {
        $(this).closest(".ui-dialog")
        .find(".ui-dialog-titlebar-close")
        .removeClass("ui-dialog-titlebar-close")
        .html("<span class='ui-button-icon-primary ui-icon ui-icon-closethick'></span>");
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
    maxHeight: 570,
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
  if(document.getElementById("NivelesM").value===document.getElementById("Niveles").value){
    mensajeError("Este barril ya pertenece a esta bodega");
  }else if(document.getElementById("NivelesM").value===""){
    mensajeError("Por favor completa todos los datos");
  }else{
    var elements = document.getElementsByClassName("selected");
    var check="";
    try{
      for (var i = elements.length-1; i >= 0; i--) {
        var eee=elements[i].querySelectorAll('td');
        var url='RestApi/POST/'+postApi;
        var params='usuario='+localStorage['usuario']+'&pass='+localStorage['password']+'&evento=Mover&consecutivo='+eee[0].innerHTML+
                  "&IdPallet="+document.getElementById("NivelesM").value+"&CBarriles="+(i+1);
        var result=await conexion("POST", url,params);
        check=result;

      }
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
  if(eee[10].innerHTML !=='Vacío (Plantel)' && document.getElementById("estado").options[document.getElementById("estado").selectedIndex].text==='Vacío (Plantel)'){
    if(await mensajeOpcional('El barril se encuentra lleno, si lo quieres cambiar a vacío generará los datos de un barril vacío ¿Quieres continuar?')){
      try{
        var params='usuario='+localStorage['usuario']+'&pass='+localStorage['password']+'&evento=Editar&consecutivo='+eee[0].innerHTML+
                '&IdPallet='+document.getElementById("Niveles").value+'&CBarriles=0&restablecer=vacio';
        var result=await conexion("POST", 'RestApi/POST/'+postApi,params);
        setTimeout(async function() {
          await mensajeSimple(result);
          dialogEditar.dialog( "close" );
        }, 500);

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
        var params='usuario='+localStorage['usuario']+'&pass='+localStorage['password']+'&evento=Editar&consecutivo='+eee[0].innerHTML+
                '&IdPallet='+document.getElementById("Niveles").value+'&CBarriles=0&restablecer=pasado';
        var result=await conexion("POST", 'RestApi/POST/'+postApi,params);
        setTimeout(async function() {
          await mensajeSimple(result);
          dialogEditar.dialog( "close" );
        }, 500);
      }catch(error){
        setTimeout(async function() {
          mensajeError(error);
        }, 500);
      }
    }
    return;
  }else{
    if(eee[5].innerHTML !== document.getElementById("tapa").value){
      try{
        await conexion("GET", 'RestApi/GET/'+getApi+'?usuario='+localStorage['usuario']+'&pass='+localStorage['password']+'&tapa='+document.getElementById("tapa").value+"&year="+document.getElementById("Aalcohol").value,'');

        campos=campos+"NoTapa="+document.getElementById("tapa").value+"&";
      }catch(error){
        //Tapa ya existe y mostrará el error aquí
        mensajeError(error);
        return;
      }
    }
    if(document.getElementById("alcohol").value===""){
      mensajeError("Selecciona una opción de recepción y alcohol");
      return;
    }
    try{
      var result =await conexion("GET", 'RestApi/GET/'+getApi+'?usuario='+localStorage['usuario']+'&pass='+localStorage['password']+'&ConsecutivoLoteB='+eee[0].innerHTML,'');
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
        var params='usuario='+localStorage['usuario']+'&pass='+localStorage['password']+'&evento=Editar&consecutivo='+eee[0].innerHTML+
                  '&IdPallet='+document.getElementById("Niveles").value+'&CBarriles=0&'+campos;
        result=await conexion("POST", 'RestApi/POST/'+postApi,params);
        await mensajeSimple(result);
        dialogEditar.dialog( "close" );
      }else{
        mensajeError("Cambia algún dato antes de guardar");
      }

    }catch(error){
      mensajeError(error);
    }
  }
}
async function GuardarAgregar(){
  if(document.getElementById('etiquetaA').value!=""){
    if(!document.getElementById("ubicacionA").value.includes(document.getElementById("Niveles").value)){
      var query="UPDATE WM_Barrica SET IdPallet=(select IdPallet from WM_Pallet where RackLocID="+document.getElementById("Niveles").value+") where Consecutivo="+EtiquetaAConsecutivo(document.getElementById("etiquetaA").value);
      var url='RestApi/POST/'+postApi;
      var params='usuario='+localStorage['usuario']+'&pass='+localStorage['password']+'&evento=Agregar&consecutivo='+
      EtiquetaAConsecutivo(document.getElementById("etiquetaA").value)+"&IdPallet="+document.getElementById("Niveles").value+"&CBarriles=1";
      try{
        var result=await conexion("POST", url,params);
        await mensajeSimple(result);
        dialogAgregar.dialog( "close" );
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
      const url='RestApi/GET/'+getApi+'?usuario='+localStorage['usuario']+'&pass='+localStorage['password']+'&consecutivo='+document.getElementById('concecutivoA').value;
      var result = await conexion("GET",url,"");
      var parsed =JSON.parse(result);
      if(parsed.length>0){
        document.getElementById('etiquetaA').value=GenerarEtiqueta(parsed[0]['Consecutivo']);
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
//Inicializa y abre dialogo de editar
 async function Editar(){
  var elements = document.getElementsByClassName("selected");
  if(elements.length==1){
    var eee=elements[0].querySelectorAll('td');
    document.getElementById("etiqueta").value =GenerarEtiqueta(eee[0].innerHTML);
    setSelectedValue(document.getElementById("uso"), eee[6].innerHTML);
    setSelectedValue(document.getElementById("edad"), eee[7].innerHTML);
    document.getElementById("tapa").value=eee[5].innerHTML;
    document.getElementById("litros").value=eee[1].innerHTML;
    document.getElementById("revisado").valueAsDate=new Date(eee[2].innerHTML);
    document.getElementById("relleno").valueAsDate=new Date(eee[3].innerHTML);
    document.getElementById("Abarrica").value=eee[4].innerHTML;
    setSelectedValue(document.getElementById("estado"), eee[10].innerHTML);
    document.getElementById("Aalcohol").value=eee[8].innerHTML;
    var result =await conexion("GET", 'RestApi/GET/'+getApi+'?usuario='+localStorage['usuario']+'&pass='+localStorage['password']+'&ConsecutivoLoteB='+eee[0].innerHTML,'');
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
  var result =await conexion("GET", 'RestApi/GET/'+getApi+'?usuario='+localStorage['usuario']+'&pass='+localStorage['password']+'&fechaLoteB='+fecha,'');
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
      $(table).find('tbody').append("<tr><td>"+GenerarEtiqueta(eee[0].innerHTML)+"</td></tr>");
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
      const url='RestApi/GET/'+getApi+'?usuario='+localStorage['usuario']+'&pass='+localStorage['password']+'&'+tipo+'='+sel.value;
      var result = await conexion("GET",url,"");
      var parsed =JSON.parse(result);
      limpiarCampos(boton);
      //se agrega un elemento vacio
      var x = document.createElement("OPTION");
      x.setAttribute("value", "");
      var t = document.createTextNode("");
      x.appendChild(t);
      document.getElementById(boton).appendChild(x);
      //se agregan los elementos obtenidos en el servidor
      for (i = 0; i < parsed.length; i++) {
        var x = document.createElement("OPTION");
        x.setAttribute("value", parsed[i][valor]);
        var t = document.createTextNode(parsed[i][etiqueta]);
        x.appendChild(t);
        document.getElementById(boton).appendChild(x);
      }
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

//Obtiene los barriles que pertenecen a esta area
async function CargarTabla(sel){
  if(sel.value!=""){
    try {
      empezar();
      const url='RestApi/GET/'+getApi+'?usuario='+localStorage['usuario']+'&pass='+localStorage['password']+'&Rack='+sel.value;
      var result = await conexion("GET",url,"");
      var parsed =JSON.parse(result);
      $("#Agregar").hide();
      $("#Mover").hide();
      $("#Editar").hide();
      var transform = {"tag":"table", "children":[
        {"tag":"tbody","children":[
          {"tag":"tr","children":[
            {"tag":"td","html":"${Consecutivo}"},
            {"tag":"td","html":"${Capacidad}"},
            {"tag":"td","html":"${Revisado}"},
            {"tag":"td","html":"${Relleno}"},
            {"tag":"td","html":"${Año}"},
            {"tag":"td","html":"${NoTapa}"},
            {"tag":"td","html":"${Uso}"},
            {"tag":"td","html":"${Edad}"},
            {"tag":"td","html":"${Recepcion}"},
            {"tag":"td","html":"${Alcohol}"},
            {"tag":"td","html":"${Estado}"},
          ]}
        ]}
      ]};
      $('#barriles > tbody').html(json2html.transform(parsed,transform));
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
  if(document.getElementById("barriles").rows.length<=9){
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
});

async function ActualizarFechasLotes(){
  try {
    const url='RestApi/GET/'+getApi+'?usuario='+localStorage['usuario']+'&pass='+localStorage['password']+'&fechasLotes=true';
    var result = await conexion("GET",url,"");
    var parsed =JSON.parse(result);
    var array1= [];
    for (var i = 0; i < parsed.length; i++) {
      array1.push(parsed[i].Fecha);
    }
    $('#data_1 .input-group.date').datepicker({
        todayBtn: "linked",
        keyboardNavigation: false,
        forceParse: false,
        autoclose: true,
        format: 'yyyy-mm-dd',
        beforeShowDay: function(date) {
            var fecha=date.getFullYear()+"-"+
            (((date.getMonth()+1)<10)?"0"+(date.getMonth()+1):(date.getMonth()+1))+"-"+
            ((date.getDate()<10)?"0"+date.getDate():date.getDate());
            if(array1.indexOf(fecha) != -1){
              return (true,"azul");
            }else{
              return (false);
            }
        }
    });

  }catch(error){
    mensajeError(error);
  }


}
async function permisos(){
  var perm=await revisarPermisos(["2"]);
  if(!perm){
    window.location.replace("index.php");
  }
}

$(document).ready(function(){
  //Por default los botones y la tabla deben de estar desactivado
  ActualizarFechasLotes();
  permisos();
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
