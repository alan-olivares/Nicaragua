'use strict'

const servidor="http://localhost:80/";
const Excel = require('exceljs');
const XlsxPopulate = require('xlsx-populate');
var http = require('http');
var express = require('express');
var app = express();
var fs = require('fs');
var cors = require('cors');
var corsOptions = {
  origin: function(origin, callback){
    return callback(null, true);
  },
  optionsSuccessStatus: 200,
  credentials: true
};
app.use(cors(corsOptions));
var autenticacion="";

app.get('/', function(req, res) {
  autenticacion = req.headers.authorization || '';
  try {
    if(autenticacion!==''){
      var id=Math.floor(Math.random() * 1000) + 1;//Id aleatorio para solicitudes entrantes
      if(req.query.reporte==='RTrasiegoVaciados'){
        llenarRTrasiegoVaciados('RTrasiegoVaciados',req.query.fecha,req.query.tanque,res,req.query.tipo,id);
      }else if(req.query.reporte==='RTrasiegoRemision'){
        llenarRTrasiegoRemision('RTrasiegoRemision',req.query.fecha,req.query.tanque,res,req.query.tipo,id);
      }else if(req.query.reporte==='RLlenadoLlenada'){
        llenarRLlenadoLlenada('RLlenadoLlenada',req.query.fecha,res,req.query.tipo,id);
      }else if(req.query.reporte==='RLlenadoMantenimineto'){
        llenarRLlenadoMantenimineto('RLlenadoMantenimineto',req.query.fecha,req.query.fecha2,res,req.query.tipo,id);
      }else if(req.query.reporte==='RLlenadoRevisado'){
        llenarRLlenadoRevisado('RLlenadoRevisado',req.query.fecha,res,req.query.tipo,id);
      }else if(req.query.reporte==='RGerencia'){
        llenarRGerencia('RGerencia',res,req.query.tipo,id);
      }else if(req.query.reporte==='RTrasiegoHojaAnalisis'){
        llenarRTrasiegoHojaAnalisis('RTrasiegoHojaAnalisis',req.query.fecha,req.query.tanque,res,req.query.tipo,id);
      }else if(req.query.reporte==='RRellenoOperacion'){
        llenarRRellenoOperacion('RRellenoOperacion',req.query.fecha,req.query.ope,res,req.query.tipo,id);
      }else if(req.query.reporte==='inventario'){
        var bodega=req.query.bodega;
        var alcohol=req.query.alcohol;
        var llenada=req.query.llenada;
        var uso=req.query.uso;
        llenarInventario('Inventario-BaPlantel',bodega,alcohol,llenada,uso,res,req.query.tipo,id);
      }else if(req.query.reporte==='barriles_plantel'){
        llenarBarrilesPlantel('Inventario-BaPlantel',res,req.query.tipo,id);
      }else if(req.query.reporte==='tanques_plantel'){
        llenarTanquesPlantel('tanques_plantel',res,req.query.tipo,id);
      }else if(req.query.reporte==='llenados'){
        llenarBarrilesLlenados('llenados',res,req.query.fecha1,req.query.fecha2,req.query.tipo,id);
      }else if(req.query.reporte==='rellenados'){
        llenarBarrilesReLlenados('rellenados',res,req.query.fecha1,req.query.fecha2,req.query.tipo,id)
      }else if(req.query.reporte==='trasiego'){
        llenarBarrilesTrasiego('trasiego',res,req.query.fecha1,req.query.fecha2,req.query.tipo,id)
      }else if(req.query.reporte==='trasiegoHoover'){
        llenarTanquesTrasiego('trasiegoHoover',res,req.query.fecha1,req.query.fecha2,req.query.tipo,id)
      }else  if(req.query.reporte==='detalleBarril'){
        var almamcen=req.query.almacen;
        var area=req.query.area;
        var seccion=req.query.seccion;
        var alcohol=req.query.alcohol;
        var cod=req.query.cod;
        var fecha=req.query.fecha;
        llenarDetalleBarril('detalleBarrilXSeccion',res,almamcen,area,seccion,alcohol,cod,fecha,req.query.tipo,id)
      }else if(req.query.reporte==='detallebarrilesPlantel'){
        var almacen=req.query.almacen;
        var area=req.query.area;
        var cod=req.query.cod;
        llenarDetalleBarrilPlantel('detalleBarrilesPlantel',res,almacen,area,cod,req.query.tipo,id)
      }else if(req.query.reporte==='detalletanquesPlantel'){
        var almacen=req.query.almacen;
        var area=req.query.area;
        llenarDetalleTanquePlantel('detalleTanquesPlantel',res,almacen,area,req.query.tipo,id)
      }
    }else{
      res.send('..Error.. Problema con la autenticación');
    }
  } catch (e) {
    console.log(e);
    res.send('..Error.. Hubo un problema al procesar la solicitud, intenta de nuevo más tarde');
  }


});


app.post('/', function(req, res){

});

http.createServer(app).listen(1337);// Puerto en el que esta escuchando las peticiones

function convertirArchivo(archivo,res,id,destino){
  const libre = require('libreoffice-convert');
  const path = require('path');
  const fs = require('fs');
  const enterPath = path.join(__dirname,'/archivos/'+archivo+'_'+id+'_temporal.xlsx');
  const outputPath = path.join(__dirname, '/archivos/'+archivo+'_'+id+'_temporal.'+destino);
  const file = fs.readFileSync(enterPath);
  libre.convert(file, '.'+destino, undefined, async function (err, done){
    if (err) {//Imprimimos en consola si hubo un error
      console.log(`Error converting file: ${err}`);
      fs.unlinkSync(__dirname+'/archivos/'+archivo+'_'+id+'_temporal.'+aOrigen);
      res.send('..Error.. Se produjo un error al convertir el archivo en pdf');
    }
    await fs.writeFileSync(outputPath, done);//Se escribe el pdf y esperamos a que este listo
    res.send('archivos/'+archivo+'_'+id+'_temporal.'+destino);
    setTimeout(function() {
      fs.unlinkSync(__dirname+'/archivos/'+archivo+'_'+id+'_temporal.xlsx');
      fs.unlinkSync(__dirname+'/archivos/'+archivo+'_'+id+'_temporal.'+destino);
    }, 5000);
});
}
function PickerToNormal(fecha){
  if(fecha!=null){
    var res = fecha.split("-");
    return res[2]+"-"+res[1]+"-"+res[0];
  }else{
    return "";
  }
}
async function llenarRTrasiegoVaciados(archivo,fecha,tanque,res,tipo,id){
  XlsxPopulate.fromFileAsync('archivos/'+archivo+'.xlsx')
    .then(async workbook => {
      try {
        const hoja="Principal";
        workbook.sheet(hoja).cell("I8").value(PickerToNormal(fecha));
        var url=servidor+'RestApi/GET/get_ReportesTrasiego.php?FSI82498=true&fecha='+fecha+'&tanque='+tanque;
        var result = await conexion(url);
        var parsed =JSON.parse(result);
        if(parsed.length>0){
          var totalCapa=0,capa=0,Barriles=0,BarrilesAnnio=0,capaAnnio=0,pos=11;
          var uso=parsed[0]['Uso'];
          var anio=parsed[0]['Año'];
          var cantidad=parseInt(parsed[0]['Cantidad']);
          nuevoUsoVaciados(workbook.sheet(hoja),10);
          for (var i = 0; i < parsed.length; i++) {
            if(anio!==parsed[i]['Año']){
              workbook.sheet(hoja).cell("D"+pos).value([['','',Barriles+' Barriles','Total tipo barril']]).style({border:true,"borderColor": "F5F5F6","bold": true});
              workbook.sheet(hoja).cell("H"+pos).value(capa).style({border:true,"borderColor": "F5F5F6","numberFormat": "#,##0.00","bold": true});
              pos+=2;
              workbook.sheet(hoja).cell("E"+pos).value([[BarrilesAnnio+' Barriles','Total año '+anio]]).style({border:true,"borderColor": "000000","bold": true});
              workbook.sheet(hoja).cell("G"+pos).value(capaAnnio).style({border:true,"borderColor": "000000","numberFormat": "#,##0.00","bold": true});
              pos+=3;
              nuevoUsoVaciados(workbook.sheet(hoja),pos);
              pos++;
              uso=parsed[i]['Uso'];
              anio=parsed[i]['Año']
              capa=Barriles=capaAnnio=BarrilesAnnio=0;
            }
            if(uso!==parsed[i]['Uso']){
              workbook.sheet(hoja).cell("D"+pos).value([['','',Barriles+' Barriles','Total tipo barril']]).style({border:true,"borderColor": "F5F5F6","bold": true});
              workbook.sheet(hoja).cell("H"+pos).value(capa).style({border:true,"borderColor": "F5F5F6","numberFormat": "#,##0.00","bold": true});
              pos+=2;
              nuevoUsoVaciados(workbook.sheet(hoja),pos);
              uso=parsed[i]['Uso'];
              pos++;
              capa=Barriles=0;
            }
            totalCapa+=parseFloat(parsed[i]['Capacidad']);
            capa+=parseFloat(parsed[i]['Capacidad']);
            capaAnnio+=parseFloat(parsed[i]['Capacidad']);
            nuevoRowVaciados(workbook.sheet(hoja),pos,parsed[i])
            Barriles++;
            BarrilesAnnio++;
            pos++;
          }
          workbook.sheet(hoja).cell("D"+pos).value([['','',Barriles+' Barriles','Total tipo barril']]).style({border:true,"borderColor": "F5F5F6","bold": true});
          workbook.sheet(hoja).cell("H"+pos).value(capa).style({border:true,"borderColor": "F5F5F6","numberFormat": "#,##0.00","bold": true});
          pos+=2;
          workbook.sheet(hoja).cell("E"+pos).value([[BarrilesAnnio+' Barriles','Total año '+anio]]).style({border:true,"borderColor": "000000","bold": true});
          workbook.sheet(hoja).cell("G"+pos).value(capaAnnio).style({border:true,"borderColor": "000000","numberFormat": "#,##0.00","bold": true});
          pos+=2;
          workbook.sheet(hoja).cell("E"+pos).value([[parsed.length+' Barriles','Total General']]).style({border:true,"borderColor": "000000","bold": true});
          workbook.sheet(hoja).cell("G"+pos).value(totalCapa).style({border:true,"borderColor": "000000","bold": true,"numberFormat": "#,##0.00"});
          workbook.sheet(hoja).cell("E"+(pos+1)).value([['','Total flujometros:']]).style({border:true,"borderColor": "000000","bold": true});
          workbook.sheet(hoja).cell("G"+(pos+1)).value(cantidad).style({border:true,"borderColor": "000000","bold": true,"numberFormat": "#,##0.00"});
          workbook.sheet(hoja).cell("E"+(pos+2)).value([['','Total merma:']]).style({border:true,"borderColor": "000000","bold": true});
          workbook.sheet(hoja).cell("G"+(pos+2)).value(totalCapa-cantidad).style({border:true,"borderColor": "000000","bold": true,"numberFormat": "#,##0.00"});
          borrarFilas((pos+3),156,workbook.sheet(hoja));
          url=servidor+'RestApi/GET/get_ReportesTrasiego.php?tanque='+tanque;
          result = await conexion(url);
          parsed =JSON.parse(result);
          workbook.sheet(hoja).cell("F8").value(parsed[0].Descripcion);
        }

        await exportar(archivo,id,tipo,workbook,res);
      } catch (e) {
        console.log(e);
        res.send('..Error.. Hubo un problema al procesar la solicitud, intenta de nuevo más tarde');
      }

    });
}
function nuevoRowVaciados(hoja,inicio,json){
  hoja.cell('D'+inicio).value([[json.NoTapa,json.Alcohol,json.Fecha_Ll,json.Uso]]).style({border:true,"borderColor": "F5F5F6","horizontalAlignment":"center"});
  hoja.cell('H'+inicio).value(json.Capacidad).style({border:true,"borderColor": "F5F5F6","numberFormat": "#,##0.00"});
}
function nuevoUsoVaciados(hoja,inicio){
  hoja.cell('D'+inicio).value([['NoTapa','Alcohol','Fecha Alcohol','Uso','Litros']]).style({"fill": "F5F5F6","horizontalAlignment":"center"});
}


function llenarRTrasiegoRemision(archivo,fecha,tanque,res,tipo,id){
  XlsxPopulate.fromFileAsync('archivos/'+archivo+'.xlsx')
    .then(async workbook => {
      try {
        const hoja="Principal";
        workbook.sheet(hoja).cell("B12").value(PickerToNormal(fecha));
        var url=servidor+'RestApi/GET/get_ReportesTrasiego.php?FSI61194=true&fecha='+fecha+'&tanque='+tanque;
        var result = await conexion(url);
        var parsed =JSON.parse(result);
        if(parsed.length>0 && parsed[0].idTanque!==undefined){
          workbook.sheet(hoja).cell("H8").value(parsed[0].EnvioNo);
          workbook.sheet(hoja).cell("C130").value('Tq: '+parsed[0].tq);
          workbook.sheet(hoja).cell("C131").value('FCV: '+parsed[0].fcv);
          for (var i = 0; i < parsed.length; i++) {
            workbook.sheet(hoja).cell("E"+(i+18)).value([[parsed[i].Tanque,0,parsed[i].Codigo,parsed[i].Año]]);
            workbook.sheet(hoja).cell("F"+(i+18)).value(parseFloat((parsed[i].Litros!=='' ||parsed[i].Litros!==null)?parsed[i].Litros:0)).style({"numberFormat": "#,##0.00"});
          }
          borrarFilas((parsed.length+18),119,workbook.sheet(hoja));
          await exportar(archivo,id,tipo,workbook,res);
        }else{
          res.send('..Error.. No se encontró ningun envío con este tanque y fecha');
        }

      } catch (e) {
        console.log(e);
        res.send('..Error.. Hubo un problema al procesar la solicitud, intenta de nuevo más tarde');
      }

    });
}

async function llenarRTrasiegoHojaAnalisis(archivo,fecha,tanque,res,tipo,id){
  XlsxPopulate.fromFileAsync('archivos/'+archivo+'.xlsx')
    .then(async workbook => {
      try {
        const hoja="Principal";
        workbook.sheet(hoja).cell("M9").value(PickerToNormal(fecha));
        var url=servidor+'RestApi/GET/get_ReportesTrasiego.php?FSI82493=true&fecha='+fecha+'&tanque='+tanque;
        var result = await conexion(url);
        var parsed =JSON.parse(result);
        if(parsed.length>0){
          workbook.sheet(hoja).cell("G201").value(parsed[0].Tanque);
          var pos=10,annio=parsed[0].Anio;
          workbook.sheet(hoja).cell("E"+pos).value(annio).style({"bold": true});
          pos++;
          for (var i = 0; i < parsed.length; i++) {
            if(annio!==parsed[i].Anio){
              annio=parsed[i].Anio;
              workbook.sheet(hoja).cell("E"+pos).value(annio).style({"bold": true});
              pos++;
            }
            delete parsed[i].Fecha;
            delete parsed[i].Renglon;
            delete parsed[i].Tanque;
            delete parsed[i].Anio;
            workbook.sheet(hoja).cell("E"+pos).value([Object.values(parsed[i])]).style({border:true,"borderColor": "d2d2d2","numberFormat": "#,##0"});
            pos++;
          }
          borrarFilas(pos,197,workbook.sheet(hoja));
          url=servidor+'RestApi/GET/get_ReportesTrasiego.php?tanque='+tanque;
          result = await conexion(url);
          parsed =JSON.parse(result);
          workbook.sheet(hoja).cell("L7").value(parsed[0].Descripcion);
        }
        await exportar(archivo,id,tipo,workbook,res);
      } catch (e) {
        console.log(e);
        res.send('..Error.. Hubo un problema al procesar la solicitud, intenta de nuevo más tarde');
      }

    });
}

async function llenarRRellenoOperacion(archivo,fecha,operacion,res,tipo,id){
  XlsxPopulate.fromFileAsync('archivos/'+archivo+'.xlsx')
    .then(async workbook => {
      try {
        const hoja="Principal";
        workbook.sheet(hoja).cell("G6").value(PickerToNormal(fecha));
        workbook.sheet(hoja).cell("D5").value((operacion==='3'?'Reporte Diario de Operación de Relleno':'Reporte Diario de Operación de Trasiego'));
        workbook.sheet(hoja).cell("I5").value((operacion==='3'?'FSI 82.4.8.4':'FSI 82.4.9.8'));
        var url=servidor+'RestApi/GET/get_ReportesTrasiego.php?RepOPDetalle=true&fecha='+fecha+'&ope='+operacion;
        var result = await conexion(url);
        var parsed =JSON.parse(result);
        if(parsed.length>0){
          var totalOrden=0,totalLitros=0,totalBarriles=0,pos=8,totalPosOrden=8,totalPosEstatus=11,totalMerma=0,donadores=0,totalFinal=0,totalLtsOrden=0;
          var Estatus=parsed[0]['Estatus']
          var IdOrden=parsed[0]['IdOrden']
          nuevaOrdenOperacion(workbook.sheet(hoja),pos,parsed[0])
          pos+=6;
          for (var i = 0; i < parsed.length; i++) {
            if(IdOrden!==parsed[i]['IdOrden']){//Nueva orden
              workbook.sheet(hoja).cell('I'+pos).value(totalLitros).style({"bold": true,"numberFormat": "#,##0.00"});
              workbook.sheet(hoja).cell('H'+pos).value('Total LTS').style({"bold": true});
              workbook.sheet(hoja).cell('I'+totalPosOrden).value(totalOrden).style({"bold": true,"numberFormat": "#,##0","horizontalAlignment":"center"});
              workbook.sheet(hoja).cell('G'+totalPosOrden).value(totalLtsOrden).style({"bold": true,"numberFormat": "#,##0.00","horizontalAlignment":"center"});
              workbook.sheet(hoja).cell('J'+totalPosOrden).value(operacion==='3'?'Merma: '+formatoNumero((donadores/totalFinal)*100)+'%':'').style({"bold": true});
              workbook.sheet(hoja).cell('F'+totalPosEstatus).value(totalBarriles).style({"bold": true,"numberFormat": "#,##0"});
              totalOrden=totalBarriles=totalLitros=totalMerma=donadores=totalFinal=totalLtsOrden=0;
              pos+=3;
              totalPosOrden=pos;
              totalPosEstatus=pos+3;
              nuevaOrdenOperacion(workbook.sheet(hoja),pos,parsed[i]);
              pos+=6;
              Estatus=parsed[i]['Estatus'];
              IdOrden=parsed[i]['IdOrden']
            }
            if(Estatus!==parsed[i]['Estatus']){//Nuevo estatus
              if(Estatus==='Relleno'){
                workbook.sheet(hoja).cell('J'+pos).value(totalMerma).style({"bold": true,"numberFormat": "#,##0.00"});
              }
              workbook.sheet(hoja).cell('I'+pos).value(totalLitros).style({"bold": true,"numberFormat": "#,##0.00"});
              workbook.sheet(hoja).cell('H'+pos).value('Total LTS').style({"bold": true});
              workbook.sheet(hoja).cell('F'+totalPosEstatus).value(totalBarriles).style({"bold": true,"numberFormat": "#,##0"});
              totalBarriles=0;
              pos+=2;
              Estatus=parsed[i]['Estatus'];
              nuevaEstadoOperacion(workbook.sheet(hoja),pos,parsed[i]);
              totalPosEstatus=pos+1;
              pos+=4;
              totalLitros=0;
            }
            nuevoRowOperacion(workbook.sheet(hoja),pos,parsed[i]);
            totalBarriles++;
            pos++;
            totalOrden++;
            var capa=parseFloat(parsed[i]['Capacidad']!==null?parsed[i]['Capacidad']:0);
            totalFinal+=capa;
            totalLitros+=capa;
            totalLtsOrden+=capa;
            donadores+=(parsed[i]['Estatus']==='Donador')?capa:0;
            totalMerma+=parseFloat(parsed[i]['Merma']!==null?parsed[i]['Merma']:0);
          }
          if(Estatus==='Relleno'){
            workbook.sheet(hoja).cell('J'+pos).value(totalMerma).style({"bold": true,"numberFormat": "#,##0.00"});
          }
          workbook.sheet(hoja).cell('I'+pos).value(totalLitros).style({"bold": true,"numberFormat": "#,##0.00"});
          workbook.sheet(hoja).cell('J'+totalPosOrden).value(operacion==='3'?'Merma: '+formatoNumero((donadores/totalFinal)*100)+'%':'').style({"bold": true});
          workbook.sheet(hoja).cell('H'+pos).value('Total LTS').style({"bold": true});
          workbook.sheet(hoja).cell('G'+totalPosOrden).value(totalLtsOrden).style({"bold": true,"numberFormat": "#,##0.00","horizontalAlignment":"center"});
          workbook.sheet(hoja).cell('I'+totalPosOrden).value(totalOrden).style({"bold": true,"numberFormat": "#,##0","horizontalAlignment":"center"});
          workbook.sheet(hoja).cell('F'+totalPosEstatus).value(totalBarriles).style({"bold": true,"numberFormat": "#,##0"});
        }

        await exportar(archivo,id,tipo,workbook,res);
      } catch (e) {
        console.log(e);
        res.send('..Error.. Hubo un problema al procesar la solicitud, intenta de nuevo más tarde');
      }

    });
}

function nuevaOrdenOperacion(hoja,inicio,json){
  hoja.cell('D'+inicio).value([['Orden:',json.IdOrden,'Total lts:','','Barriles Reg:']]).style({"horizontalAlignment":"center"});
  //hoja.cell('G'+inicio).value(totalLtsOrden).style({"numberFormat": "#,##0.00"});
  hoja.range("D"+inicio+":J"+inicio).style({ bottomBorder:true,"bold": true});
  nuevaEstadoOperacion(hoja,inicio+2,json);
}
function nuevaEstadoOperacion(hoja,inicio,json){
  hoja.cell('E'+inicio).value(json.Estatus).style({"fill": "7AB0FF"});
  hoja.cell('E'+(inicio+1)).value([['Total Reg:','','Año llenada:',json.Fecha_Ll,'Alcohol:',json.Alcohol]]);
  hoja.range("G"+(inicio+3)+":H"+(inicio+3)).merged(true);
  hoja.cell('E'+(inicio+3)).value([['Etiqueta','Uso','Ubicación','Ubicación','Litros']]).style({"fill": "F5F5F6","horizontalAlignment":"center"});
  if(json.Estatus==='Relleno'){
    hoja.cell('J'+(inicio+3)).value('Merma').style({"fill": "F5F5F6","horizontalAlignment":"center"});
  }
}
function nuevoRowOperacion(hoja,inicio,json){
  hoja.range("G"+inicio+":H"+inicio).merged(true);
  hoja.cell('E'+inicio).value([[json.Etiqueta,json.Uso,json.Ubicacion]]).style({border:true,"borderColor": "F5F5F6","horizontalAlignment":"center"});
  hoja.cell('I'+inicio).value(json.Capacidad).style({border:true,"borderColor": "F5F5F6","numberFormat": "#,##0.00"});
  if(json.Estatus==='Relleno'){
    hoja.cell('J'+inicio).value(json.Merma).style({border:true,"borderColor": "F5F5F6","numberFormat": "#,##0.00"});
  }
}

function formatoNumero(numero){
  if(numero==null)
    return '';
  if(numero==='')
    return '';
  if(numero===undefined)
    return '';
  var can=parseFloat(numero);
  return ((Math.round(can * 100) / 100).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
}
function llenarRLlenadoLlenada(archivo,fecha,res,tipo,id){
  XlsxPopulate.fromFileAsync('archivos/'+archivo+'.xlsx')
    .then(async workbook => {
      try {
        const hoja="Principal";
        var url=servidor+'RestApi/GET/get_ReportesLlenado.php?OPDetalleLlen=true&fecha='+fecha;
        var result = await conexion(url);
        var parsed =JSON.parse(result);
        if(parsed.length>0){
          var totalCapa=0,totalBarr=0,totalTanq=0;
          var uso=parsed[0]['Uso'];
          var tanque=parsed[0]['Tanque'];
          nuevoTanqueLlenado(workbook.sheet(hoja),8,fecha,parsed[0]);
          nuevoEncaLlenado(workbook.sheet(hoja),11);
          var totalPos=9;
          var pos=12;
          for (var i = 0; i < parsed.length; i++) {
            if(tanque!==parsed[i]['Tanque']){
              totalRowLlenado(workbook.sheet(hoja),pos,uso,totalBarr,totalCapa);
              workbook.sheet(hoja).cell('I'+totalPos).value(totalTanq).style({"fill": "F5F5F6","numberFormat": "0.00"});;
              totalTanq=totalBarr=totalCapa=0;
              pos+=3;
              totalPos=pos+1;
              nuevoTanqueLlenado(workbook.sheet(hoja),pos,fecha,parsed[i]);
              uso=parsed[i]['Uso'];
              tanque=parsed[i]['Tanque']
              pos+=3;
              nuevoEncaLlenado(workbook.sheet(hoja),pos);
              pos++;
            }
            if(uso!==parsed[i]['Uso']){
              totalRowLlenado(workbook.sheet(hoja),pos,uso,totalBarr,totalCapa);
              totalBarr=0;
              totalCapa=0;
              pos+=2;
              uso=parsed[i]['Uso'];
              nuevoEncaLlenado(workbook.sheet(hoja),pos);
              pos++;
            }
            nuevoRowLlenado(workbook.sheet(hoja),pos,parsed[i]);
            totalBarr++;
            totalCapa+=parseFloat(parsed[i]['Capacidad']);
            totalTanq+=parseFloat(parsed[i]['Capacidad']);
            pos++;
          }
          workbook.sheet(hoja).cell('I'+totalPos).value(totalTanq).style({"fill": "F5F5F6","numberFormat": "#,##0.00"});;
          totalRowLlenado(workbook.sheet(hoja),pos,uso,totalBarr,totalCapa);
        }
        await exportar(archivo,id,tipo,workbook,res);
      } catch (e) {
        console.log(e);
        res.send('..Error.. Hubo un problema al procesar la solicitud, intenta de nuevo más tarde');
      }

    });
}
function nuevoTanqueLlenado(hoja,inicio,fecha,json){
  hoja.cell('D'+inicio).value([['Fecha','Fecha Recepción','Alcohol','Litros Recepción','Litros Consumo','Litros Llenada','Tanque']]).style({"fill": "F5F5F6","horizontalAlignment":"center"});
  hoja.cell('D'+(inicio+1)).value([[PickerToNormal(fecha),json.FechaLote,json.Alcohol]]).style({"fill": "F5F5F6","horizontalAlignment":"center"});
  hoja.cell('J'+(inicio+1)).value(json.Tanque).style({"fill": "F5F5F6","horizontalAlignment":"center"});
  hoja.cell('G'+(inicio+1)).value([[parseFloat((json.RecepLitros==null)?-1:json.RecepLitros),parseFloat((json.Consumo==null)?-1:json.Consumo)]]).style({"fill": "F5F5F6","numberFormat": "#,##0.00"});
}
function nuevoEncaLlenado(hoja,inicio){
  hoja.range("H"+inicio+":I"+inicio).merged(true);
  hoja.cell('E'+inicio).value([['Etiqueta','NoTapa','Uso','Litros']]).style({"fill": "F5F5F6","horizontalAlignment":"center"});
}
function nuevoRowLlenado(hoja,inicio,json){
  hoja.range("H"+inicio+":I"+inicio).merged(true);
  hoja.cell('E'+inicio).value([[json.Etiqueta,json.NoTapa,json.Uso]]).style({border:true,"borderColor": "F5F5F6","horizontalAlignment":"center"});
  hoja.cell('H'+inicio).value(parseFloat(json.Capacidad)).style({border:true,"borderColor": "F5F5F6","numberFormat": "#,##0.00"});
}
function totalRowLlenado(hoja,inicio,uso,totalBarr,totalCapa){
  hoja.range("H"+inicio+":I"+inicio).merged(true);
  hoja.cell('E'+inicio).value([['',uso+' Total: '+totalBarr,'TOTAL LTS']]).style({border:true,"borderColor": "F5F5F6","horizontalAlignment":"center","bold": true});
  hoja.cell('H'+inicio).value(totalCapa).style({border:true,"borderColor": "F5F5F6","numberFormat":"#,##0.00","bold": true});
}

function llenarRLlenadoMantenimineto(archivo,fecha,fecha2,res,tipo,id){
  XlsxPopulate.fromFileAsync('archivos/'+archivo+'.xlsx')
    .then(async workbook => {
      try {
        const hoja="Principal";
        workbook.sheet(hoja).cell('E7').value(PickerToNormal(fecha));
        if(fecha===fecha2){
          borrarFilas(8,22,workbook.sheet(hoja));
        }else{
          workbook.sheet(hoja).cell('G7').value('Fecha final:');
          workbook.sheet(hoja).cell('I7').value(PickerToNormal(fecha2));
          var url=servidor+'RestApi/GET/get_ReportesLlenado.php?OPDetalleMantGen=true&fecha='+fecha+'&fecha2='+fecha2;
          var result = await conexion(url);
          var parsed =JSON.parse(result);
          var tipoRep=(parsed.length>0)?parsed[0].Reparación:'',total=0,totalGnr=0,pos=10;
          for (var i = 0; i < parsed.length; i++) {
            if(tipoRep!==parsed[i].Reparación){
              pos=ponerLineasTotal(pos,workbook.sheet(hoja),total,tipoRep);
              tipoRep=parsed[i].Reparación;
              total=0;
            }
            total+=parseInt(parsed[i].Total);
            totalGnr+=parseInt(parsed[i].Total);
            workbook.sheet(hoja).cell('F'+pos).value(parsed[i].Reparación);
            workbook.sheet(hoja).cell('H'+pos).value(parsed[i].Uso);
            workbook.sheet(hoja).cell('J'+pos).value(parsed[i].Total).style({"numberFormat": "#,##0"});
            pos++;
            //workbook.sheet(hoja).range("F"+(i+10)+":L"+(i+10)).style({border:true});
          }
          pos=ponerLineasTotal(pos,workbook.sheet(hoja),total,tipoRep);
          pos=ponerLineasTotal(pos,workbook.sheet(hoja),totalGnr,'general');
          borrarFilas(pos,22,workbook.sheet(hoja));
        }
        var url=servidor+'RestApi/GET/get_ReportesLlenado.php?OPDetalleMant=true&fecha='+fecha+'&fecha2='+fecha2;
        var result = await conexion(url);
        var parsed =JSON.parse(result);
        if(parsed.length>0){
          await tablaXDia(parsed,workbook.sheet(hoja));
        }
        await exportar(archivo,id,tipo,workbook,res);
      } catch (e) {
        console.log(e);
        res.send('..Error.. Hubo un problema al procesar la solicitud, intenta de nuevo más tarde');
      }

    });
}
function ponerLineasTotal(pos,excel,total,tipo){
  excel.range("F"+pos+":I"+pos).merged(true).style({"horizontalAlignment":"right","fill": "d2d2d2"});
  excel.range("J"+pos+":L"+pos).merged(true).style({"fill": "d2d2d2"});
  excel.cell('F'+pos).value('Total '+tipo+':');
  excel.cell('J'+pos).value(total).style({"numberFormat": "#,##0"});
  return pos+1;
}
async function tablaXDia(parsed,excel){
   var fecha=parsed[0].fecha,tipo=parsed[0].Reparación,total=0,totalGnr=0,pos=24;
   pos=inicioTabla(fecha,pos,excel);
   for(var x=0;x<parsed.length;x++){
     if(fecha!==parsed[x].fecha){
       pos=ponerLineasTotal(pos,excel,total,tipo);
       pos=ponerLineasTotal(pos,excel,totalGnr,PickerToNormal(fecha));
       pos+=2;
       tipo=parsed[x].Reparación;
       total=totalGnr=0;
       pos=await interTabla(fecha,pos,excel);
       fecha=parsed[x].fecha;
       pos=inicioTabla(fecha,pos,excel);
     }
     if(tipo!==parsed[x].Reparación){
       pos=ponerLineasTotal(pos,excel,total,tipo);
       tipo=parsed[x].Reparación;
       total=0;
     }
     total+=parseInt(parsed[x].Total);
     totalGnr+=parseInt(parsed[x].Total);
     excel.range("F"+pos+":G"+pos).merged(true);
     excel.range("H"+pos+":I"+pos).merged(true);
     excel.range("J"+pos+":L"+pos).merged(true);
     excel.cell('F'+pos).value([[parsed[x].Reparación,'',parsed[x].Uso]]).style({"horizontalAlignment":"center"});
     excel.cell('J'+pos).value(parsed[x].Total).style({"numberFormat": "#,##0"});
     pos++;
   }
   pos=ponerLineasTotal(pos,excel,total,tipo);
   pos=ponerLineasTotal(pos,excel,totalGnr,PickerToNormal(fecha));
   pos+=2;
   await interTabla(fecha,pos,excel);
 }
 async function interTabla(fecha,pos,excel){
   excel.range("D"+pos+":E"+pos).merged(true);
   excel.range("G"+pos+":H"+pos).merged(true);
   excel.range("I"+pos+":N"+pos).merged(true);
   excel.cell('D'+pos).value([['Etiqueta','','Uso','Operador','','Reparación','','','','','']]).style({"horizontalAlignment":"center",border:true,"fill": "F5F5F6"});
   var url=servidor+'RestApi/GET/get_ReportesLlenado.php?OPDetalleMantDet=true&fecha='+fecha;
   var result = await conexion(url);
   var parsed =JSON.parse(result);
   pos++;
   pos=tablaDetalles(parsed,pos,excel);
   return pos;
 }
 function inicioTabla(fecha,pos,excel){
   excel.range("F"+pos+":L"+pos).merged(true).style({border:true,"horizontalAlignment":"center","fill": "F5F5F6"});
   excel.cell('F'+pos).value('Fecha: '+PickerToNormal(fecha));
   pos+=2;
   excel.range("F"+pos+":G"+pos).merged(true).style({border:true,"horizontalAlignment":"center","fill": "F5F5F6"});
   excel.range("H"+pos+":I"+pos).merged(true).style({border:true,"horizontalAlignment":"center","fill": "F5F5F6"});
   excel.range("J"+pos+":L"+pos).merged(true).style({border:true,"horizontalAlignment":"center","fill": "F5F5F6"});
   excel.cell('F'+pos).value([['Reparación','','Uso','','Total']]);
   return (pos+1);
 }

 function tablaDetalles(parsed,pos,excel){
   var etiqueta="";
   for(var x=0;x<parsed.length;x++){
     if(etiqueta!==parsed[x].Etiqueta){
       etiqueta=parsed[x].Etiqueta;
       excel.range("D"+pos+":E"+pos).merged(true);
       excel.range("G"+pos+":H"+pos).merged(true);
       excel.range("I"+pos+":N"+pos).merged(true);
       excel.cell('D'+pos).value([[parsed[x].Etiqueta,'',parsed[x].Uso,parsed[x].Operario,'',parsed[x].TipoMant]]).style({"horizontalAlignment":"center"});
       if(parsed[x].IdTipoMant==2){
         pos++;
         excel.range("D"+pos+":H"+pos).merged(true);
         excel.cell('I'+pos).value([['Aros','Tapas','Duelas','Cep Duela','Rep Canal','Canal Nvo']]).style({"horizontalAlignment":"center",border:true,fontSize:10});
         pos++;
         excel.range("D"+pos+":H"+pos).merged(true);
         excel.cell('I'+pos).value([[parsed[x].CAro,parsed[x].CTapas,parsed[x].CDuela,parsed[x].CepDuela,parsed[x].RepCanal,parsed[x].CanalNvo]]).style({"horizontalAlignment":"center"});
       }
       pos++;
     }else if(parsed[x].IdTipoMant==2){
       excel.range("D"+pos+":H"+pos).merged(true);
       excel.cell('I'+pos).value([[parsed[x].CAro,parsed[x].CTapas,parsed[x].CDuela,parsed[x].CepDuela,parsed[x].RepCanal,parsed[x].CanalNvo]]).style({"horizontalAlignment":"center"});
       pos++;
     }

   }
   return (pos+3);
 }

async function exportar(archivo,id,tipo,workbook,res){
  await workbook.toFileAsync('archivos/'+archivo+'_'+id+'_temporal.xlsx');//Escribe el archivo en un temporal
  if(tipo==='xlsx' || tipo==='excel'){//Descargar en excel
    res.send('archivos/'+archivo+'_'+id+'_temporal.xlsx')//Avisamos al usuario que el archivo esta listo con la ubicacion donde se encuentra
    setTimeout(function() {
      fs.unlinkSync(__dirname+'/archivos/'+archivo+'_'+id+'_temporal.xlsx');
    }, 5000);

  }else{
    convertirArchivo(archivo,res,id,tipo);//El usuario lo quiere en otro formato
  }
}


function llenarRLlenadoRevisado(archivo,fecha,res,tipo,id){
  XlsxPopulate.fromFileAsync('archivos/'+archivo+'.xlsx')
    .then(async workbook => {
      try {
        const hoja="Principal";
        var url=servidor+'RestApi/GET/get_ReportesLlenado.php?RepOpRevisadoTotal=true&fecha='+fecha;
        var result = await conexion(url);
        var parsed =JSON.parse(result);
        if(parsed.length>0){
          for (var i = 0; i < parsed.length; i++) {
            workbook.sheet(hoja).cell('F'+(i+11)).value([[parsed[i].Uso,parsed[i].Barriles,parsed[i].Capacidad]]).style({border:true,"borderColor": "F5F5F6","horizontalAlignment":"center"});
          }
          borrarFilas((parsed.length+11),14,workbook.sheet(hoja));
          url=servidor+'RestApi/GET/get_ReportesLlenado.php?RepOpRevisado=true&fecha='+fecha;
          result = await conexion(url);
          parsed =JSON.parse(result);
          var totalCapa=0;
          var totalBarr=0;
          var uso=parsed[0]['Uso']
          workbook.sheet(hoja).cell('E8').value(parsed[0].Alcohol);
          workbook.sheet(hoja).cell('G8').value(parsed[0].FechaRevisado);
          workbook.sheet(hoja).cell('I8').value(parsed[0].FechaLote);
          var pos=16;
          nuevoEncaRevisado(uso,workbook.sheet(hoja),pos);
          pos+=3;
          for (var i = 0; i < parsed.length; i++) {
            if(uso!==parsed[i]['Uso']){
              totalRowRevisado(uso,workbook.sheet(hoja),pos,totalBarr,totalCapa);
              totalBarr=0;
              totalCapa=0;
              uso=parsed[i]['Uso'];
              pos+=2;
              nuevoEncaRevisado(uso,workbook.sheet(hoja),pos);
              pos+=3;
            }
            RowRevisado(workbook.sheet(hoja),pos,parsed[i]);
            pos++;
            totalBarr++;
            totalCapa+=parseFloat(parsed[i]['Capacidad']);
          }
          totalRowRevisado(uso,workbook.sheet(hoja),pos,totalBarr,totalCapa);
        }
        await exportar(archivo,id,tipo,workbook,res);
      } catch (e) {
        console.log(e);
        res.send('..Error.. Hubo un problema al procesar la solicitud, intenta de nuevo más tarde');
      }

    });
}
function nuevoEncaRevisado(uso,hoja,inicio){
  hoja.cell('D'+inicio).value('Uso: '+uso).style({"bold": true});
  hoja.cell('D'+(inicio+2)).value([['Etiqueta','NoTapa','Uso','Litros','Ubicación']]).style({"fill": "F5F5F6","horizontalAlignment":"center"});
}
function totalRowRevisado(uso,hoja,inicio,totalBarr,totalCapa){
  hoja.cell('D'+inicio).value([[uso+' Total: '+totalBarr,'','TOTAL LTS']]).style({border:true,"borderColor": "F5F5F6","bold": true});
  hoja.cell('G'+inicio).value(totalCapa).style({border:true,"borderColor": "F5F5F6","numberFormat": "#,##0.00","bold": true});
}
function RowRevisado(hoja,inicio,json){
  hoja.cell('D'+inicio).value([[json.Etiqueta,json.NoTapa,json.Uso,'',json.Ubicacion]]).style({border:true,"borderColor": "F5F5F6"});
  hoja.cell('G'+inicio).value(json.Capacidad).style({border:true,"borderColor": "F5F5F6","numberFormat": "#,##0.00"});
}

function borrarFilas(inicio,cantidad,hoja){
  for (var i = inicio; i <= cantidad; i++) {
    hoja.row(i).hidden(true);
  }
}


function llenarRGerencia(archivo,res,tipo,id){
  XlsxPopulate.fromFileAsync('archivos/'+archivo+'.xlsx')
    .then(async workbook => {
      try {
        const hoja="Principal";
        var url=servidor+'RestApi/GET/get_Reportes.php?gerencia=true';
        var result = await conexion(url);
        var parsed =JSON.parse(result);
        if(parsed.length>0){
          var pos=8;
          var totales=[0,0,0,0,0,0,0,0];
          var annio=parsed[0]['Año'];
          nuevoEncaGerencia(annio,workbook.sheet(hoja),pos);
          pos+=3;
          for (var i = 0; i < parsed.length; i++) {
            if(annio!==parsed[i]['Año']){
              totalRowGerencia(workbook.sheet(hoja),pos,totales);
              totales=[0,0,0,0,0,0,0,0];
              annio=parsed[i]['Año']
              pos+=5;
              nuevoEncaGerencia(annio,workbook.sheet(hoja),pos);
              pos+=3;
            }
            RowGerencia(workbook.sheet(hoja),pos,parsed[i]);
            pos++;
            totales=sumarCamposGerencia(totales,parsed[i]);
          }
          totalRowGerencia(workbook.sheet(hoja),pos,totales);
        }
        await exportar(archivo,id,tipo,workbook,res);
      } catch (e) {
        console.log(e);
        res.send('..Error.. Hubo un problema al procesar la solicitud, intenta de nuevo más tarde');
      }

    });
}
function nuevoEncaGerencia(anio,hoja,inicio){
  hoja.cell('D'+inicio).value('Año llenada:').style({"bold": true,'underline':true});
  hoja.cell('E'+inicio).value(anio).style({"bold": true,'underline':true});
  hoja.cell('E'+(inicio+2)).value([['Uso','C1P176','C2P176','E25P176','E50P176','E100P176','E150P176','Birrectificado','Birr Honduras']]).style({"fill": "F5F5F6"});
}
function RowGerencia(hoja,inicio,json){
  hoja.cell('E'+inicio).value(json.Codigo).style({border:true,"borderColor": "F5F5F6"});
  hoja.cell('F'+inicio).value([[json.C1P176,json.C2P176,json.E25P176,json.E50P176,json.E100P176,json.E150P176,json.Birrectificado,json['Birrectificado Honduras']]]).style({border:true,"borderColor": "F5F5F6","numberFormat": "#,##0"});
}
function totalRowGerencia(hoja,inicio,totales){
  hoja.cell('E'+inicio).value('Total Clase:').style({border:true,"borderColor": "F5F5F6"});
  hoja.cell('F'+inicio).value([totales]).style({border:true,"borderColor": "F5F5F6","numberFormat": "#,##0"});
  hoja.cell('D'+(inicio+2)).value('Total Barriles').style({border:true,"borderColor": "000000"});
  var total=0;
  for (var i = 0; i < totales.length; i++) {
    total+=totales[i];
  }
  hoja.cell('E'+(inicio+2)).value(total).style({border:true,"borderColor": "000000","numberFormat": "#,##0"});
}

function sumarCamposGerencia(totalCapa,json){
  totalCapa[0]+=json.C1P176;
  totalCapa[1]+=json.C2P176;
  totalCapa[2]+=json.E25P176;
  totalCapa[3]+=json.E50P176;
  totalCapa[4]+=json.E100P176;
  totalCapa[5]+=json.E150P176;
  totalCapa[6]+=json.Birrectificado;
  totalCapa[7]+=json['Birrectificado Honduras'];
  return totalCapa;
}
//Inicia reporte en inventario.html
function llenarInventario(archivo,bodega,alcohol,llenada,uso,res,tipo,id){
  XlsxPopulate.fromFileAsync('archivos/'+archivo+'.xlsx')
    .then(async workbook => {
      try {
        const hoja="Principal";
        var url=servidor+'RestApi/GET/get_Reportes.php?inventarioDeta=true&bodega='+bodega+'&alcohol='+alcohol+'&llenada='+llenada+'&uso='+uso;
        var result = await conexion(url);
        var parsed =JSON.parse(result);
        await ponerEncabezadoInventario(bodega,alcohol,llenada,uso,workbook.sheet(hoja));
        insertarTablaUnoInventario(parsed,workbook.sheet(hoja),13);
        var inicio=parsed.length;
        url=servidor+'RestApi/GET/get_Reportes.php?inventario=true&bodega='+bodega+'&alcohol='+alcohol+'&llenada='+llenada+'&uso='+uso;
        result = await conexion(url);
        parsed =JSON.parse(result);
        insertarTablaDosInventario(parsed,workbook.sheet(hoja),inicio+17);
        await exportar(archivo,id,tipo,workbook,res);
      } catch (e) {
        console.log(e);
        res.send('..Error.. Hubo un problema al procesar la solicitud, intenta de nuevo más tarde');
      }

    });
}

function insertarTablaUnoInventario(json,hoja,inicio){
  convinarCeldas(hoja,["G"+(inicio-1)+":H"+(inicio-1),"I"+(inicio-1)+":J"+(inicio-1),"K"+(inicio-1)+":L"+(inicio-1),"M"+(inicio-1)+":N"+(inicio-1),"O"+(inicio-1)+":P"+(inicio-1)])
  hoja.cell('G'+(inicio-1)).value([['Año Alcohol','','Alcohol','','Barril','','Barriles','','Litros','']]).style({"fill": "F5F5F6","horizontalAlignment":"center"});
  for (var i = 0; i < json.length; i++) {
    convinarCeldas(hoja,["G"+(i+inicio)+":H"+(i+inicio),"I"+(i+inicio)+":J"+(i+inicio),"K"+(i+inicio)+":L"+(i+inicio),"M"+(i+inicio)+":N"+(i+inicio),"O"+(i+inicio)+":P"+(i+inicio)])
    hoja.cell('G'+(i+inicio)).value([[json[i].Fecha_Li,'',json[i].Alcohol,'',json[i].Barril,'']]).style({border:true,"borderColor": "F5F5F6","horizontalAlignment":"center"});
    hoja.cell('M'+(i+inicio)).value([[parseInt(json[i]['Total Barriles']),0]]).style({border:true,"borderColor": "F5F5F6","numberFormat": "#,##0"});
    hoja.cell('O'+(i+inicio)).value([[parseInt(json[i]['Total Litros']),0]]).style({border:true,"borderColor": "F5F5F6","numberFormat": "#,##0.00"});
  }
}
function insertarTablaDosInventario(json,hoja,inicio){
  var totales=[0,0,0,0,0,0,0,0,0];
  hoja.cell('D'+(inicio-1)).value([['Número','Bodega','Fila','Año Alcohol','Alcohol','Uso','A','B','C','D','RC','E','F','Barriles','Litros']]).style({border:true,"borderColor": "000000","horizontalAlignment":"center"});
  for (var i = 0; i < json.length; i++) {
    hoja.cell('D'+(i+inicio)).value([[json[i].Num,json[i].Bod,json[i].Fila,json[i].Fecha_Li,json[i].Alcohol,json[i].Uso]]).style({"horizontalAlignment":"center"});
    hoja.cell('J'+(i+inicio)).value([[json[i].A,json[i].B,json[i].C,json[i].D,json[i].RC,json[i].E,json[i].F,json[i].TotalBarriles]]).style({"numberFormat": "#,##0"});
    hoja.cell('R'+(i+inicio)).value(json[i].totallitros).style({"numberFormat": "#,##0.00"});
    totales[0]+=parseInt(json[i].A);
    totales[1]+=parseInt(json[i].B);
    totales[2]+=parseInt(json[i].C);
    totales[3]+=parseInt(json[i].D);
    totales[4]+=parseInt(json[i].RC);
    totales[5]+=parseInt(json[i].E);
    totales[6]+=parseInt(json[i].F);
    totales[7]+=parseInt(json[i].TotalBarriles);
    totales[8]+=parseFloat(json[i].totallitros);
  }
  hoja.cell('I'+(json.length+inicio)).value('Totales:').style({border:true,"borderColor": "000000","horizontalAlignment":"center"});
  hoja.cell('J'+(json.length+inicio)).value([totales]).style({border:true,"borderColor": "000000","numberFormat": "#,##0"});
  hoja.cell('R'+(json.length+inicio)).value(totales[8]).style({border:true,"borderColor": "000000","numberFormat": "#,##0.00"});

}
async function ponerEncabezadoInventario(bodega,alcohol,llenada,uso,hoja){
  convinarCeldas(hoja,["G6:I6","K6:M6","Q6:S6"]);
  hoja.range("G6:I6").style({bottomBorder:true,"borderColor": "000000", "horizontalAlignment":"center"});
  hoja.range("K6:M6").style({bottomBorder:true,"borderColor": "000000", "horizontalAlignment":"center"});
  hoja.range("Q6:S6").style({bottomBorder:true,"borderColor": "000000", "horizontalAlignment":"center"});
  hoja.cell('D5').value('Inventario').style({ bold: true, "horizontalAlignment":"left" });
  hoja.cell('H5').value('Bodega:').style({ bold: true, "horizontalAlignment":"center" });
  hoja.cell('L5').value('Alcohol:').style({ bold: true, "horizontalAlignment":"center" });
  hoja.cell('O5').value('Año:').style({ bold: true, "horizontalAlignment":"center" });
  hoja.cell('R5').value('Uso:').style({ bold: true, "horizontalAlignment":"center" });
  if(bodega===''){
    hoja.cell('G6').value('Todos');
  }else{
    var url=servidor+'RestApi/GET/get_Reportes.php?almacenesTod='+bodega;
    var result = await conexion(url);
    var parsed =JSON.parse(result);
    hoja.cell('G6').value(parsed[0].Nombres);
  }
  if(alcohol===''){
    hoja.cell('K6').value('Todos').style({ "horizontalAlignment":"center" });
  }else{
    var url=servidor+'RestApi/GET/get_Reportes.php?alcoholTod='+alcohol;
    var result = await conexion(url);
    var parsed =JSON.parse(result);
    hoja.cell('K6').value(parsed[0].Nombres);
  }
  if(llenada===''){
    hoja.cell('O6').value('Todos').style({bottomBorder:true,"borderColor": "000000", "horizontalAlignment":"center"});
  }else{
    hoja.cell('O6').value(llenada).style({bottomBorder:true,"borderColor": "000000", "horizontalAlignment":"center"});
  }
  if(uso===''){
    hoja.cell('Q6').value('Todos');
  }else{
    var url=servidor+'RestApi/GET/get_Reportes.php?usoTod='+uso;
    var result = await conexion(url);
    var parsed =JSON.parse(result);
    hoja.cell('Q6').value(parsed[0].Nombres);
  }

}
//Termina reporte en inventario.html
//Inicia reporte en barriles_plantel.html
function llenarBarrilesPlantel(archivo,res,tipo,id){
  XlsxPopulate.fromFileAsync('archivos/'+archivo+'.xlsx')
    .then(async workbook => {
      try {
        const hoja="Principal";
        workbook.sheet(hoja).cell('D5').value('Inventario de barriles vacios en plantel').style({ bold: true, "horizontalAlignment":"left" });
        var url=servidor+'RestApi/GET/get_Reportes.php?barriles_plantel=true';
        var result = await conexion(url);
        var parsed =JSON.parse(result);
        insertarTablaUnoBarrilesPlantel(parsed,workbook.sheet(hoja),13);
        await exportar(archivo,id,tipo,workbook,res);
      } catch (e) {
        console.log(e);
        res.send('..Error.. Hubo un problema al procesar la solicitud, intenta de nuevo más tarde');
      }

    });
}
function insertarTablaUnoBarrilesPlantel(json,hoja,inicio){
  var totales=[0,0,0,0,0,0,0,0];
  hoja.range("G"+(inicio-1)+":I"+(inicio-1)).merged(true);
  hoja.cell('F'+(inicio-1)).value([['Número','Plantel','','','Uso','A','B','C','D','F','RC','RF','Totales']]).style({"fill": "F5F5F6","horizontalAlignment":"center"});
  for (var i = 0; i < json.length; i++) {
    hoja.range("G"+(i+inicio)+":I"+(i+inicio)).merged(true);
    hoja.cell('F'+(i+inicio)).value([[json[i].Num,json[i].Plantel,'','',json[i].Uso]]).style({"horizontalAlignment":"center",border:true,"borderColor": "F5F5F6"});
    hoja.cell('K'+(i+inicio)).value([[json[i].A,json[i].B,json[i].C,json[i].D,json[i].F,json[i].RC,json[i].RF,json[i].Total]]).style({"numberFormat": "#,##0",border:true,"borderColor": "F5F5F6"});
    totales[0]+=parseInt(json[i].A);
    totales[1]+=parseInt(json[i].B);
    totales[2]+=parseInt(json[i].C);
    totales[3]+=parseInt(json[i].D);
    totales[4]+=parseInt(json[i].F);
    totales[5]+=parseInt(json[i].RC);
    totales[6]+=parseInt(json[i].RF);
    totales[7]+=parseInt(json[i].Total);
  }
  hoja.cell('I'+(json.length+inicio)).value([['Totales:','']]).style({bold: true,border:true,"borderColor": "F5F5F6","horizontalAlignment":"center"});
  hoja.cell('K'+(json.length+inicio)).value([totales]).style({bold: true,border:true,"borderColor": "F5F5F6","numberFormat": "#,##0"});
}

//Termina reporte en barriles_plantel.html
//Inicia reporte en tanques_plantel.html
function llenarTanquesPlantel(archivo,res,tipo,id){
  XlsxPopulate.fromFileAsync('archivos/'+archivo+'.xlsx')
    .then(async workbook => {
      try {
        const hoja=workbook.sheet("Principal");
        var url=servidor+'RestApi/GET/get_Reportes.php?tanques_plantel=true';
        var result = await conexion(url);
        var parsed =JSON.parse(result);
        var totales=0;
        for (var i = 0; i < parsed.length; i++) {
          hoja.cell('F'+(i+8)).value([[parsed[i].Num,parsed[i].Plantel]]);
          hoja.cell('H'+(i+8)).value(parsed[i].Tanques).style({"numberFormat": "#,##0"});
          totales+=parseInt(parsed[i].Tanques);
        }
        hoja.cell('G'+(parsed.length+8)).value('Totales:').style({bold: true,border:true,"borderColor": "F5F5F6","horizontalAlignment":"right"});
        hoja.cell('H'+(parsed.length+8)).value(totales).style({bold: true,border:true,"borderColor": "F5F5F6","numberFormat": "#,##0"});
        await exportar(archivo,id,tipo,workbook,res);
      } catch (e) {
        console.log(e);
        res.send('..Error.. Hubo un problema al procesar la solicitud, intenta de nuevo más tarde');
      }

    });
}

//Termina reporte en tanques_plantel.html
var intVal = function ( i ) {
    return typeof i === 'string' ?
        i.replace(/[\$,]/g, '')*1 :
        typeof i === 'number' ?
            i : 0;
};
//Empieza reporte en llehandos.html
function llenarBarrilesLlenados(archivo,res,fecha,fecha2,tipo,id){
  XlsxPopulate.fromFileAsync('archivos/'+archivo+'.xlsx')
    .then(async workbook => {
      try {
        const hoja="Principal";
        workbook.sheet(hoja).cell("H6").value(PickerToNormal(fecha));
        workbook.sheet(hoja).cell("L6").value(PickerToNormal(fecha2));
        var url=servidor+'RestApi/GET/get_Reportes.php?llenados=true&fecha1='+fecha+'&fecha2='+fecha2;
        var result = await conexion(url);
        var parsed =JSON.parse(result);
        var totalB=0,totalLts=0;
        workbook.sheet(hoja).cell("E8").value([['Fecha','Fecha lote','Alcohol','Tanque','Uso','Barriles','Litros']]).style({"fill": "F5F5F6","horizontalAlignment":"center"});
        for (var i = 0; i < parsed.length; i++) {
          workbook.sheet(hoja).cell("E"+(i+9)).value([[parsed[i].Fecha,parsed[i].FechaLote,parsed[i].Alcohol,parsed[i].Tanque,parsed[i].Uso]]).style({border:true,"borderColor": "F5F5F6","horizontalAlignment":"center"});
          workbook.sheet(hoja).cell("J"+(i+9)).value(intVal(parsed[i].T_Barril)).style({border:true,"borderColor": "F5F5F6","numberFormat": "#,##0"});
          workbook.sheet(hoja).cell("K"+(i+9)).value(intVal(parsed[i].T_Lts)).style({border:true,"borderColor": "F5F5F6","numberFormat": "#,##0.00","horizontalAlignment":"right"});
          totalB+=intVal(parsed[i].T_Barril);
          totalLts+=intVal(parsed[i].T_Lts);
        }
        workbook.sheet(hoja).cell("I"+(parsed.length+9)).value('Totales:').style({border:true,"borderColor": "F5F5F6","horizontalAlignment":"center"});
        workbook.sheet(hoja).cell("J"+(parsed.length+9)).value(totalB).style({border:true,"borderColor": "F5F5F6","numberFormat": "#,##0","horizontalAlignment":"right"});
        workbook.sheet(hoja).cell("K"+(parsed.length+9)).value(totalLts).style({border:true,"borderColor": "F5F5F6","numberFormat": "#,##0.00","horizontalAlignment":"right"});
        var inicio=parsed.length+13;
        url=servidor+'RestApi/GET/get_Reportes.php?llenadosT2=true&fecha1='+fecha+'&fecha2='+fecha2;
        result = await conexion(url);
        parsed =JSON.parse(result);
        var total=0;
        workbook.sheet(hoja).cell("E"+(inicio-1)).value([['Etiqueta','Fecha','Tapa','Uso','Litros','Alcohol','Año Alcohol','Tanque']]).style({border:true,"borderColor": "000000","horizontalAlignment":"center"});
        for (var i = 0; i < parsed.length; i++) {
          workbook.sheet(hoja).cell("E"+(i+inicio)).value([[parsed[i].Etiqueta,parsed[i].Fecha,parsed[i].NoTapa,parsed[i].Uso,1,parsed[i].Alcohol,parsed[i]['Año Alcohol'],parsed[i].Tanque]]).style({"horizontalAlignment":"center"});
          workbook.sheet(hoja).cell("I"+(i+inicio)).value(parseFloat(parsed[i].Capacidad)).style({"numberFormat": "#,##0.00","horizontalAlignment":"right"});
          total+=parseFloat(parsed[i].Capacidad);
        }
        workbook.sheet(hoja).cell("H"+(parsed.length+inicio)).value(parsed.length).style({bold: true,border:true,"borderColor": "000000","numberFormat": "#,##0"});
        workbook.sheet(hoja).cell("G"+(parsed.length+inicio)).value('Totales:').style({bold: true,border:true,"borderColor": "000000","horizontalAlignment":"right"});
        workbook.sheet(hoja).cell("I"+(parsed.length+inicio)).value(total).style({bold: true,border:true,"borderColor": "000000","numberFormat": "#,##0.00"});
        await exportar(archivo,id,tipo,workbook,res);
      } catch (e) {
        console.log(e);
        res.send('..Error.. Hubo un problema al procesar la solicitud, intenta de nuevo más tarde');
      }

    });

}
//Termina reporte en llehandos.html
//Empieza reporte en rellenados.html
function llenarBarrilesReLlenados(archivo,res,fecha,fecha2,tipo,id){
  XlsxPopulate.fromFileAsync('archivos/'+archivo+'.xlsx')
    .then(async workbook => {
      try {
        const hoja="Principal";
        workbook.sheet(hoja).cell("I6").value(PickerToNormal(fecha));
        workbook.sheet(hoja).cell("M6").value(PickerToNormal(fecha2));
        var url=servidor+'RestApi/GET/get_Reportes.php?rellenados=true&fecha1='+fecha+'&fecha2='+fecha2;
        var result = await conexion(url);
        var parsed =JSON.parse(result);
        var totalB=0,totalLts=0;
        workbook.sheet(hoja).cell("G8").value([['N° Orden','Fecha ODT','Alcohol','Año Alcohol','Uso','Tipo','Total','Total litros']]).style({"fill": "F5F5F6","horizontalAlignment":"center"});
        for (var i = 0; i < parsed.length; i++) {
          workbook.sheet(hoja).cell("G"+(i+9)).value([[parsed[i].NoOrden,parsed[i].FechaOdT,parsed[i].Alcohol,parsed[i].Fecha_Ll,parsed[i].Uso,parsed[i].Estatus]]).style({border:true,"borderColor": "F5F5F6","horizontalAlignment":"center"});
          workbook.sheet(hoja).cell("M"+(i+9)).value(intVal(parsed[i].Total)).style({border:true,"borderColor": "F5F5F6","numberFormat": "#,##0"});
          workbook.sheet(hoja).cell("N"+(i+9)).value(intVal(parsed[i].totalLts)).style({border:true,"borderColor": "F5F5F6","numberFormat": "#,##0.00"});
          totalB+=intVal(parsed[i].Total);
          totalLts+=intVal(parsed[i].totalLts);
        }
        workbook.sheet(hoja).cell("L"+(parsed.length+9)).value('Totales:').style({border:true,"borderColor": "F5F5F6","horizontalAlignment":"center"});
        workbook.sheet(hoja).cell("M"+(parsed.length+9)).value(totalB).style({border:true,"borderColor": "F5F5F6","numberFormat": "#,##0","horizontalAlignment":"right"});
        workbook.sheet(hoja).cell("N"+(parsed.length+9)).value(totalLts).style({border:true,"borderColor": "F5F5F6","numberFormat": "#,##0.00","horizontalAlignment":"right"});
        var inicio=parsed.length+13;
        url=servidor+'RestApi/GET/get_Reportes.php?rellenadosT2=true&fecha1='+fecha+'&fecha2='+fecha2;
        result = await conexion(url);
        parsed =JSON.parse(result);
        var totalLitros=0;
        var totalMerma=0;
        workbook.sheet(hoja).cell("E"+(inicio-1)).value([['Fecha','N° Orden','Año','Alcohol','Tipo','Uso','Etiqueta','Litros','Merma','Último relleno','Relleno actual']]).style({border:true,"borderColor": "000000","horizontalAlignment":"center"});
        for (var i = 0; i < parsed.length; i++) {
          workbook.sheet(hoja).cell("E"+(i+inicio)).value([[parsed[i].Fecha,parsed[i].NoOrden,parsed[i]['Año'],parsed[i].Alcohol,parsed[i].Tipo,parsed[i].Uso,parsed[i].Etiqueta,0,0,parsed[i]['Ultimo relleno'],parsed[i]['Relleno Actual']]]).style({"horizontalAlignment":"center"});
          workbook.sheet(hoja).cell("L"+(i+inicio)).value([[intVal(parsed[i].Litros),intVal(parsed[i].Merma)]]).style({"numberFormat": "#,##0.00","horizontalAlignment":"right"});
          totalLitros+=intVal(parsed[i].Litros);
          totalMerma+=intVal(parsed[i].Merma);
        }
        workbook.sheet(hoja).cell("I"+(parsed.length+inicio)).value(parsed.length).style({bold: true,border:true,"borderColor": "000000","numberFormat": "#,##0"});
        workbook.sheet(hoja).cell("H"+(parsed.length+inicio)).value('Total B:').style({bold: true,border:true,"borderColor": "000000","horizontalAlignment":"right"});
        workbook.sheet(hoja).cell("K"+(parsed.length+inicio)).value('Total Lts:').style({bold: true,border:true,"borderColor": "000000","horizontalAlignment":"right"});
        workbook.sheet(hoja).cell("M"+(parsed.length+inicio)).value('Total Merma:').style({bold: true,border:true,"borderColor": "000000","horizontalAlignment":"right"});
        workbook.sheet(hoja).cell("L"+(parsed.length+inicio)).value(totalLitros).style({bold: true,border:true,"borderColor": "000000","numberFormat": "#,##0.00"});
        workbook.sheet(hoja).cell("N"+(parsed.length+inicio)).value(totalMerma).style({bold: true,border:true,"borderColor": "000000","numberFormat": "#,##0.00"});
        await exportar(archivo,id,tipo,workbook,res);
      } catch (e) {
        console.log(e);
        res.send('..Error.. Hubo un problema al procesar la solicitud, intenta de nuevo más tarde');
      }

    });

}
//Termina reporte en rellenados.html
//Inicia reporte en trasiego.html
function llenarBarrilesTrasiego(archivo,res,fecha,fecha2,tipo,id){
  XlsxPopulate.fromFileAsync('archivos/'+archivo+'.xlsx')
    .then(async workbook => {
      try {
        const hoja="Principal";
        workbook.sheet(hoja).cell("H6").value(PickerToNormal(fecha));
        workbook.sheet(hoja).cell("L6").value(PickerToNormal(fecha2));
        var url=servidor+'RestApi/GET/get_Reportes.php?trasiego=true&fecha1='+fecha+'&fecha2='+fecha2;
        var result = await conexion(url);
        var parsed =JSON.parse(result);
        workbook.sheet(hoja).range("J8:K8").merged(true);
        var totalB=0,totalLts=0,cantTanq=0;
        workbook.sheet(hoja).cell("E8").value([['N° Orden','Fecha','Alcohol','Tanque','Cantidad Tanque','Total de Barriles','','Total Litros']]).style({"fill": "F5F5F6","horizontalAlignment":"center"});
        for (var i = 0; i < parsed.length; i++) {
          workbook.sheet(hoja).range("J"+(i+9)+":K"+(i+9)).merged(true);
          workbook.sheet(hoja).cell("E"+(i+9)).value([[parsed[i].NoOrden,parsed[i].Fecha,parsed[i].Alcohol,parsed[i].Tanque]]).style({border:true,"borderColor": "F5F5F6","horizontalAlignment":"center"});
          workbook.sheet(hoja).cell("I"+(i+9)).value([[intVal(parsed[i].CantTanq),0,0,parsed[i].TotalLts]]).style({border:true,"borderColor": "F5F5F6","numberFormat": "#,##0.00","horizontalAlignment":"right"});
          workbook.sheet(hoja).cell("J"+(i+9)).value(intVal(parsed[i].TotalBarriles)).style({border:true,"borderColor": "F5F5F6","numberFormat": "#,##0","horizontalAlignment":"right"});
          totalB+=intVal(parsed[i].TotalBarriles);
          totalLts+=intVal(parsed[i].TotalLts);
          cantTanq+=intVal(parsed[i].CantTanq);
        }
        workbook.sheet(hoja).range("J"+(parsed.length+9)+":K"+(parsed.length+9)).merged(true);
        workbook.sheet(hoja).cell("H"+(parsed.length+9)).value('Totales:').style({border:true,"borderColor": "F5F5F6","horizontalAlignment":"center"});
        workbook.sheet(hoja).cell("I"+(parsed.length+9)).value(cantTanq).style({border:true,"borderColor": "F5F5F6","numberFormat": "#,##0.00","horizontalAlignment":"right"});
        workbook.sheet(hoja).cell("J"+(parsed.length+9)).value(totalB).style({border:true,"borderColor": "F5F5F6","numberFormat": "#,##0","horizontalAlignment":"right"});
        workbook.sheet(hoja).cell("L"+(parsed.length+9)).value(totalLts).style({border:true,"borderColor": "F5F5F6","numberFormat": "#,##0.00","horizontalAlignment":"right"});
        var inicio=parsed.length+13;
        url=servidor+'RestApi/GET/get_Reportes.php?trasiegoT2=true&fecha1='+fecha+'&fecha2='+fecha2;
        result = await conexion(url);
        parsed =JSON.parse(result);
        var totalLitros=0;
        workbook.sheet(hoja).cell("E"+(inicio-1)).value([['Número','Fecha','Alcohol','Etiqueta','Capacidad','Uso','Año Alcohol','Tanque']]).style({border:true,"borderColor": "000000","horizontalAlignment":"center"});
        for (var i = 0; i < parsed.length; i++) {
          workbook.sheet(hoja).cell("E"+(i+inicio)).value([[parsed[i].Num,parsed[i].Fecha,parsed[i].Alcohol,parsed[i].Etiqueta,0,parsed[i].Uso,parsed[i]['Año Alcohol'],parsed[i].Tanque]]).style({"horizontalAlignment":"center"});
          workbook.sheet(hoja).cell("I"+(i+inicio)).value(parseFloat(parsed[i].Capacidad)).style({"numberFormat": "#,##0.00","horizontalAlignment":"right"});
          totalLitros+=parseFloat(parsed[i].Capacidad);
        }
        workbook.sheet(hoja).range("E"+(parsed.length+inicio)+":F"+(parsed.length+inicio)).merged(true);
        workbook.sheet(hoja).cell("G"+(parsed.length+inicio)).value([[parsed.length,0]]).style({bold: true,border:true,"borderColor": "000000","numberFormat": "#,##0","horizontalAlignment":"right"});
        workbook.sheet(hoja).cell("E"+(parsed.length+inicio)).value('Total barriles:').style({bold: true,border:true,"borderColor": "000000","horizontalAlignment":"right"});
        workbook.sheet(hoja).cell("H"+(parsed.length+inicio)).value('Total litros:').style({bold: true,border:true,"borderColor": "000000","horizontalAlignment":"right"});
        workbook.sheet(hoja).cell("I"+(parsed.length+inicio)).value(totalLitros).style({bold: true,border:true,"borderColor": "000000","numberFormat": "#,##0.00","horizontalAlignment":"right"});
        await exportar(archivo,id,tipo,workbook,res);
      } catch (e) {
        console.log(e);
        res.send('..Error.. Hubo un problema al procesar la solicitud, intenta de nuevo más tarde');
      }

    });

}
//Termina reporte en trasiego.html
//Inicia reporte en trasiegoHoover.html
function llenarTanquesTrasiego(archivo,res,fecha,fecha2,tipo,id){
  XlsxPopulate.fromFileAsync('archivos/'+archivo+'.xlsx')
    .then(async workbook => {
      try {
        const hoja=workbook.sheet("Principal");
        hoja.cell("J5").value(PickerToNormal(fecha));
        hoja.cell("L5").value(PickerToNormal(fecha2));
        var url=servidor+'RestApi/GET/get_Reportes.php?trasiegoHoover=true&fecha1='+fecha+'&fecha2='+fecha2;
        var result = await conexion(url);
        var parsed =JSON.parse(result);
        var etiqueta="";
        var pos=9,tanques=0;
        for (var i = 0; i < parsed.length; i++) {
          if(parsed[i].Etiqueta!==etiqueta){//Se encontro un nuevo tanque hoover
            tanques++;
            etiqueta=parsed[i].Etiqueta;
            hoja.cell("E"+(i+pos)).value([[parsed[i].Etiqueta,parseFloat(parsed[i].Litros),parsed[i].FechaLLenado,'','','','','']]).style({border:true,"borderColor": "F5F5F6","horizontalAlignment":"center"});
            pos++;
            hoja.cell("E"+(i+pos)).value([['','','',parsed[i].IdOrden,parsed[i].EtiquetaBarr,parsed[i].Descripcion,parsed[i].Recepcion,parseFloat(parsed[i].Capacidad)]]).style({border:true,"borderColor": "F5F5F6","horizontalAlignment":"center"});
          }else{//Se añaden los barriles de cada tanque hoover
            hoja.cell("E"+(i+pos)).value([['','','',parsed[i].IdOrden,parsed[i].EtiquetaBarr,parsed[i].Descripcion,parsed[i].Recepcion,parseFloat(parsed[i].Capacidad)]]).style({border:true,"borderColor": "F5F5F6","horizontalAlignment":"center"});
          }
        }
        var posFinal=(parsed.length+pos)+2;
        hoja.range("E"+posFinal+":F"+posFinal).merged(true);
        hoja.cell("E"+posFinal).value('Total tanques llenados').style({border:true,"borderColor": "000000","horizontalAlignment":"center"});
        hoja.range("I"+posFinal+":J"+posFinal).merged(true);
        hoja.cell("I"+posFinal).value('Total barriles vacíados').style({border:true,"borderColor": "000000","horizontalAlignment":"center"});
        hoja.cell("G"+posFinal).value(tanques).style({border:true,"borderColor": "000000","horizontalAlignment":"right","numberFormat": "#,##0"});
        hoja.cell("K"+posFinal).value(parsed.length).style({border:true,"borderColor": "000000","horizontalAlignment":"right","numberFormat": "#,##0"});
        await exportar(archivo,id,tipo,workbook,res);
      } catch (e) {
        console.log(e);
        res.send('..Error.. Hubo un problema al procesar la solicitud, intenta de nuevo más tarde');
      }

    });

}
//Termina reporte en trasiegoHoover.html
//Incia reporte en descripcion.php
function llenarDetalleBarril(archivo,res,almacen,area,seccion,alcohol,cod,fecha,tipo,id){
  XlsxPopulate.fromFileAsync('archivos/'+archivo+'.xlsx')
    .then(async workbook => {
      try {
        const hoja="Principal";
        var url=servidor+'RestApi/GET/get_Reportes.php?detallesXBarril=true&almacen='+almacen+'&area='+area+'&seccion='+seccion+'&alcohol='+alcohol+'&codificacion='+cod+'&fecha='+fecha;
        var result = await conexion(url);
        var parsed =JSON.parse(result);
        if(parsed.length>0){
          var totalLitros=0;
          workbook.sheet(hoja).cell("E8").value(parsed[0].Bod);
          workbook.sheet(hoja).cell("J8").value(parsed[0].Costado);
          workbook.sheet(hoja).cell("O8").value(parsed[0].Fila);
          for (var i = 0; i < parsed.length; i++) {
            workbook.sheet(hoja).cell("D"+(i+15)).value([[parsed[i].Num,parsed[i].Torre,parsed[i].Nivel,parsed[i].Lote,parsed[i].Alcohol,parsed[i].DiasAlco,parsed[i].Uso,parsed[i].Fec_Barril,parsed[i].DiasBarr,parsed[i].Edad,parsed[i].FechaRevisado,parsed[i].FechaRelleno,parsed[i].NoTapa,0,parsed[i].Etiqueta]]).style({"horizontalAlignment":"center"});
            workbook.sheet(hoja).cell("Q"+(i+15)).value(parseFloat(parsed[i].CapacidadIni)).style({"numberFormat": "#,##0.00","horizontalAlignment":"right"});
            totalLitros+=parseFloat(parsed[i].CapacidadIni);
          }
          workbook.sheet(hoja).cell("N"+(parsed.length+15)).value('Totales:').style({bold: true,border:true,"borderColor": "000000","horizontalAlignment":"right"});
          workbook.sheet(hoja).cell("O"+(parsed.length+15)).value(parsed.length).style({bold: true,border:true,"borderColor": "000000","numberFormat": "#,##0"});
          workbook.sheet(hoja).cell("Q"+(parsed.length+15)).value(totalLitros).style({bold: true,border:true,"borderColor": "000000","numberFormat": "#,##0.00"});
        }
        await exportar(archivo,id,tipo,workbook,res);
      } catch (e) {
        console.log(e);
        res.send('..Error.. Hubo un problema al procesar la solicitud, intenta de nuevo más tarde');
      }

    });
}
//Termina reporte en descripcion.php

//Incia reporte en descripcion_bodegas.php para barriles
function llenarDetalleBarrilPlantel(archivo,res,almacen,area,cod,tipo,id){
  XlsxPopulate.fromFileAsync('archivos/'+archivo+'.xlsx')
    .then(async workbook => {
      try {
        const hoja="Principal";
        var url=servidor+'RestApi/GET/get_Reportes.php?detallesBarrilesPlantel=true&almacen='+almacen+'&area='+area+'&codificacion='+cod;
        var result = await conexion(url);
        var parsed =JSON.parse(result);
        if(parsed.length>0){
          workbook.sheet(hoja).cell("E8").value(parsed[0].Bod);
          workbook.sheet(hoja).cell("I8").value(parsed[0].Costado);
          for (var i = 0; i < parsed.length; i++) {
            workbook.sheet(hoja).cell("D"+(i+15)).value([[parsed[i].Num,parsed[i].Barril,parsed[i]['Año'],parsed[i].Fecha,parsed[i].DiasBarril,parsed[i].Edad,parsed[i].Estado,parsed[i].Etiqueta]]).style({"horizontalAlignment":"center"});
          }
          workbook.sheet(hoja).range("D"+(parsed.length+15)+":E"+(parsed.length+15)).merged(true);
          workbook.sheet(hoja).cell("D"+(parsed.length+15)).value('Total barriles:').style({bold: true,border:true,"borderColor": "000000","horizontalAlignment":"right"});
          workbook.sheet(hoja).cell("F"+(parsed.length+15)).value(parsed.length).style({bold: true,border:true,"borderColor": "000000","numberFormat": "#,##0"});
        }
        await exportar(archivo,id,tipo,workbook,res);
      } catch (e) {
        console.log(e);
        res.send('..Error.. Hubo un problema al procesar la solicitud, intenta de nuevo más tarde');
      }

    });
}
//Termina reporte en descripcion_bodegas.php para barriles

//Incia reporte en descripcion_bodegas.php para tanques
function llenarDetalleTanquePlantel(archivo,res,almacen,area,tipo,id){
  XlsxPopulate.fromFileAsync('archivos/'+archivo+'.xlsx')
    .then(async workbook => {
      try {
        const hoja="Principal";
        var url=servidor+'RestApi/GET/get_Reportes.php?detallesTanquesPlantel=true&almacen='+almacen+'&area='+area;
        var result = await conexion(url);
        var parsed =JSON.parse(result);
        if(parsed.length>0){
          workbook.sheet(hoja).cell("E8").value(parsed[0].Bod);
          workbook.sheet(hoja).cell("I8").value(parsed[0].Cost);
          for (var i = 0; i < parsed.length; i++) {
            workbook.sheet(hoja).cell("E"+(i+12)).value([[parsed[i].Etiqueta,parsed[i]['Año'],parsed[i].FechaTanque,parsed[i].DiasTanque,parsed[i].Estado]]).style({"horizontalAlignment":"center"});
          }
          workbook.sheet(hoja).range("E"+(parsed.length+12)+":F"+(parsed.length+12)).merged(true);
          workbook.sheet(hoja).cell("E"+(parsed.length+12)).value('Total tanques:').style({bold: true,border:true,"borderColor": "000000","horizontalAlignment":"right"});
          workbook.sheet(hoja).cell("G"+(parsed.length+12)).value(parsed.length).style({bold: true,border:true,"borderColor": "000000","numberFormat": "#,##0"});
        }
        await exportar(archivo,id,tipo,workbook,res);
      } catch (e) {
        console.log(e);
        res.send('..Error.. Hubo un problema al procesar la solicitud, intenta de nuevo más tarde');
      }

    });
}
//Termina reporte en descripcion_bodegas.php para tanques
function convinarCeldas(hoja,convinaciones){
  for (var i = 0; i < convinaciones.length; i++) {
    hoja.range(convinaciones[i]).merged(true);
  }
}



function conexion(url) {
  var request = require('request');
  var result;
  console.log(url);
  return new Promise(function (resolve, reject) {
    request({
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        "Authorization":autenticacion
      },
      url: url,
      method: 'GET'
    },async function (error, response, body) {
      if (!error && response.statusCode == 200) {
        resolve(body.trim());
      }else{
        reject('Error');
      }
    })
  });
}
