ObtenerNotificaciones();
revisarTema();
const getNode='http://'+$(location).attr('hostname')+":1337/";
revisarPermisosInterface();
$(".scrollingtable2").css("height",window.innerHeight-260);
$(".scrollingtable").css("height",window.innerHeight-330);

async function ObtenerNotificaciones(){
  var numero=await conexion("GET", 'RestApi/GET/get_notificaciones.php','');
  document.getElementById("letrero1").innerHTML=numero;
  document.getElementById("letrero2").innerHTML=numero;
  document.getElementById("letrero3").innerHTML=numero;
  document.getElementById("letrero4").innerHTML="Tienes "+numero+" solicitudes pendientes";
}
async function setDates(fechas,format){
  try {
    var fecha=await conexion("GET", 'RestApi/GET/get_time.php?format='+format,'');
    fechas.forEach((item, i) => {
      $(item).val(fecha);
    });
    return fecha;
  } catch (e) {
    return dateFormatoCompleto(new Date,'-')
  }

}
function revisarTema(){
  $('.navbar-static-top').css({"background":"#f3f3f4"});
  if(localStorage['tema']==='2'){
    $('.gray-bg').removeClass('gray-bg').addClass('gray-bg-dark');
    $('.navbar-static-top').removeClass('navbar-static-top').addClass('navbar-static-top-dark');
    $("#page-wrapper").css({"background-color":"#333333"});
    entradasDark(['select','input','.footer','.panel-body','.white-bg','.ui-dialog-content','.ibox-content','.panel-default','.search-field']);
    $('.ui-dialog-titlebar').css({"color":"#a2a1a1"});
    $('.ui-dialog-titlebar').css({"background-color":"#2f2f2f"});
    $('.panel-title').css({"color":"#a2a1a1"});
    $('.panel-heading').css({"background-color":"#2f2f2f"});
    //$('.navbar-fixed-top').css({"background-color":"#a2a1a1"});
    $('.barriles_length').css({"color":"#a2a1a1"});
    $('.table-bordered').removeClass('table-bordered').addClass('table-bordered-dark');

  }
}
String.prototype.formatUnicorn = String.prototype.formatUnicorn ||
function () {
  "use strict";
  var str = this.toString();
  if (arguments.length) {
    var t = typeof arguments[0];
    var key;
    var args = ("string" === t || "number" === t) ?
      Array.prototype.slice.call(arguments)
      : arguments[0];

    for (key in args) {
      str = str.replace(new RegExp("\\{" + key + "\\}", "gi"), args[key]);
    }
  }

  return str;
};

function findString(text) {
  if(event.key === 'Enter') {
      if(!window.find(text)){
        alert(text+' no se encuentra dentro de la página');
      }
  }
}

function quitarSeleccion(buton){
  $(".selected").removeClass("selected");
  $(".seleccionado").removeClass("seleccionado");
  $(buton).attr("hidden",true);
}
function lengthSeleccion(){
  var elements = document.getElementsByClassName("selected");
  return elements.length;
}

function entradasDark(valores){
  valores.forEach((item, i) => {
    $(item).css({"color":"#a2a1a1"});
    $(item).css({"background-color":"#181818"});
    $(item+":disabled").css({"background-color":"#303030"});
  });

}
function FormatDate(fecha){
  if(fecha!=""){
    var res = fecha.split("-");
    return res[0]+"-"+res[1]+"-"+res[2];
  }else{
    return "";
  }
}
function PickerToNormal(fecha){
  if(fecha!=null){
    var res = fecha.split("-");
    return res[2]+"-"+res[1]+"-"+res[0];
  }else{
    return "";
  }
}
function NormalToPicker(fecha){
  if(fecha!=null){
    var res = fecha.split("-");
    return res[1]+"-"+res[0]+"-"+res[2];
  }else{
    return "";
  }
}
async function ActualizarFechasLotes(url){
  try {
    var result = await conexion("GET",url,"");
    var parsed =JSON.parse(result);
    var array1= [];
    var currentDay= await setDates([],'Y-m-d');
    array1.push(currentDay);
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
            var fecha=dateFormatoCompleto(date,'-');
            if(array1.indexOf(fecha) != -1 ){
              return (true,"azul");
            }else{
              return (false);
            }
        }
    });


  }catch(error){
    mensajeError(error);
  }
  $("#data_1 .input-group.date").datepicker().datepicker("setDate", currentDay);


}

function empezar(){
  $(".sk-spinner-three-bounce div").css("visibility","visible");
}
function parar(){
  $(".sk-spinner-three-bounce div").css("visibility","hidden");
}

function dateFormato(date,sep) {
  var dia=date.getDate();
  var mes=date.getMonth();
  var year=date.getFullYear();
  return ((dia<10)?'0'+dia:dia)+sep+((mes<9)?'0'+(mes+1):(mes+1))+sep+String(year).substring(2);
}
function dateFormatoCompleto(date,sep) {
  var dia=date.getDate();
  var mes=date.getMonth();
  var year=date.getFullYear();
  return year+sep+((mes<9)?'0'+(mes+1):(mes+1))+sep+((dia<10)?'0'+dia:dia);
}

function mensajeOpcional(texto) {
  return new Promise(function(resolve, reject) {
        swal({
              title: "Aviso",
              text: texto,
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "Si",
              cancelButtonText: "No",
              closeOnConfirm: true
          },
          function (isConfirm) {
            resolve(isConfirm);
          });
  });
}
function mensajeSimple(texto){
  return new Promise(function(resolve, reject) {
      swal({
        title: "¡Hecho!",
        text: texto,
        type: "success",
        showCancelButton: false,
        confirmButtonColor: "#1c84c6",
        confirmButtonText: "OK",
        cancelButtonText: "No",
        closeOnConfirm: true
      },
      function (isConfirm) {
            resolve(isConfirm);
          });
  });
}
function mensajeWarning(texto){
  return new Promise(function(resolve, reject) {
      swal({
        title: "¡Advertencia!",
        text: texto,
        type: "warning",
        showCancelButton: false,
        confirmButtonColor: "#1c84c6",
        confirmButtonText: "OK",
        closeOnConfirm: true
      },
      function (isConfirm) {
            resolve(isConfirm);
          });
  });
}
function mensajeError(error){
  return new Promise(function(resolve, reject) {
    swal({
      title: "Error",
      text: error,
      type: "error",
      showCancelButton: false,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: "Cerrar",
      cancelButtonText: "No",
      closeOnConfirm: true
    },
      function (isConfirm) {
            resolve(isConfirm);
          });
  });
}

async function permisos(valores){
  var perm=await revisarPermisos(valores);
  if(!perm){
    if(localStorage['paginaI']!=null && !$(location).attr('pathname').includes(localStorage['paginaI'])){
      window.location.replace(localStorage['paginaI']);
    }else{
      limpiar();
    }
  }
}

async function revisarPermisos(pagina){
  var permisos=await conexion("GET", 'RestApi/GET/get_permisos.php','');
  var reg=false;
  pagina.forEach(function(valor) {
    if(permisos.includes(","+valor+",")){//el servidor separa cada permiso con una ,
      reg=true;
    }
  });
  return reg;
}
async function revisarPermisosInterface2(){
  var permisos=await conexion("GET", 'RestApi/GET/get_permisos.php','');
  if(permisos.includes(",1,")){
    $(".uno").css("visibility","visible");
  }
  if(permisos.includes(",2,")){
    $(".dos").css("visibility","visible");
  }
  if(permisos.includes(",3,")){
    $(".tres").css("visibility","visible");
  }
  if(permisos.includes(",4,")){
    $(".cuatro").css("visibility","visible");
  }
}
async function revisarPermisosInterface(){
  var permisos=await conexion("GET", 'RestApi/GET/get_permisos.php','');
  if(!permisos.includes(",1,")){$(".uno").remove()}
  if(!permisos.includes(",2,")){$(".dos").remove()}
  if(!permisos.includes(",3,")){$(".tres").remove()}
  if(!permisos.includes(",4,")){$(".cuatro").remove()}
  if(!permisos.includes(",5,")){$(".cinco").remove()}
  if(!permisos.includes(",6,")){$(".seis").remove()}
  if(!permisos.includes(",7,")){$(".siete").remove()}
  if(!permisos.includes(",8,")){$(".ocho").remove()}
  if(!permisos.includes(",9,")){$(".nueve").remove()}
  if(!permisos.includes(",10,")){$(".diez").remove()}
  if(!permisos.includes(",11,")){$(".once").remove()}
  if(!permisos.includes(",3,") && !permisos.includes(",4,")){$(".tres-cuatro").remove()}
  if(!permisos.includes(",1,") && !permisos.includes(",2,")){$(".uno-dos").remove()}
  if(!permisos.includes(",2,") && !permisos.includes(",5,")){$(".dos-cinco").remove()}
}

function conexion(method, url,params) {
    return new Promise(function (resolve, reject) {
        let xhttp = new XMLHttpRequest();
        xhttp.open(method, url,true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded;charset=UTF-8");
        xhttp.setRequestHeader("Authorization", "basic " + btoa(localStorage['usuario'] + ":" + localStorage['password']) );
        xhttp.onload = function () {
            if (this.readyState == 4 && this.status == 200) {
              if(xhttp.response.includes("..Error..")){
                reject(xhttp.response.replace("..Error..",""));
              }else if(xhttp.response.includes("..Desautorizado..")){
                limpiar();
              }else{
                resolve(xhttp.response);
              }

            } else {
                reject({
                    status: this.status,
                    statusText: xhttp.statusText
                });
            }
        };
        xhttp.onerror = function () {
            reject({
                status: this.status,
                statusText: xhttp.statusText
            });
        };
        xhttp.send(params);
    });
}
function llenarSelect(select,valor,texto,json){
  if($(select).is(':empty')){
    $(select).append($('<option>', {
      value: "",
      text: ""
    }));
  }
  for (i = 0; i < json.length; i++) {
    $(select).append($('<option>', {
      value: json[i][valor],
      text: json[i][texto]
    }));
  }
}
function crearTablaJson(json,tabla){
  var content="";
  if(json.length>0){
    var keys=Object.keys(json[0]);
    content="<tr>";
    keys.forEach(function(key) {
      content+='<th>'+key+'</th>';
    });
    content+="</tr>";
    $(tabla+' > thead').html(content);
    content="";
    for (var i = 0; i < json.length; i++) {
      content+="<tr>";
      keys.forEach(function(key) {
        content+='<td style="max-width:100%;white-space:nowrap;">'+(json[i][key]==null?'':json[i][key])+'</td>';
      });
      content+="</tr>";
    }
    $(tabla+' > tbody').html(content);
    //$('#cargandoIndicador').text('');

  }else{
    $(tabla+' > thead').html('<tr><th>Tabla sin datos</th></tr>');
    $(tabla+' > tbody').html('<tr><td></td></tr>');
  }
  revisarTema();
}

function GenerarEtiqueta(consecutivo,tipo){
  var n = String(consecutivo).length;
  for (var i = n; i < 6; i++) {
    consecutivo="0"+consecutivo;
  }
  return "01"+tipo+consecutivo;
}
function EtiquetaAConsecutivo(etiqueta){
  etiqueta=etiqueta.substring(4,10);
  return String(parseInt(etiqueta));
}

//Buscar las etiquetas de un select para emparejar values con etiquetas
function setSelectedValue(selectObj, valueToSet) {
  for (var i = 0; i < selectObj.options.length; i++) {
    if (selectObj.options[i].text.includes(valueToSet)) {
        selectObj.options[i].selected = true;
        return;
    }
  }
}

function fomatoNumero(numero){
  if(numero==null)
    return '';
  if(numero==='')
    return '';
  if(numero===undefined)
    return '';
  var can=parseFloat(numero);
  return ((Math.round(can * 100) / 100).toFixed(3).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
}
//Llenado de pdf
function ponerEncabezadoPdf(doc,titulo,iso){
  doc.setFontSize(11);
  doc.text("Fecha: "+PickerToNormal($('#fecha').val()),480,20);
  doc.text("Trazabilidad de Barriles y Rones Envejecidos",300,60);
  doc.addImage(document.getElementById('imagenLogo'), 10, 10);
  doc.text(titulo,40,110);
  doc.text(iso,480,110);
}
function ponerTablasPdf(doc,tablas,inicio){
  var res;
  var options = {
    createdCell: function (cell, data) {
      cell.styles.fillColor = '#ffffff';
      cell.styles.textColor = "black";
    },
    theme: 'grid',
    tableWidth: 'auto',
    columnWidth: 'auto',
    margin: {
      top: 80
    },
    styles: {
      overflow: 'linebreak',
      fillColor: "#F5F5F6",
      textColor:"black"
    },
    fontSize:9,
    startY: inicio + 20
   };
  for (var i = 0; i < tablas.length; i++) {
    res = doc.autoTableHtmlToJson(document.getElementById(tablas[i]));
    doc.autoTable(res.columns, res.data, options);
    options.startY=doc.autoTableEndPosY() + 20;
  }
}
//limpia la sesión
function limpiar() {
  localStorage['sesion_timer']="";
  window.location.replace("login.php");
}
