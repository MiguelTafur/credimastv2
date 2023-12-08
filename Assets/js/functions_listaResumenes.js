let tableResumenes;
document.addEventListener('DOMContentLoaded', function(){
tableResumenes = $('#tableResumenes').dataTable( 
{
    "aProcessing":true,
    "aServerSide":true,
    "language": {
        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
    },
    "ajax":{
        "url": " "+base_url+"/ListaResumenes/getResumenes",
        "dataSrc":""
    },
    "columns":[
        {"data":"datecreated"}, 
        {"data":"base"},
        {"data":"cobrado"},
        {"data":"ventas"},
        {"data":"gasto"},
        {"data":"total"}
    ],
    "resonsieve":"true",
    "bDestroy": true,
    "iDisplayLength": 20,
    "order":[[0,"desc"]] 
});
    
}, false);

