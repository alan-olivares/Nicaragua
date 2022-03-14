//Existe una sesión activa y se va hacía el inicio
if(localStorage['sesion_timer'] || ''!=''){
  var fecha=new Date(localStorage['sesion_timer']);
  var fechaActual=new Date();
  //fecha=fecha.setMinutes(fecha.getMinutes() + 60);
  fecha.setTime(fecha.getTime() + (30 * 60 * 1000));
  //alert(fecha);
  if(fechaActual.getTime()<fecha.getTime()){
    localStorage['sesion_timer']=new Date();
    window.location.replace(localStorage['paginaI']);
  }
}
