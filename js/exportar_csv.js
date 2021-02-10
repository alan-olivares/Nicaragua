function exportTableToCSV(filename) {
    var csv = [];
    var rows = document.querySelectorAll("table tr");
    if(rows.length>0){
      for (var i = 0; i < rows.length; i++) {
          var row = [], cols = rows[i].querySelectorAll("td, th");
          for (var j = 0; j < cols.length; j++)
              row.push('\"' + cols[j].innerText + '\"');
          csv.push(row.join(","));
      }
      downloadCSV(csv.join("\n"), filename);
    }else{
      window.alert("Haz una busqueda antes de exportar");
    }

}
function downloadCSV(csv, filename) {
    //Codigo para exportar caracteres especiales
    var buffer = new ArrayBuffer(3);
    var dataView = new DataView(buffer);
    dataView.setUint8(0, 0xef);
    dataView.setUint8(1, 0xbb);
    dataView.setUint8(2, 0xbf);
    var read = new Uint8Array(buffer);

    var blob = new Blob([read, csv], {type: 'text/csv;charset=utf-8'});
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
}
