let tableRutas;
let rowTable = "";
let divLoading = document.querySelector("#divLoading");
document.addEventListener('DOMContentLoaded', function(){
    tableRutas = $('#tableRutas').dataTable( 
    {
        "aProcessing":true,
        "aServerSide":true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "ajax":{
            "url": " "+base_url+"/Rutas/getRutas",
            "dataSrc":""
        },
        "columns":[
            {"data":"nombre"},
            {"data":"codigo"},
            {"data":"pagamento"},
            {"data":"options"}
        ],
        
        "resonsieve":"true",
        "bDestroy": true,
        "iDisplayLength": 20,
        "order":[[2,"DESC"]]  
    });

    if(document.querySelector("#formRuta")){
        let formRuta = document.querySelector("#formRuta");
        formRuta.onsubmit = function(e)
        {
            e.preventDefault();
            let strNombre = document.querySelector('#txtNombre').value;
            let strDia = document.querySelector('#txtDia').value;

            if(strNombre == '')
            {
                swal("Atención", "Escribe un nombre.", "error");
                return false;
            }

            let ElementsValid = document.getElementsByClassName("valid");
            for (let i = 0; i < ElementsValid.length; i++) {
                if(ElementsValid[i].classList.contains('is-invalid')){
                    swal("Atención!", "Por favor verifique los campos en rojo.", "error");
                    return false;
                }
            }

            divLoading.style.display = "flex";
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url + '/Rutas/setRutas';
            let formData = new FormData(formRuta);
            request.open("POST",ajaxUrl,true);
            request.send(formData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200)
                {
                    let objData = JSON.parse(request.responseText);
                    if(objData.status)
                    {
                        if(rowTable == ""){
                            tableRutas.api().ajax.reload();
                        }else{
                            rowTable.cells[0].textContent = strNombre;
                            rowTable.cells[2].textContent = strDia;
                            rowTable = "";
                        }
                        $('#modalFormRutas').modal("hide");
                        formRuta.reset();
                        swal("Ruta", objData.msg, "success");
                        //tableRutas.api().ajax.reload();
                        
                    }else{
                        swal("Error", objData.msg, "error");
                    }
                }
                divLoading.style.display = "none";
                return false;
            }
        }
    }
}, false);

function fntEditInfo(element, idruta)
{
    rowTable = element.parentNode.parentNode.parentNode;
    document.querySelector('#titleModal').innerHTML = "Actualizar Ruta";
    document.querySelector('#btnText').innerHTML = "Actualizar";
    document.querySelector("#diaP").classList.remove('d-none');

    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Rutas/getRuta/'+idruta;
    request.open("GET",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function(){

        if(request.readyState == 4 && request.status == 200){
            let objData = JSON.parse(request.responseText);
            if(objData.status)
            {
                document.querySelector("#codigoRuta").value = objData.data.codigo;
                document.querySelector("#txtDia").value = objData.data.pagamento;
                document.querySelector("#txtNombre").value = objData.data.nombre;
            }
                
        }
        $('#modalFormRutas').modal('show');
    }
}

function fntDelInfo(idruta)
{
    swal({
        title: "Eliminar Ruta",
        text: "¿Realmente quiere eliminar ella Ruta?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function(isConfirm) {
        
        if (isConfirm) 
        {
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url+'/Rutas/delRuta';
            let strData = "idRuta="+idruta;
            request.open("POST",ajaxUrl,true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    let objData = JSON.parse(request.responseText);
                    if(objData.status)
                    {
                        swal("Eliminar!", objData.msg , "success");
                        tableRutas.api().ajax.reload();
                    }else{
                        swal("Atención!", objData.msg , "error");
                    }
                }
            }
        }
    });
}

function openModal()
{
    rowTable = "";  
    document.querySelector("#diaP").classList.add('d-none');
    document.querySelector('#codigoRuta').value ="";
    document.querySelector('#btnText').innerHTML ="Guardar";
    document.querySelector('#titleModal').innerHTML = "Nueva Ruta";
    document.querySelector("#formRuta").reset();
    $('#modalFormRutas').modal('show');
}