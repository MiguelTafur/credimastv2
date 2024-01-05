let divLoading = document.querySelector("#divLoading");
let tablePrestamos;
let tablePrestamosFinalizados;
let tableResumenes;
let rowTable = "";

let hoy = new Date();
 
let dia = hoy.getDate();
let mes = hoy.getMonth() + 1;
let anio = hoy.getFullYear();

dia = ("0" + dia).slice(-2);
mes = ("0" + mes).slice(-2);
    
let formateo = `${anio}-${mes}-${dia}`;

//swal("PAGAMENTO DIA 30/01/2023", "Recuerda efectuar el pagamento hasta el dia 30 para evitar bloqueos temporales.", "warning");

document.addEventListener('DOMContentLoaded', function()
{

    //NUEVO PAGO
    if(document.querySelector("#formPagos"))
    {
        let formPagos = document.querySelector("#formPagos");
        formPagos.onsubmit = function(e){
            e.preventDefault();

            let intMonto = document.querySelector('#txtMontoPago').value;

            if(intMonto == ""){
                swal("Atención", "Debes ingresar un monto.", "error");
                return false;
            }


            let ElementsValid = document.getElementsByClassName("valid");
            for (let i = 0; i < ElementsValid.length; i++) {
                if(ElementsValid[i].classList.contains('is-invalid')){
                    swal("Atencion!", "Solo puedes agregar números al monto.", "error");
                    return false;
                }
            }

            divLoading.style.display = "flex";
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url+'/Prestamos/setPago';
            let formData = new FormData(formPagos);
            request.open("POST",ajaxUrl,true);
            request.send(formData);

            request.onreadystatechange = function()
            {
                if(request.readyState == 4 && request.status == 200)
                {
                    let objData = JSON.parse(request.responseText);
                    if(objData.status){
                        if(tablePrestamos)
                        {
                            tablePrestamos.ajax.reload();
                        }
                            formPagos.reset();
                    }else{
                        swal("Error", objData.msg, "error");
                    }
                } 
                divLoading.style.display = "none";
                return false;   
            }
        }
    }

    //RENOVAR PRÉSTAMO
    if(document.querySelector("#formRenovarPrestamo"))
    {
        let formRenovarPrestamo = document.querySelector("#formRenovarPrestamo");
        formRenovarPrestamo.onsubmit = function(e){
            e.preventDefault();

            let intMonto = document.querySelector('#txtMonto').value;
            let intTaza = document.querySelector('#txtTaza').value;
            let intPlazo = document.querySelector('#txtPlazo').value;
            let intFormato = document.querySelector('#listFormato').value;
            let estado = 1;

            if(intMonto == "" || intTaza == "" || intPlazo == "" || intFormato == ""){
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
            let ajaxUrl = base_url+'/Ventas/setPrestamo';
            let formData = new FormData(formRenovarPrestamo);
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
                            closeOnConfirm: true,
                            closeOnCancel: true
                        }, function(isConfirm)
                        {
                            if(isConfirm)
                            {
                                tablePrestamos.ajax.reload();
                                $('#modalRenovarPrestamo').modal('hide'); 
                            }
                        }); 
                        formRenovarPrestamo.reset();
                    }else{
                        swal("Error", objData.msg, "error");
                    }
                } 
                divLoading.style.display = "none";
                return false;   
            }
        }
    }

    //ACTUALIZAR LISTA PRESTAMOS
    if(document.querySelector("#sortable"))
    {
        $("#sortable").sortable({
            connectWith: ".sortable",
            placeholder: 'dragHelper',
            scroll: true,
            rever: true,
            cursor: "move",
            update: function(){
                let lista = $("#sortable").sortable('toArray');
                let ajaxUrl = base_url + '/Prestamos/setUpdateList';
                $.post(ajaxUrl,{listPrestamos:lista});
                if(tablePrestamos)
                {
                    tablePrestamos.ajax.reload();
                }
            }
        });
    }

    //fntClientesPrestamo();
    fntPayToday();
    fntSalesToday();
}, false);

function myFunction(x)
{
  if (x.matches)
  {
    if(document.querySelector("#TPM"))
    {
        document.querySelector("#TP").classList.add("d-none");
        document.querySelector("#TPM").classList.remove("d-none");
        tablePrestamos = $('#tablePrestamosMovil').DataTable( 
        {
        "aProcessing":true,
        "aServerSide":true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "ajax":{
            "url": " "+base_url+"/Prestamos/getPrestamos",
            "dataSrc":""
        },
        "columns":[
            {"data":"nombres"},
            {"data":"total"},
            {"data":"pagamento"},
            {"data":"options"}
        ],
        "resonsieve":"true",
        "bDestroy": true,
        "iDisplayLength": 50,
        "order":[[0,"asc"]]  
        });
    }
  }
  else{
    if(document.querySelector("#TP"))
    {
        document.querySelector("#TP").classList.remove("d-none");
        document.querySelector("#TPM").classList.add("d-none");
        tablePrestamos = $('#tablePrestamos').DataTable( 
        {
            "aProcessing":true,
            "aServerSide":true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
            "ajax":{
                "url": " "+base_url+"/Prestamos/getPrestamos",
                "dataSrc":""
            },
            "columns":[
                //{"data":"orden"}, 
                {"data":"monto"},
                {"data":"formato"},       
                {"data":"total"},                                               
                {"data":"nombres"},
                {"data":"pagamento"},
                {"data":"options"}
            ],
            "columnDefs": [
                { 'className': "textcenter", "targets": [ 0 ] },
                { 'className': "textcenter", "targets": [ 1 ] },
                { 'className': "textcenter", "targets": [ 2 ] },
                { 'className': "textcenter", "targets": [ 3 ] }
              ],   
            "resonsieve":"true",
            "bDestroy": true,
            "iDisplayLength": 50,
            "order":[[0,"asc"]]  
        });
    }
  }

  if(tablePrestamos)
  {
    tablePrestamos.on("init.dt", function()
    {
        for (let i = 0; i < tablePrestamos.rows().count(); i++)
        {
            let row = tablePrestamos.row(i);
            let fechaInicio = row.data().datecreated;
            let fechaFinal = row.data().datefinal;
            let vencimiento = row.data().diasVence;

            //console.log(row.data());

            if(fechaInicio == formateo)
            {
                $(row.node()).addClass("table-success");
            }
            if(fechaFinal == formateo)
            {
                $(row.node()).addClass("table-dark");
            }

            if(vencimiento == false)
            {
                $(row.node()).addClass("table-warning");
            }
            if(vencimiento == "vencido")
            {
                $(row.node()).addClass("table-danger");
            }
        }
    });

  }

}

myFunction(mmObj);


// Add the match function as a listener for state changes:
mmObj.addListener(myFunction);

function fntPayToday(){
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Prestamos/getPayToday';
    request.open("POST",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function()
    {
        if(request.readyState == 4 && request.status == 200)
        {
            let objData = JSON.parse(request.responseText);
            if(objData)
            {
                if(document.querySelector("#iCobrado"))
                {
                    document.querySelector("#iCobrado").innerHTML = objData.cobrado;
                }
            }
        }
    }
}

function fntSalesToday(){
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Prestamos/getSalesToday';
    request.open("POST",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function()
    {
        if(request.readyState == 4 && request.status == 200)
        {
            let objData = JSON.parse(request.responseText);
            if(objData)
            {
                if(document.querySelector("#iVentas"))
                {
                    document.querySelector("#iVentas").innerHTML = objData.ventas;
                }
            }
        }
    }
}

function fntViewPrestamo(idprestamo)
{
    let fecha = "undefined";
    divLoading.style.display = "flex";
    document.querySelector('#titleModal').innerHTML = "Datos de Préstamo";
    document.querySelector("#tableViewPrestamo").classList.remove('d-none');
    document.querySelector("#containerPagos").classList.add('d-none');
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Prestamos/getPrestamo/';
    let formData = new FormData();
    formData.append('idPrestamo',idprestamo);
    formData.append('datefinal',fecha);
    request.open("POST",ajaxUrl,true);
    request.send(formData);
    request.onreadystatechange = function()
    {
        if(request.readyState == 4 && request.status == 200)
        {
            let objData = JSON.parse(request.responseText);
            if(objData.status){
                document.querySelector("#celFecha").classList.add("text-success", "font-italic");
                document.querySelector("#celVence").classList.add("text-danger", "font-italic");
                if(objData.data.diaPagamento != ""){
                    document.querySelector("#trDiaPagamento").classList.remove('d-none');
                    document.querySelector("#tdDiaPagamento").innerHTML = objData.data.diaPagamento;
                }else{
                    document.querySelector("#trDiaPagamento").classList.add('d-none');
                }
                document.querySelector("#celFecha").innerHTML = objData.data.datecreated;
                document.querySelector("#celVence").innerHTML = objData.data.fechavence;
                document.querySelector("#celNombres").innerHTML = objData.data.nombres;
                document.querySelector("#celNegocio").innerHTML = objData.data.apellidos;
                document.querySelector("#celMonto").innerHTML = objData.data.monto;
                document.querySelector("#celFormato").innerHTML =  objData.data.formato;
                document.querySelector("#celTaza").innerHTML = objData.data.taza;
                document.querySelector("#celPlazo").innerHTML = objData.data.plazo;
                document.querySelector("#celParcela").innerHTML = objData.data.parcela;
                document.querySelector("#celSaldo").innerHTML = objData.data.total;
                document.querySelector("#celPagado").innerHTML = objData.data.pagado;
                document.querySelector("#celPendiente").innerHTML = objData.data.pendiente;
                document.querySelector("#celCancelado").innerHTML = objData.data.cancelado;
                document.querySelector("#idPrestamoP").value = objData.data.idprestamo;
                document.querySelector("#clientePagos").value = objData.data.nombres+' - '+objData.data.apellidos;

                $('#modalViewPrestamo').modal('show');
            }else{
                swal("Error", objData.msg, "error");
            }
        }
        divLoading.style.display = "none";
        return false;
    }
}

function fntRenovarPrestamo(idprestamo, fecha)
{
    let selected = document.querySelector("#listFormato");

    const optionChanged = () => {
        if(selected.value == 1) {
            let checkbox = document.querySelector("#pagamentoSabado").parentElement;
            checkbox.classList.remove('d-none');
        }

        if(selected.value == 2 || selected.value == 3) {
            let checkbox = document.querySelector("#pagamentoSabado").parentElement;
            checkbox.classList.add('d-none');
        }
    }

    selected.addEventListener('change', optionChanged);
    divLoading.style.display = "flex";
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Prestamos/getPrestamo/';
    let formData = new FormData();
    formData.append('idPrestamo',idprestamo);
    formData.append('datefinal',fecha);

    request.open("POST",ajaxUrl,true);
    request.send(formData);
    request.onreadystatechange = function()
    {
        if(request.readyState == 4 && request.status == 200)
        {
            let objData = JSON.parse(request.responseText);
            if(objData.status){
                document.querySelector("#clienteRenovar").innerHTML = objData.data.nombres.toUpperCase() + ' ' + objData.data.apellidos.toUpperCase();
                document.querySelector("#inputClienteRenovar").value = objData.data.personaid;
                $('#modalRenovarPrestamo').modal('show'); 
            }
        }
        divLoading.style.display = "none";
        return false;
    }
}

function listPagos()
{  
    document.querySelector("#tableViewPrestamo").classList.add('d-none');
    document.querySelector("#containerPagos").classList.remove('d-none');
    let idprestamo = document.querySelector("#idPrestamoP").value;
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
                document.querySelector("#listaPagos").innerHTML = trPagos;
            }else{
                document.querySelector("#listaPagos").innerHTML = '<tr><td class"textcenter" colspan="2">No hay datos</td><tr>';
            }
        }
    }
    let clientePagos = document.querySelector("#clientePagos").value;
    document.querySelector("#clientePago").innerHTML = clientePagos;
    document.querySelector("#listaPagos").innerHTML = '<tr><td class"text-center" colspan="2">No hay datos</td><tr>';
}

function backFntViewPrestamo()
{  
    document.querySelector("#tableViewPrestamo").classList.remove('d-none');
    document.querySelector("#containerPagos").classList.add('d-none');
}

function fntPagoPrestamo(idprestamo)
{
    let fechaAnterior = "";
    let txtPago = document.getElementsByClassName("inpPago");
    if(document.querySelector("#fechaAnterior")){
        fechaAnterior = document.querySelector("#fechaAnterior");
    }

    for (var j = 0; j < txtPago.length; j++)
    {
        if(txtPago[j].classList.contains(idprestamo))
        {
            divLoading.style.display = "flex";
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url + '/Prestamos/setPago/';
            let formData = new FormData();

            formData.append('codigoPrestamo',idprestamo);
            formData.append('txtMontoPago',txtPago[j].value);
            if(document.querySelector("#fechaAnterior")){
                formData.append('fechaAnterior',fechaAnterior.value);
            }
            request.open("POST",ajaxUrl,true);
            request.send(formData);
            request.onreadystatechange = function()
            {
                if(request.readyState == 4 && request.status == 200)
                {
                    let objData = JSON.parse(request.responseText);
                    if(objData.status){
                        if(tablePrestamos)
                        {
                            tablePrestamos.ajax.reload(
                                function(){
                                    for (let i = 0; i < tablePrestamos.rows().count(); i++)
                                    {
                                        let row = tablePrestamos.row(i);
                                        let fechaInicio = row.data().datecreated;
                                        let fechaFinal = row.data().datefinal;
                                        let vencimiento = row.data().diasVence;
                                
                                        //console.log(row.data());
                                
                                        if(fechaInicio == formateo)
                                        {
                                            $(row.node()).addClass("table-success");
                                        }
                                        if(fechaFinal == formateo)
                                        {
                                            $(row.node()).addClass("table-dark");
                                        }
                                        if(vencimiento == false)
                                        {
                                            $(row.node()).addClass("table-warning");
                                        }
                                        if(vencimiento == "vencido")
                                        {
                                            $(row.node()).addClass("table-danger");
                                        }
                                        
                                    }
                                }
                            );
                            let btnDelPay;
                            if(objData.total == 0)
                            {
                                btnDelPay = '<p class="text-danger"><b><i>FINALIZADO</i><b/></p><button class="btn btn-danger btn-sm" onclick="fntDelPago('+objData.idpago+')" title="Eliminar pago">'+objData.pago+'</button>';
                            }else{
                                btnDelPay = '<button class="btn btn-success btn-sm" onclick="fntDelPago('+objData.idpago+')" title="Eliminar pago">'+objData.pago+'</button>';
                            }
                            let divDelPay = document.getElementById("div-"+idprestamo);
                            divDelPay.innerHTML = btnDelPay;

                            if(document.getElementById("tot-"+idprestamo))
                            {
                                let pTotal = document.getElementById("tot-"+idprestamo).innerHTML = '<p class="font-weight-bold font-italic text-danger">'+objData.total+'</p>';
                            }
                            fntPayToday();
                        }else{
                            swal({
                                title: "",
                                text: objData.msg,
                                type: "success",
                                confirmButtonText: "Continuar",
                                closeOnConfirm: false,
                            }, function(isConfirm){
                                location.reload();
                            });
                        }
                    }else{
                        swal("Error", objData.msg, "error");
                    }
                } 
                divLoading.style.display = "none";
                return false;  
            }
        }
    }
}

function fntPayAll()
{
    let txtPago = document.getElementsByClassName("inpPago");
    for (let j = 0; j < txtPago.length; j++)
    {
        if(txtPago[j].value != "")
        {
            let pago = {
                'pago': txtPago[j].value,
                'id': txtPago[j].id
            }
            let dados = JSON.stringify(pago);

            divLoading.style.display = "flex";
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url + '/Prestamos/setPayAll/'+dados;
            let formData = new FormData();
            request.open("GET",ajaxUrl,true);
            request.send(formData);
            request.onreadystatechange = function()
            {
                if(request.readyState == 4 && request.status == 200)
                {
                    let objData = JSON.parse(request.responseText);
                    if(objData.status){
                        if(tablePrestamos)
                        {
                            fntPayToday();
                            //window.location = base_url+'/prestamos';
                            tablePrestamos.ajax.reload(
                                function(){
                                    for (let i = 0; i < tablePrestamos.rows().count(); i++)
                                    {
                                        let row = tablePrestamos.row(i);
                                        let fechaInicio = row.data().datecreated;
                                        let fechaFinal = row.data().datefinal;
                                        let vencimiento = row.data().diasVence;
                                
                                        //console.log(row.data());
                                
                                        if(fechaInicio == formateo)
                                        {
                                            $(row.node()).addClass("table-success");
                                        }
                                        if(fechaFinal == formateo)
                                        {
                                            $(row.node()).addClass("table-dark");
                                        }
                                        if(vencimiento == false)
                                        {
                                            $(row.node()).addClass("table-warning");
                                        }
                                        if(vencimiento == "vencido")
                                        {
                                            $(row.node()).addClass("table-danger");
                                        }
                                        
                                    }
                                }
                            );
                        }else{
                            window.location = base_url+'/prestamos';
                        }
                    }else{
                        swal("Error", objData.msg, "error");
                    }
                } 
                divLoading.style.display = "none";
                return false;  
            }
        }
    }
}

function fntDelPrestamo(idprestamo)
{
    swal({
        title: "Eliminar Préstamo",
        text: "¿Realmente quiere eliminar el Préstamo?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function(isConfirm)
    {
        if(isConfirm)
        {
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url+'/Prestamos/delPrestamo';
            let strData = "idPrestamo="+idprestamo;
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
                        swal("Eliminar!", objData.msg , "success");
                        if(tablePrestamos)
                        {
                            tablePrestamos.ajax.reload(
                                function(){
                                    for (let i = 0; i < tablePrestamos.rows().count(); i++)
                                    {
                                        let row = tablePrestamos.row(i);
                                        let fechaInicio = row.data().datecreated;
                                        let fechaFinal = row.data().datefinal;
                                        let vencimiento = row.data().diasVence;
                                
                                        //console.log(row.data());
                                
                                        if(fechaInicio == formateo)
                                        {
                                            $(row.node()).addClass("table-success");
                                        }
                                        if(fechaFinal == formateo)
                                        {
                                            $(row.node()).addClass("table-dark");
                                        }
                                        if(vencimiento == false)
                                        {
                                            $(row.node()).addClass("table-warning");
                                        }
                                        if(vencimiento == "vencido")
                                        {
                                            $(row.node()).addClass("table-danger");
                                        }
                                        
                                    }
                                }
                            );
                            fntSalesToday();
                        }
                    }else{
                        swal("Atención!", objData.msg , "error");
                    }
                }
            }
        }
    });    
}

function fntDelPrestamoAnterior(idprestamo)
{
    swal({
        title: "Eliminar Préstamo",
        text: "¿Realmente quiere eliminar el Préstamo?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function(isConfirm)
    {
        if(isConfirm)
        {
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url+'/Prestamos/delPrestamo';
            let strData = "idPrestamo="+idprestamo;
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
                        swal({
                            title: "",
                            text: objData.msg,
                            type: "success",
                            confirmButtonText: "Continuar",
                            closeOnConfirm: false,
                        }, function(isConfirm){
                            location.reload();
                        });
                    }else{
                        swal("Atención!", objData.msg , "error");
                    }
                }
            }
        }
    });    
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
                        $('#modalFormPrestamosFinalizados').modal('hide');
                        $('#modalViewPrestamo').modal('hide');
                        swal("Eliminar!", objData.msg , "success");
                        if(tablePrestamos)
                        {
                            tablePrestamos.ajax.reload(
                                function(){
                                    for (let i = 0; i < tablePrestamos.rows().count(); i++)
                                    {
                                        let row = tablePrestamos.row(i);
                                        let fechaInicio = row.data().datecreated;
                                        let fechaFinal = row.data().datefinal;
                                        let vencimiento = row.data().diasVence;
                                
                                        //console.log(row.data());
                                
                                        if(fechaInicio == formateo)
                                        {
                                            $(row.node()).addClass("table-success");
                                        }
                                        if(fechaFinal == formateo)
                                        {
                                            $(row.node()).addClass("table-dark");
                                        }
                                        if(vencimiento == false)
                                        {
                                            $(row.node()).addClass("table-warning");
                                        }
                                        if(vencimiento == "vencido")
                                        {
                                            $(row.node()).addClass("table-danger");
                                        }
                                        
                                    }
                                }
                            );
                            fntPayToday();
                        }else{
                            swal({
                                title: "",
                                text: objData.msg,
                                type: "success",
                                confirmButtonText: "Continuar",
                                closeOnConfirm: false,
                            }, function(isConfirm){
                                location.reload();
                            });
                        }
                    }else{
                        swal("Atención!", objData.msg , "error");
                    }
                }
            }
        }
    });
}

function fntDelPagoFinalizado(pagoid)
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
            let ajaxUrl = base_url+'/Prestamos/delPagoAnterior';
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
                        $('#modalFormPrestamosFinalizados').modal('hide');
                        $('#modalViewPrestamo').modal('hide');
                        swal("Eliminar!", objData.msg , "success");
                        if(tablePrestamos)
                        {
                            tablePrestamos.api().ajax.reload();
                        }else{
                            swal({
                                title: "",
                                text: objData.msg,
                                type: "success",
                                confirmButtonText: "Continuar",
                                closeOnConfirm: false,
                            }, function(isConfirm){
                                location.reload();
                            });
                        }
                    }else{
                        swal("Atención!", objData.msg , "error");
                    }
                }
            }
        }
    });
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

function openModalEnrutar()
{
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Prestamos/getListPrestamos';
    request.open("POST",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function()
    {
        if(request.readyState == 4 && request.status == 200)
        {
            let objData = JSON.parse(request.responseText);
            if(objData)
            {
                document.querySelector("#sortable").innerHTML = objData.nombres;
            }else{
                swal("Atención!", objData.msg , "error");
            }
        }
    }  
    $('#sortable').sortable();
    $('#modalFormEnrutar').modal('show');
}

function openModal()
{
    document.querySelector("#divPrestamosFinalizados").classList.add("d-none");
    document.querySelector("#divNuevoPrestamo").classList.remove("d-none"); 
    document.querySelector("#divViewResumen").classList.add('d-none');
    document.querySelector("#btnPayAll").classList.add('d-none');
    if(document.querySelector("#divTablasPrestamos"))
    {
        document.querySelector("#divTablasPrestamos").classList.add('d-none');
    }
    if(document.querySelector("#resumenPendiente"))
    {
        document.querySelector("#resumenPendiente").classList.add('d-none');   
    }
}