'use strict'

const servidor="http://localhost:80/Nicaragua/";
const Excel = require('exceljs');
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
        llenarRLlenadoMantenimineto('RLlenadoMantenimineto',req.query.fecha,res,req.query.tipo,id);
      }else if(req.query.reporte==='RLlenadoRevisado'){
        llenarRLlenadoRevisado('RLlenadoRevisado',req.query.fecha,res,req.query.tipo,id);
      }else if(req.query.reporte==='RGerencia'){
        llenarRGerencia('RGerencia',res,req.query.tipo,id);
      }else if(req.query.reporte==='RTrasiegoHojaAnalisis'){
        llenarRTrasiegoHojaAnalisis('RTrasiegoHojaAnalisis',req.query.fecha,req.query.tanque,res,req.query.tipo,id);
      }else if(req.query.reporte==='RRellenoOperacion'){
        llenarRRellenoOperacion('RRellenoOperacion',req.query.fecha,req.query.ope,res,req.query.tipo,id);
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
  const XlsxPopulate = require('xlsx-populate');
  XlsxPopulate.fromFileAsync('archivos/'+archivo+'.xlsx')
    .then(async workbook => {
      const hoja="Principal";
      workbook.sheet(hoja).cell("I8").value(PickerToNormal(fecha));
      var url=servidor+'RestApi/GET/get_ReportesTrasiego.php?FSI82498=true&fecha='+fecha+'&tanque='+tanque;
      var result = await conexion(url);
      var parsed =JSON.parse(result);
      if(parsed.length>0){
        var totalCapa=0;
        var capa=0;
        var Barriles=0;
        var BarrilesAnnio=0;
        var capaAnnio=0;
        var uso=parsed[0]['Uso'];
        var anio=parsed[0]['Año'];
        var cantidad=parseInt(parsed[0]['Cantidad']);
        var pos=11;
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
            capa=0;
            Barriles=0;
            capaAnnio=0;
            BarrilesAnnio=0;
          }
          if(uso!==parsed[i]['Uso']){
            workbook.sheet(hoja).cell("D"+pos).value([['','',Barriles+' Barriles','Total tipo barril']]).style({border:true,"borderColor": "F5F5F6","bold": true});
            workbook.sheet(hoja).cell("H"+pos).value(capa).style({border:true,"borderColor": "F5F5F6","numberFormat": "#,##0.00","bold": true});
            pos+=2;
            nuevoUsoVaciados(workbook.sheet(hoja),pos);
            uso=parsed[i]['Uso'];
            pos++;
            capa=0;
            Barriles=0;
          }
          totalCapa+=parseInt(parsed[i]['Capacidad']);
          capa+=parseInt(parsed[i]['Capacidad']);
          capaAnnio+=parseInt(parsed[i]['Capacidad']);
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
        url=servidor+'RestApi/GET/get_ReportesTrasiego.php?tanque='+tanque;
        result = await conexion(url);
        parsed =JSON.parse(result);
        workbook.sheet(hoja).cell("F8").value(parsed[0].Descripcion);
      }

      await exportar(archivo,id,tipo,workbook,res);
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
  const XlsxPopulate = require('xlsx-populate');
  XlsxPopulate.fromFileAsync('archivos/'+archivo+'.xlsx')
    .then(async workbook => {
      const hoja="Principal";
      workbook.sheet(hoja).cell("E14").value(PickerToNormal(fecha));
      workbook.sheet(hoja).cell("I14").value(fecha.substring(0, 4));
      var url=servidor+'RestApi/GET/get_ReportesTrasiego.php?FSI61194=true&fecha='+fecha+'&tanque='+tanque;
      var result = await conexion(url);
      var parsed =JSON.parse(result);
      if(parsed.length>0){
        var totalCapa=0;
        workbook.sheet(hoja).cell("K14").value(parsed[0]["Hora"]);
        for (var i = 0; i < parsed.length; i++) {
          delete parsed[i]["Hora"];
          workbook.sheet(hoja).cell("D"+(i+19)).value([Object.values(parsed[i])]).style({border:true,"borderColor": "F5F5F6"});
          totalCapa+=parseInt(parsed[i]['Cantidad']);
        }
        workbook.sheet(hoja).cell("H"+(parsed.length+19)).value('Total').style({border:true,"borderColor": "F5F5F6"});
        workbook.sheet(hoja).cell("I"+(parsed.length+19)).value(totalCapa).style({border:true,"borderColor": "F5F5F6"});
        url=servidor+'RestApi/GET/get_ReportesTrasiego.php?tanque='+tanque;
        result = await conexion(url);
        parsed =JSON.parse(result);
        workbook.sheet(hoja).cell("E8").value(parsed[0].Descripcion);
        borrarFilas((parsed.length+21),548,workbook.sheet(hoja));
      }
      await exportar(archivo,id,tipo,workbook,res);
    });
}

async function llenarRTrasiegoHojaAnalisis(archivo,fecha,tanque,res,tipo,id){
  const XlsxPopulate = require('xlsx-populate');
  XlsxPopulate.fromFileAsync('archivos/'+archivo+'.xlsx')
    .then(async workbook => {
      const hoja="Principal";
      workbook.sheet(hoja).cell("L9").value(PickerToNormal(fecha));
      var url=servidor+'RestApi/GET/get_ReportesTrasiego.php?FSI82493=true&fecha='+fecha+'&tanque='+tanque;
      var result = await conexion(url);
      var parsed =JSON.parse(result);
      if(parsed.length>0){
        workbook.sheet(hoja).cell("G101").value(parsed[0].Tanque);
        for (var i = 0; i < parsed.length; i++) {
          delete parsed[i].Fecha;
          delete parsed[i].Renglon;
          delete parsed[i].Tanque;
          workbook.sheet(hoja).cell("E"+(i+13)).value([Object.values(parsed[i])]).style({border:true,"borderColor": "F5F5F6","numberFormat": "#,##0"});
        }
        borrarFilas((parsed.length+13),97,workbook.sheet(hoja));
        url=servidor+'RestApi/GET/get_ReportesTrasiego.php?tanque='+tanque;
        result = await conexion(url);
        parsed =JSON.parse(result);
        workbook.sheet(hoja).cell("L7").value(parsed[0].Descripcion);
      }

      await exportar(archivo,id,tipo,workbook,res);
    });
}

async function llenarRRellenoOperacion(archivo,fecha,operacion,res,tipo,id){
  const XlsxPopulate = require('xlsx-populate');
  XlsxPopulate.fromFileAsync('archivos/'+archivo+'.xlsx')
    .then(async workbook => {
      const hoja="Principal";
      workbook.sheet(hoja).cell("G6").value(PickerToNormal(fecha));
      workbook.sheet(hoja).cell("D5").value((operacion==='3'?'Reporte Diario de Operación de Relleno':'Reporte Diario de Operación de Trasiego'));
      workbook.sheet(hoja).cell("I5").value((operacion==='3'?'FSI 82.4.8.4':'FSI 82.4.9.8'));
      var url=servidor+'RestApi/GET/get_ReportesTrasiego.php?RepOPDetalle=true&fecha='+fecha+'&ope='+operacion;
      var result = await conexion(url);
      var parsed =JSON.parse(result);
      if(parsed.length>0){
        var totalOrden=0;
        var totalLitros=0;
        var totalBarriles=0;
        var Estatus=parsed[0]['Estatus']
        var IdOrden=parsed[0]['IdOrden']
        var pos=8;
        var totalPosOrden=8;
        var totalPosEstatus=11;
        nuevaOrdenOperacion(workbook.sheet(hoja),pos,parsed[0])
        pos+=6;
        for (var i = 0; i < parsed.length; i++) {
          if(IdOrden!==parsed[i]['IdOrden']){
            workbook.sheet(hoja).cell('I'+pos).value(totalLitros).style({"bold": true,"numberFormat": "#,##0.00"});
            workbook.sheet(hoja).cell('H'+pos).value('Total LTS').style({"bold": true});
            workbook.sheet(hoja).cell('J'+totalPosOrden).value(totalOrden).style({"bold": true,"numberFormat": "#,##0"});
            workbook.sheet(hoja).cell('F'+totalPosEstatus).value(totalBarriles).style({"bold": true,"numberFormat": "#,##0"});
            totalOrden=0;
            totalBarriles=0;
            totalLitros=0;
            pos+=3;
            totalPosOrden=pos;
            totalPosEstatus=pos+3;
            nuevaOrdenOperacion(workbook.sheet(hoja),pos,parsed[i]);
            pos+=6;
            Estatus=parsed[i]['Estatus'];
            IdOrden=parsed[i]['IdOrden']
          }
          if(Estatus!==parsed[i]['Estatus']){
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
          totalLitros+=parseFloat((parsed[i]['Capacidad']==null?0:parsed[i]['Capacidad']));
        }
        workbook.sheet(hoja).cell('I'+pos).value(totalLitros).style({"bold": true,"numberFormat": "#,##0.00"});
        workbook.sheet(hoja).cell('H'+pos).value('Total LTS').style({"bold": true});
        workbook.sheet(hoja).cell('J'+totalPosOrden).value(totalOrden).style({"bold": true,"numberFormat": "#,##0"});
        workbook.sheet(hoja).cell('F'+totalPosEstatus).value(totalBarriles).style({"bold": true,"numberFormat": "#,##0"});
      }

      await exportar(archivo,id,tipo,workbook,res);
    });
}
function nuevaOrdenOperacion(hoja,inicio,json){
  hoja.cell('E'+inicio).value([['Orden:',json.IdOrden,'Tanque Reg lts:','','Barriles Reg:']]).style({"horizontalAlignment":"center"});
  hoja.cell('H'+inicio).value(json.CantTanq).style({"numberFormat": "#,##0.00"});
  hoja.range("D"+inicio+":J"+inicio).style({ bottomBorder:true,"bold": true});
  nuevaEstadoOperacion(hoja,inicio+2,json);
}
function nuevaEstadoOperacion(hoja,inicio,json){
  hoja.cell('E'+inicio).value(json.Estatus).style({"fill": "7AB0FF"});
  hoja.cell('E'+(inicio+1)).value([['Total Reg:','','Año llenada:',json.Fecha_Ll,'Alcohol:',json.Alcohol]]);
  hoja.range("G"+(inicio+3)+":H"+(inicio+3)).merged(true);
  hoja.cell('E'+(inicio+3)).value([['Etiqueta','Uso','Ubicación','Ubicación','Litros']]).style({"fill": "F5F5F6"});
}
function nuevoRowOperacion(hoja,inicio,json){
  hoja.range("G"+inicio+":H"+inicio).merged(true);
  hoja.cell('E'+inicio).value([[json.Etiqueta,json.Uso,json.Ubicacion]]).style({border:true,"borderColor": "F5F5F6","horizontalAlignment":"center"});
  hoja.cell('I'+inicio).value(json.Capacidad).style({border:true,"borderColor": "F5F5F6","numberFormat": "#,##0.00"});
}

function llenarRLlenadoLlenada(archivo,fecha,res,tipo,id){
  const XlsxPopulate = require('xlsx-populate');
  XlsxPopulate.fromFileAsync('archivos/'+archivo+'.xlsx')
    .then(async workbook => {
      const hoja="Principal";
      var url=servidor+'RestApi/GET/get_ReportesLlenado.php?OPDetalleLlen=true&fecha='+fecha;
      var result = await conexion(url);
      var parsed =JSON.parse(result);
      if(parsed.length>0){
        var totalCapa=0;
        var totalBarr=0;
        var totalTanq=0;
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
            totalTanq=0;
            totalBarr=0;
            totalCapa=0;
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

function llenarRLlenadoMantenimineto(archivo,fecha,res,tipo,id){
  const XlsxPopulate = require('xlsx-populate');
  XlsxPopulate.fromFileAsync('archivos/'+archivo+'.xlsx')
    .then(async workbook => {
      const hoja="Principal";
      var url=servidor+'RestApi/GET/get_ReportesLlenado.php?OPDetalleMant=true&fecha='+fecha;
      workbook.sheet(hoja).cell('E7').value(PickerToNormal(fecha));
      var result = await conexion(url);
      var parsed =JSON.parse(result);
      if(parsed.length>0){
        for (var i = 0; i < parsed.length; i++) {
          workbook.sheet(hoja).cell('E'+(i+10)).value(parsed[i].Reparación).style({border:true,"borderColor": "F5F5F6","horizontalAlignment":"center"});
          workbook.sheet(hoja).cell('G'+(i+10)).value(parsed[i].Uso).style({border:true,"borderColor": "F5F5F6","horizontalAlignment":"center"});
          workbook.sheet(hoja).cell('H'+(i+10)).value(parsed[i].Total).style({border:true,"borderColor": "F5F5F6","numberFormat": "#,##0"});
        }
        borrarFilas((parsed.length+10),13,workbook.sheet(hoja));
        url=servidor+'RestApi/GET/get_ReportesLlenado.php?OPDetalleMantDet=true&fecha='+fecha;
        result = await conexion(url);
        parsed =JSON.parse(result);
        for (var i = 0; i < parsed.length; i++) {
          workbook.sheet(hoja).cell('E'+(i+16)).value([[parsed[i].Etiqueta,parsed[i].Uso,parsed[i].Operador,parsed[i].Reparación]]).style({border:true,"borderColor": "F5F5F6","horizontalAlignment":"center"});
        }
      }
      await exportar(archivo,id,tipo,workbook,res);
    });
}

async function exportar(archivo,id,tipo,workbook,res){
  await workbook.toFileAsync('archivos/'+archivo+'_'+id+'_temporal.xlsx');//Escribe el archivo en un temporal
  if(tipo==='xlsx' || tipo==='excel'){//Descargar en excel
    res.send('archivos/'+archivo+'_'+id+'_temporal.xlsx')//Avisamos al usuario que el archivo esta listo con la ubicacion donde se encuentra
    setTimeout(function() {
      fs.unlinkSync(__dirname+'/archivos/'+archivo+'_'+id+'_temporal.xlsx');
    }, 5000);

  }else{
    convertirArchivo(archivo,res,id,tipo);//El usuario lo quiere en pdf
  }
}


function llenarRLlenadoRevisado(archivo,fecha,res,tipo,id){
  const XlsxPopulate = require('xlsx-populate');
  XlsxPopulate.fromFileAsync('archivos/'+archivo+'.xlsx')
    .then(async workbook => {
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
  const XlsxPopulate = require('xlsx-populate');
  XlsxPopulate.fromFileAsync('archivos/'+archivo+'.xlsx')
    .then(async workbook => {
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
async function stringToJson(url){
  try {
    var datos;
    datos=await conexion(url);
    return JSON.parse(datos);
  } catch (e) {
    return [];
  }
}
function FormatDate(fecha,sep){
  if(fecha!=null){
    var res = fecha.split("-");
    return res[2]+sep+res[1]+sep+res[0];
  }else{
    return "";
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
        resolve(body);
      }else{
        reject('Error');
      }
    })
  });
}
