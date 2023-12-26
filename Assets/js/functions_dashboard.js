let divLoading = document.querySelector("#divLoading");

//swal("Pagamento dia 30", "Recuerda efectuar el pagamento hasta el dia 30", "warning");

$('.date-picker').datepicker( {
    closeText: 'Cerrar',
    prevText: '<Ant',
    nextText: 'Sig>',
    currentText: 'Hoy',
    monthNames: ['1 -', '2 -', '3 -', '4 -', '5 -', '6 -', '7 -', '8 -', '9 -', '10 -', '11 -', '12 -'],
    monthNamesShort: ['Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre'],
    changeMonth: true,
    changeYear: true,
    showButtonPanel: true,
    dateFormat: 'MM yy',
    showDays: false,
    onClose: function(dateText, inst) {
        $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
    }
});

function fntViewPrestamo(idprestamo)
{
    divLoading.style.display = "flex";
    document.querySelector('#titleModal').innerHTML = "Datos de Préstamo";
    document.querySelector("#tableViewPrestamo").classList.remove('d-none');
    document.querySelector("#containerPagos").classList.add('d-none');
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Prestamos/getPrestamo/'+idprestamo;
    request.open("GET",ajaxUrl,true);
    request.send();
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
                document.querySelector("#celMonto").innerHTML = smoney + objData.data.monto;
                document.querySelector("#celFormato").innerHTML =  objData.data.formato;
                document.querySelector("#celTaza").innerHTML = objData.data.taza;
                document.querySelector("#celPlazo").innerHTML = objData.data.plazo;
                document.querySelector("#celParcela").innerHTML = smoney + objData.data.parcela;
                document.querySelector("#celSaldo").innerHTML = smoney + objData.data.total;
                document.querySelector("#celPagado").innerHTML = smoney + objData.data.pagado;
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
function fntListPrestamosFinalizados()
{  
    $('#modalFormPrestamosFinalizados').modal('show');
    tablePrestamosFinalizados = $('#tablePrestamosFinalizados').dataTable( 
    {
        "aProcessing":true,
        "aServerSide":true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "ajax":{
            "url": " "+base_url+"/Prestamos/getPrestamosFinalizados",
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
        "iDisplayLength": 5,
        "order":[[1,"desc"]]  
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
    /*let clientePagosFinalizados = nombres;
    document.querySelector("#clientePagoFinalizado").innerHTML = clientePagosFinalizados;*/
    $('#modalFormPagosFinalizados').modal('show');
    //document.querySelector("#listaPagosFinalizados").innerHTML = '<tr><td class"text-center" colspan="2">No hay datos</td><tr>';
}
function fntViewPrestamoFinalizado(idprestamo)
{
    document.querySelector(".AF").classList.add("d-none");
    document.querySelector(".PF").classList.remove("d-none");
    document.querySelector("#idPagosFinalizados").classList.add("d-none");
    document.querySelector("#tableViewPrestamoFinalizado").classList.remove('d-none');

    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Prestamos/getPrestamoFinalizado/'+idprestamo;
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

function fntSearchVMes()
{
    let fecha = document.querySelector(".ventasMes").value;
    if(fecha == "")
    {
        swal("", "Seleccione mes y año", "error");
        return false;
    }
    divLoading.style.display = "flex";
    let  request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let  ajaxUrl = base_url+'/Dashboard/ventasMes'; 
    let  formData = new FormData();
    formData.append('fecha', fecha);
    request.open("POST",ajaxUrl,true);
    request.send(formData);
    request.onreadystatechange = function()
    {
        if(request.readyState != 4) return;
        if(request.status == 200)
        {
            $("#graficaMes").html(request.responseText);
            divLoading.style.display = "none";
            return false;
        }
    }
}

function fntSearchCMes()
{
    let fecha = document.querySelector(".cobradoMes").value;
    if(fecha == "")
    {
        swal("", "Seleccione mes y año", "error");
        return false;
    }
    divLoading.style.display = "flex";
    let  request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let  ajaxUrl = base_url+'/Dashboard/cobradoMes'; 
    let  formData = new FormData();
    formData.append('fecha', fecha);
    request.open("POST",ajaxUrl,true);
    request.send(formData);
    request.onreadystatechange = function()
    {
        if(request.readyState != 4) return;
        if(request.status == 200)
        {
            $("#graficaMesCobrado").html(request.responseText);
            divLoading.style.display = "none";
            return false;
        }
    }
}

function fntSearchGMes()
{
    let fecha = document.querySelector(".gastosMes").value;
    if(fecha == "")
    {
        swal("", "Seleccione mes y año", "error");
        return false;
    }
    divLoading.style.display = "flex";
    let  request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let  ajaxUrl = base_url+'/Dashboard/gastosMes'; 
    let  formData = new FormData();
    formData.append('fecha', fecha);
    request.open("POST",ajaxUrl,true);
    request.send(formData);
    request.onreadystatechange = function()
    {
        if(request.readyState != 4) return;
        if(request.status == 200)
        {
            $("#graficaMesGastos").html(request.responseText);
            divLoading.style.display = "none";
            return false;
        }
    }
}

function fntViewDetalleC()
{
    $('#modalDetalleC').modal('show');
    document.querySelector("#divCobradoD").classList.add("d-none");
    $('#fechaCobrado').daterangepicker({
        "autoUpdateInput": false,
        "locale": {
            "format": "DD/MM/YYYY",
            "separator": " - ",
            "applyLabel": "Aplicar",
            "cancelLabel": "Cancelar",
            "daysOfWeek": [
                "Dom",
                "Seg",
                "Ter",
                "Qua",
                "Qui",
                "Sex",
                "Sab"
            ],
            "monthNames": [
                "Janeiro",
                "Fevereiro",
                "Março",
                "Abil",
                "Maio",
                "Junho",
                "Julho",
                "Agosto",
                "Setembro",
                "Outubro",
                "Novembro",
                "Dezembro"
            ],
            "firstDay": 1
        }
    });

    $('#fechaCobrado').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
    });
  
    $('#fechaCobrado').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
}

function fntViewDetalleV()
{
    $('#modalDetalleV').modal('show');
    document.querySelector("#divVentasD").classList.add("d-none");
    $('#fechaVentas').daterangepicker({
        "autoUpdateInput": false,
        "locale": {
            "format": "DD/MM/YYYY",
            "separator": " - ",
            "applyLabel": "Aplicar",
            "cancelLabel": "Cancelar",
            "daysOfWeek": [
                "Dom",
                "Seg",
                "Ter",
                "Qua",
                "Qui",
                "Sex",
                "Sab"
            ],
            "monthNames": [
                "Janeiro",
                "Fevereiro",
                "Março",
                "Abil",
                "Maio",
                "Junho",
                "Julho",
                "Agosto",
                "Setembro",
                "Outubro",
                "Novembro",
                "Dezembro"
            ],
            "firstDay": 1
        }
    });

    $('#fechaVentas').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
    });
  
    $('#fechaVentas').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
}

function fntViewDetalleG()
{
    $('#modalDetalleG').modal('show');
    document.querySelector("#divGastosD").classList.add("d-none");
    $('#fechaGastos').daterangepicker({
        "autoUpdateInput": false,
        "locale": {
            "format": "DD/MM/YYYY",
            "separator": " - ",
            "applyLabel": "Aplicar",
            "cancelLabel": "Cancelar",
            "daysOfWeek": [
                "Dom",
                "Seg",
                "Ter",
                "Qua",
                "Qui",
                "Sex",
                "Sab"
            ],
            "monthNames": [
                "Janeiro",
                "Fevereiro",
                "Março",
                "Abil",
                "Maio",
                "Junho",
                "Julho",
                "Agosto",
                "Setembro",
                "Outubro",
                "Novembro",
                "Dezembro"
            ],
            "firstDay": 1
        }
    });

    $('#fechaGastos').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
    });
  
    $('#fechaGastos').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
}

function fntSearchCobradoD()
{
    let fecha = document.querySelector("#fechaCobrado").value;
    if(fecha == "")
    {
        swal("", "Seleccione la fecha", "error");
        return false;
    }

    divLoading.style.display = "flex";
    let  request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let  ajaxUrl = base_url+'/Dashboard/getCobradoD'; 
    let  formData = new FormData();
    formData.append('fecha', fecha);
    request.open("POST",ajaxUrl,true);
    request.send(formData);
    request.onreadystatechange = function()
    {
        if(request.readyState != 4) return;
        if(request.status == 200)
        {
            let objData = JSON.parse(request.responseText);
            arrCobrado = objData.cobradoD;
            totalC = objData.totalCobrado;

            $(function () {
                $('[data-toggle="popover"]').popover({
                    container: "body",
                    trigger: "focus",
                    html: true
                })
              })

            document.querySelector("#datosCobradoD").innerHTML = arrCobrado;
            document.querySelector("#markCobrado").innerHTML = smoney + ' ' + totalC;
            document.querySelector("#divCobradoD").classList.remove("d-none");
            
        }

        divLoading.style.display = "none";
        return false;
    }
}

function fntSearchVentasD()
{
    let fecha = document.querySelector("#fechaVentas").value;
    if(fecha == "")
    {
        swal("Error", "Seleccione la fecha", "error");
        return false;
    }

    divLoading.style.display = "flex";
    let  request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let  ajaxUrl = base_url+'/Dashboard/getVentasD'; 
    let  formData = new FormData();
    formData.append('fecha', fecha);
    request.open("POST",ajaxUrl,true);
    request.send(formData);
    request.onreadystatechange = function()
    {
        if(request.readyState != 4) return;
        if(request.status == 200)
        {
            let objData = JSON.parse(request.responseText);
            arrVentas = objData.ventasD;
            totalV = objData.totalVentas;

            $(function () {
                $('[data-toggle="popover"]').popover({
                    container: "body",
                    trigger: "focus",
                    html: true
                })
              });

            document.querySelector("#datosVentasD").innerHTML = arrVentas;
            document.querySelector("#markVentas").innerHTML = smoney + ' ' + totalV;
            document.querySelector("#divVentasD").classList.remove("d-none");
            
        }

        divLoading.style.display = "none";
        return false;
   }
}

function fntSearchGastosD()
{
    let fecha = document.querySelector("#fechaGastos").value;
    if(fecha == "")
    {
        swal("Error", "Seleccione la fecha", "error");
        return false;
    }

    divLoading.style.display = "flex";
    let  request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let  ajaxUrl = base_url+'/Dashboard/getGastosD'; 
    let  formData = new FormData();
    formData.append('fecha', fecha);
    request.open("POST",ajaxUrl,true);
    request.send(formData);
    request.onreadystatechange = function()
    {
        if(request.readyState != 4) return;
        if(request.status == 200)
        {
            let objData = JSON.parse(request.responseText);
            arrGastos = objData.gastosD;
            totalG = objData.totalGastos;

            $(function () {
                $('[data-toggle="popover"]').popover({
                    container: "body",
                    trigger: "focus",
                    html: true
                })
              });

            document.querySelector("#datosGastosD").innerHTML = arrGastos;
            document.querySelector("#markGastos").innerHTML = smoney + ' ' + totalG;
            document.querySelector("#divGastosD").classList.remove("d-none");
            
        }

        divLoading.style.display = "none";
        return false;
   }
}

function fntSearchVAnio(){
    let anio = document.querySelector(".ventasAnio").value;
    if(anio == ""){
        swal("", "Ingrese año " , "error");
        return false;
    }else{
        let request = (window.XMLHttpRequest) ? 
            new XMLHttpRequest() : 
            new ActiveXObject('Microsoft.XMLHTTP');
        let ajaxUrl = base_url+'/Dashboard/ventasAnio';
        divLoading.style.display = "flex";
        let formData = new FormData();
        formData.append('anio',anio);
        request.open("POST",ajaxUrl,true);
        request.send(formData);
        request.onreadystatechange = function(){
            if(request.readyState != 4) return;
            if(request.status == 200){
                $("#graficaAnio").html(request.responseText);
                divLoading.style.display = "none";
                return false;
            }
        }
    }
}

function fntSearchCAnio(){
    let anio = document.querySelector(".cobradoAnio").value;
    if(anio == ""){
        swal("", "Ingrese año " , "error");
        return false;
    }else{
        let request = (window.XMLHttpRequest) ? 
            new XMLHttpRequest() : 
            new ActiveXObject('Microsoft.XMLHTTP');
        let ajaxUrl = base_url+'/Dashboard/cobradoAnio';
        divLoading.style.display = "flex";
        let formData = new FormData();
        formData.append('anio',anio);
        request.open("POST",ajaxUrl,true);
        request.send(formData);
        request.onreadystatechange = function(){
            if(request.readyState != 4) return;
            if(request.status == 200){
                $("#graficaCAnio").html(request.responseText);
                divLoading.style.display = "none";
                return false;
            }
        }
    }
}

function fntSearchGAnio(){
    let anio = document.querySelector(".gastosAnio").value;
    if(anio == ""){
        swal("", "Ingrese año " , "error");
        return false;
    }else{
        let request = (window.XMLHttpRequest) ? 
            new XMLHttpRequest() : 
            new ActiveXObject('Microsoft.XMLHTTP');
        let ajaxUrl = base_url+'/Dashboard/gastosAnio';
        divLoading.style.display = "flex";
        let formData = new FormData();
        formData.append('anio',anio);
        request.open("POST",ajaxUrl,true);
        request.send(formData);
        request.onreadystatechange = function(){
            if(request.readyState != 4) return;
            if(request.status == 200){
                $("#graficaGAnio").html(request.responseText);
                divLoading.style.display = "none";
                return false;
            }
        }
    }
}