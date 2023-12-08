let tablePrestamosFinalizados;
document.addEventListener('DOMContentLoaded', function(){
tablePrestamosFinalizados = $('#tablePrestamosFinalizados').dataTable( 
{
    "aProcessing":true,
    "aServerSide":true,
    "language": {
        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
    },
    "ajax":{
        "url": " "+base_url+"/PrestamosFinalizados/getPrestamosFinalizados",
        "dataSrc":""
    },
    "columns":[
        {"data":"datecreated"}, 
        {"data":"datefinal"},
        {"data":"nombres"},
        {"data":"abonos"},
        {"data":"options"}
    ],
    
    "resonsieve":"true",
    "bDestroy": true,
    "iDisplayLength": 20,
    "order":[[0,"desc"]]  
});
    
}, false);

function fntViewPrestamoFinalizado(idprestamo)
{
    document.querySelector(".AF").classList.add("d-none");
    document.querySelector(".PF").classList.remove("d-none");
    document.querySelector("#idPagosFinalizados").classList.add("d-none");
    document.querySelector("#tableViewPrestamoFinalizado").classList.remove('d-none');

    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/PrestamosFinalizados/getPrestamoFinalizado/'+idprestamo;
    request.open("GET",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function()
    {
        if(request.readyState == 4 && request.status == 200)
        {
            let objData = JSON.parse(request.responseText);
            if(objData.status){
                document.querySelector("#celNombresFinalizado").innerHTML = objData.data.nombres;
                document.querySelector("#celNegocioFinalizado").innerHTML = objData.data.apellidos;
                document.querySelector("#celMontoFinalizado").innerHTML = smoney + objData.data.monto;
                document.querySelector("#celFormatoFinalizado").innerHTML =  objData.data.formato;
                document.querySelector("#celTazaFinalizado").innerHTML = objData.data.taza;
                document.querySelector("#celParcelaFinalizado").innerHTML = smoney + objData.data.parcela;
                document.querySelector("#celPlazoFinalizado").innerHTML = objData.data.plazo;
                document.querySelector("#celPagadoFinalizado").innerHTML = smoney + objData.data.pagado

                $('#modalFormPagosFinalizados').modal('show');
            }else{
                swal("Error", objData.msg, "error");
            }
        }
    }
}

function fntlistPagosFinalizados(idprestamo)
{  
    document.querySelector(".AF").classList.remove("d-none");
    document.querySelector(".PF").classList.add("d-none");
    document.querySelector("#idPagosFinalizados").classList.remove("d-none");
    document.querySelector("#tableViewPrestamoFinalizado").classList.add("d-none");
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Prestamos/getListPagos/'+idprestamo;
    request.open("GET",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function()
    {
        if(request.readyState == 4 && request.status == 200)
        {
            let objData = JSON.parse(request.responseText);
            if(objData.status)
            {
                let trPagos = objData.data;
                document.querySelector("#listaPagosFinalizados").innerHTML = trPagos;
            }else{
                document.querySelector("#listaPagosFinalizados").innerHTML = '<tr><td class"textcenter" colspan="2">No hay datos</td><tr>';
            }
        }
    }
    $('#modalFormPagosFinalizados').modal('show');
}

function fntDelPago(pagoid)
{
    swal({
        title: "Eliminar Pago",
        text: "¿Realmente quiere eliminar el Pago?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function(isConfirm){
        if(isConfirm)
        {
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url+'/Prestamos/delPago';
            let strData = "pagoId="+pagoid;
            request.open("POST",ajaxUrl,true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);
            request.onreadystatechange = function()
            {
                if(request.readyState == 4 && request.status == 200)
                {
                    let objData = JSON.parse(request.responseText);
                    if(objData.status)
                    {
                        $('#modalFormPagosFinalizados').modal('hide');
                        //$('#modalFormPrestamosFinalizados').modal('hide');
                        //$('#modalViewPrestamo').modal('hide');
                        swal("Eliminar!", objData.msg , "success");
                        if(tablePrestamosFinalizados)
                        {
                            tablePrestamosFinalizados.api().ajax.reload();
                        }else{
                            window.location = base_url+'/prestamos';
                        }
                    }else{
                        swal("Atención!", objData.msg , "error");
                    }
                }
            }
        }
    });
}