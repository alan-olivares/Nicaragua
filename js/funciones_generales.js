ObtenerNotificaciones();
const getNode='http://'+$(location).attr('hostname')+":1337/";
revisarPermisosInterface();
$(".scrollingtable2").css("height",screen.height-260);
async function ObtenerNotificaciones(){
  var numero=await conexion("GET", 'RestApi/GET/get_notificaciones.php','');
  document.getElementById("letrero1").innerHTML=numero;
  document.getElementById("letrero2").innerHTML=numero;
  document.getElementById("letrero3").innerHTML=numero;
  document.getElementById("letrero4").innerHTML="Tienes "+numero+" solicitudes pendientes";
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
async function ActualizarFechasLotes(url){
  try {
    var result = await conexion("GET",url,"");
    var parsed =JSON.parse(result);
    var array1= [];
    array1.push(dateFormatoCompleto(new Date,'-'));
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
  $("#data_1 .input-group.date").datepicker().datepicker("setDate", new Date());


}

function empezar(){
  $(".sk-spinner-three-bounce div").css("visibility","visible");
}
function parar(){
  $(".sk-spinner-three-bounce div").css("visibility","hidden");
}
async function mensajeOpcional2(texto){
  try{
      return await swal({
            title: "Aviso",
            text: texto,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Si",
            cancelButtonText: "No",
            closeOnConfirm: true
        });
      // SUCCESS
    }catch(e){
        // Fail!
        console.error(e);
        return false;
    }
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
              if(!xhttp.response.includes("..Error..")){
                console.log(xhttp.response);
                resolve(xhttp.response);
              }else{
                reject(xhttp.response);
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
  $(tabla+' > thead').empty();
  $(tabla+' > tbody').empty();
  var content="";
  if(json.length>0){
    var keys=Object.keys(json[0]);
    content="<tr>";
    keys.forEach(function(key) {
      content+='<th>'+key+'</th>';
    });
    content+="</tr>";
    $(tabla+' > thead').append(content);
    content="";
    for (var i = 0; i < json.length; i++) {
      content+="<tr>";
      keys.forEach(function(key) {
        content+='<td style="max-width:100%;white-space:nowrap;">'+(json[i][key]==null?'':json[i][key])+'</td>';
      });
      content+="</tr>";
    }
    $(tabla+' > tbody').append(content);
  }else{
    $(tabla+' > thead').append('<tr><th>Tabla sin datos</th></tr>');
  }

  return content;
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
  for (var i = 0; i < String(etiqueta).length; i++) {
    if(String(etiqueta).charAt(0)==="0"){
      etiqueta=etiqueta.substring(1,String(etiqueta).length);
    }else{
      return etiqueta;
    }
  }
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
