document.addEventListener('DOMContentLoaded', function()
{
    if(document.querySelector("#formPrestamos"))
    {
        let formPrestamos = document.querySelector("#formPrestamos");
        formPrestamos.onsubmit = function(e){
            e.preventDefault();

            let intClienteId = document.querySelector('#listClientId').value;
            let intMonto = document.querySelector('#txtMonto').value;
            let intTaza = document.querySelector('#txtTaza').value;
            let intPlazo = document.querySelector('#txtPlazo').value;
            let intFormato = document.querySelector('#listFormato').value;
            let strObservacion = document.querySelector('#txtObservacion').value;

            if(intClienteId == "" || intMonto == "" || intTaza == "" || intPlazo == "" || intFormato == ""){
                swal("Atención", "Todos los campos son obligatorios.", "error");
                return false;
            }

            if($("#listAddClients").hasClass("d-none")){
                swal("Atención", "Debes agregar un cliente!", "error");
                return false;
            }

            let ElementsValid = document.getElementsByClassName("valid");
            for (let i = 0; i < ElementsValid.length; i++) {
                if(ElementsValid[i].classList.contains('is-invalid')){
                    swal("Atencion!", "Por favor verifique los campos en rojo.", "error");
                    return false;
                }
            }

            divLoading.style.display = "flex";
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url+'/Ventas/setPrestamo';
            let formData = new FormData(formPrestamos);
            request.open("POST",ajaxUrl,true);
            request.send(formData);

            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    let objData = JSON.parse(request.responseText);
                    if(objData.status){
                        swal({
                            title: "Préstamo agregado",
                            text: "El préstamo se ha adicionado a la lista",
                            type: "success",
                            showCancelButton: true,
                            confirmButtonText: "Ver lista",
                            cancelButtonText: "Nuevo préstamo",
                            closeOnConfirm: false,
                            closeOnCancel: true
                        }, function(isConfirm)
                        {
                            if(isConfirm)
                            {
                                window.location = base_url+'/prestamos';
                            }
                        }); 
                        formPrestamos.reset();
                    }else{
                        swal("Error", objData.msg, "error");
                    }
                } 
                divLoading.style.display = "none";
                return false;   
            }
        }
    }

    if(document.querySelector("#formCliente")){
        let formCliente = document.querySelector("#formCliente");
        formCliente.onsubmit = function(e)
        {
            e.preventDefault();
            let strIdentificacion = document.querySelector('#txtIdentificacion').value;
            let strNombre = document.querySelector('#txtNombre').value;
            let strApellido = document.querySelector('#txtApellido').value;
            let intTelefono = document.querySelector('#txtTelefono').value;
            let strDireccion = document.querySelector('#txtDireccion').value;

            if(strIdentificacion == '' || strNombre == '' || strApellido == '' || intTelefono == '' || strDireccion == '')
            {
                swal("Atención", "Todos los campos son obligatorios.", "error");
                return false;
            }

            let ElementsValid = document.getElementsByClassName("valid");
            for (let i = 0; i < ElementsValid.length; i++) {
                if(ElementsValid[i].classList.contains('is-invalid')){
                    swal("Atencion!", "Por favor verifique los campos en rojo.", "error");
                    return false;
                }
            }

            divLoading.style.display = "flex";
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url + '/Clientes/setCliente';
            let formData = new FormData(formCliente);
            request.open("POST",ajaxUrl,true);
            request.send(formData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200)
                {
                    let objData = JSON.parse(request.responseText);
                    if(objData.status)
                    {
                        $('#modalFormCliente').modal("hide");
                        formCliente.reset();
                        swal("Clientes", objData.msg, "success");  
                        fntClientesPrestamo();                      
                    }else{
                        swal("Error", objData.msg, "error");
                    }
                }
                divLoading.style.display = "none";
                return false;
            }
        }
    }
    fntClientesPrestamo();
}, false);

function fntClientesPrestamo()
{
    if(document.querySelector("#listClientId")){
        let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        let ajaxUrl = base_url+'/Clientes/getSelectClientes';
        request.open("POST",ajaxUrl,true);
        request.send();

        request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200){
                document.querySelector('#listClientId').innerHTML = request.responseText;
                $('#listClientId').select2({
                    placeholder: 'Seleccione un Cliente'
                });
            }
        }
    }
}

function openModal()
{
    document.querySelector('#titleModal').innerHTML = "Nuevo Cliente";
    document.querySelector("#formCliente").reset();
    $('#modalFormCliente').modal('show');
}