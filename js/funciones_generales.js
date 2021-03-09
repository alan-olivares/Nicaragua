ObtenerNotificaciones();
revisarPermisosInterface();
async function ObtenerNotificaciones(){
  var numero=await conexion("GET", 'RestApi/GET/get_notificaciones.php?usuario='+localStorage['usuario']+'&pass='+localStorage['password'],'');
  document.getElementById("letrero1").innerHTML=numero;
  document.getElementById("letrero2").innerHTML=numero;
  document.getElementById("letrero3").innerHTML=numero;
  document.getElementById("letrero4").innerHTML="Tienes "+numero+" solicitudes pendientes";
}

function FormatDate(fecha){
  if(fecha!=null){
    var res = fecha.split("-");
    return res[0]+"-"+res[1]+"-"+res[2];
  }else{
    return "";
  }
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

async function revisarPermisos(pagina){
  var permisos=await conexion("GET", 'RestApi/GET/get_permisos.php?usuario='+localStorage['usuario']+'&pass='+localStorage['password'],'');
  var reg=false;
  pagina.forEach(function(valor) {
    if(permisos.includes(","+valor+",")){//el servidor separa cada permiso con una , así no habrá problema de repeticiones
      reg=true;
    }
  });
  return reg;
}
async function revisarPermisosInterface2(){
  var permisos=await conexion("GET", 'RestApi/GET/get_permisos.php?usuario='+localStorage['usuario']+'&pass='+localStorage['password'],'');
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
  var permisos=await conexion("GET", 'RestApi/GET/get_permisos.php?usuario='+localStorage['usuario']+'&pass='+localStorage['password'],'');
  if(!permisos.includes(",1,")){
    $(".uno").remove();
  }
  if(!permisos.includes(",2,")){
    $(".dos").remove();
  }
  if(!permisos.includes(",3,")){
    $(".tres").remove();
  }
  if(!permisos.includes(",4,")){
    $(".cuatro").remove();
  }
  if(!permisos.includes(",3,") && !permisos.includes(",4,")){
    $(".tres-cuatro").remove();
  }
  if(!permisos.includes(",1,") && !permisos.includes(",2,")){
    $(".uno-dos").remove();
  }
}

function conexion(method, url,params) {
    return new Promise(function (resolve, reject) {
        let xhttp = new XMLHttpRequest();
        xhttp.open(method, url,true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.onload = function () {
            if (this.readyState == 4 && this.status == 200) {
              if(!xhttp.response.includes("..Error..")){
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
function GenerarEtiqueta(consecutivo){
  var n = String(consecutivo).length;
  for (var i = n; i < 6; i++) {
    consecutivo="0"+consecutivo;
  }
  return "0101"+consecutivo;
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

//limpia la sesión
function limpiar() {
  localStorage['sesion_timer']="";
  window.location.replace("login.php");
}
