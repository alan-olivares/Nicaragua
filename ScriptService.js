var Service = require('node-windows').Service;

// Creamos el servicio
const path = require('path');
var direccion=path.join(__dirname,'/index.js')
var svc = new Service({
  name:'Reportes SERLicorera',
  description: 'Servicio en NodeJS para mapear solicitudes de reportes',
  script: direccion
});

//Cuando se instale el servicio, se iniciará
svc.on('install',function(){
  svc.start();
});
//Instalamos el servicio
svc.install();
