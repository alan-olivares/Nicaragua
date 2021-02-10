if(localStorage['sesion_timer'] || ''!=''){
  var fecha=new Date(localStorage['sesion_timer']);
  var fechaActual=new Date();
  //fecha=fecha.setMinutes(fecha.getMinutes() + 60);
  fecha.setTime(fecha.getTime() + (30 * 60 * 1000));
  //alert(fecha);
  if(fechaActual.getTime()>fecha.getTime()){
    localStorage['sesion_timer']='';
    alert('Sesión cerrada por inactividad');
    var gettingCurrent = browser.windows.getCurrent();
    gettingCurrent.close();
  }else{
    localStorage['sesion_timer']=new Date();
  }
}else{
  localStorage['sesion_timer']="";
  alert('Vuelve iniciar sesión para acceder');
  var gettingCurrent = browser.windows.getCurrent();
  gettingCurrent.close();
}
