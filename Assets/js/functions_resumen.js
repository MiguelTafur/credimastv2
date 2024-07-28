document.addEventListener('DOMContentLoaded', function()
{
    //NUEVA BASE
    if(document.querySelector("#formBase"))
    {
        let formBase = document.querySelector("#formBase");
        formBase.onsubmit = function(e){
            e.preventDefault();

            let intBase = document.querySelector('#txtBase').value;
            //let strObservacion = document.querySelector('#txtObservacion').value;

            if(intBase == ""){
                swal("Atención", "Debes ingresar una base.", "error");
                return false;
            }

            let ElementsValid = document.getElementsByClassName("valid");
            for (let i = 0; i < ElementsValid.length; i++) {
                if(ElementsValid[i].classList.contains('is-invalid')){
                    swal("Atencion!", "Base incorrecta.", "error");
                    return false;
                }
            }

            divLoading.style.display = "flex";
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url+'/Resumen/setBase';
            let formData = new FormData(formBase);
            request.open("POST",ajaxUrl,true);
            request.send(formData);

            request.onreadystatechange = function()
            {
                if(request.readyState == 4 && request.status == 200)
                {
                    let objData = JSON.parse(request.responseText);
                    if(objData.status){
                        if(document.querySelector("#celAddBase")){

                            document.querySelector("#celAddBase").classList.add("d-none");
                        }
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
                        swal("Error", objData.msg, "error");
                    }
                } 
                divLoading.style.display = "none";
                return false;   
            }
        }
    }

    //NUEVO GASTO
    if(document.querySelector("#formGasto"))
    {
        let formGasto = document.querySelector("#formGasto");
        formGasto.onsubmit = function(e){
            e.preventDefault();

            let intGasto = document.querySelector('#txtGasto').value;
            let strObservacion = document.querySelector('#txtObservacion').value;

            if(intGasto == ""){
                swal("Atención", "Debes ingresar un gasto.", "error");
                return false;
            }

            let ElementsValid = document.getElementsByClassName("valid");
            for (let i = 0; i < ElementsValid.length; i++) {
                if(ElementsValid[i].classList.contains('is-invalid')){
                    swal("Atencion!", "Solo puedes ingresar números.", "error");
                    return false;
                }
            }

            divLoading.style.display = "flex";
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url+'/Resumen/setGasto';
            let formData = new FormData(formGasto);
            request.open("POST",ajaxUrl,true);
            request.send(formData);

            request.onreadystatechange = function()
            {
                if(request.readyState == 4 && request.status == 200)
                {
                    let objData = JSON.parse(request.responseText);
                    if(objData.status){
                        
                        if(document.querySelector("#fechaAnterior"))
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
                        }
                        fntViewResumen();
                        $('#modalFormGasto').modal('hide');
                        formGasto.reset();
                    }else{
                        swal("Error", objData.msg, "error");
                    }
                } 
                divLoading.style.display = "none";
                return false;   
            }
        }
    }

    //NUEVO RESUMEN
    if(document.querySelector("#formResumen"))
    {
        let formResumen = document.querySelector("#formResumen");
        formResumen.onsubmit = function(e){
            e.preventDefault();

            divLoading.style.display = "flex";
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url+'/Resumen/setResumen';
            let formData = new FormData(formResumen);
            request.open("POST",ajaxUrl,true);
            request.send(formData);

            request.onreadystatechange = function()
            {
                if(request.readyState == 4 && request.status == 200)
                {
                    let objData = JSON.parse(request.responseText);
                    if(objData.status){
                        swal({
                            title: "",
                            text: objData.msg,
                            type: "success",
                            confirmButtonText: "Continuar",
                            closeOnConfirm: false,
                        }, function(isConfirm){
                            window.location = base_url+'/dashboard';
                        });
                    }else{
                        swal("Error", objData.msg, "error");
                    }
                } 
                divLoading.style.display = "none";
                return false;   
            }
        }
    }

    //RESUMEN ANTERIOR
    if(document.querySelector("#formResumenAnterior"))
    {
        let formResumenAnterior = document.querySelector("#formResumenAnterior");
        formResumenAnterior.onsubmit = function(e){
            e.preventDefault();

            divLoading.style.display = "flex";
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url+'/Resumen/setResumenAnterior';
            let formData = new FormData(formResumenAnterior);
            request.open("POST",ajaxUrl,true);
            request.send(formData);

            request.onreadystatechange = function()
            {
                if(request.readyState == 4 && request.status == 200)
                {
                    let objData = JSON.parse(request.responseText);
                    if(objData.status){
                        //fntViewResumen();
                        swal({
                            title: "",
                            text: objData.msg,
                            type: "success",
                            confirmButtonText: "Continuar",
                            closeOnConfirm: false,
                        }, function(isConfirm){
                            window.location = base_url+'/prestamos';
                        });
                    }else{
                        swal("Error", objData.msg, "error");
                    }
                } 
                divLoading.style.display = "none";
                return false;   
            }
        }
    }

    fntViewResumen();

}, false);

function fntViewResumen()
{
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Resumen/getResumen';
    request.open("POST",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function()
    {
        if(request.readyState == 4 && request.status == 200)
        {
            let objData = JSON.parse(request.responseText);
            console.log(objData);
            if(objData)
            {
                //BASE
                let idbaseJ = objData.idbase;
                let baseJ = objData.base;
                
                if(idbaseJ > 0)
                {
                    if(document.querySelector("#celDelBase")){
                        document.querySelector("#celDelBase").classList.remove("d-none");
                    }
                    if(document.querySelector("#celInfoBase")){
                        document.querySelector("#celInfoBase").classList.remove("d-none");
                    }
                    if(document.querySelector("#celBase")){
                        document.querySelector("#celBase").innerHTML = /*smoney+" "+*/baseJ;
                    }
                    document.querySelector("#idBase").value = objData.idbase;
                }else{
                    if(document.querySelector("#celBase"))
                    {
                        document.querySelector("#celBase").innerHTML = /*smoney+" "+*/baseJ;
                    }
                    if(document.querySelector("#celAddBase"))
                    {
                        document.querySelector("#celAddBase").classList.remove("d-none");
                    }
                    if (document.querySelector("#baseAnterior")) {
                        document.querySelector("#baseAnterior").value = baseJ;
                    }else{
                        if(document.querySelector("#baseAnterior")){
                            document.querySelector("#baseAnterior").value = "0";
                        }
                    }

                    if (document.querySelector("#baseAnteriorRA")) {
                        document.querySelector("#baseAnteriorRA").value = baseJ;
                    }else{
                        if(document.querySelector("#baseAnteriorRA")){
                            document.querySelector("#baseAnteriorRA").value = "0";
                        }
                    }
                }

                //GASTO
                let gastosJ = objData.gastos;
                let idgasto = objData.idGasto;
                let delGastosJ = objData.delGastos;
                if(gastosJ >= 1){
                    //console.log(gastosJ);
                    if(document.querySelector("#celGastos")){
                        document.querySelector("#celGastos").innerHTML = /*smoney+" "+*/gastosJ;  
                    document.querySelector("#gastos").value = gastosJ;
                    document.querySelector("#idGasto").value = idgasto;  
                    document.querySelector("#celDelGastos").classList.remove("d-none");
                    document.querySelector("#d-delGastos").innerHTML = delGastosJ;
                    }
                }else{
                    if(document.querySelector("#celGastos")){
                        document.querySelector("#celGastos").innerHTML = /*smoney+" "+*/"0";  
                    }
                    document.querySelector("#celDelGastos").classList.add("d-none");
                }

                //COBRADO
                let cobradoJ = objData.cobrado;
                if(cobradoJ > 0){
                    if(document.querySelector("#celCobrado")){
                        document.querySelector("#celCobrado").innerHTML = /*smoney+" "+*/objData.cobrado;
                    }

                    document.querySelector("#cobrado").value = cobradoJ;  

                    if(document.querySelector("#celInfoCobrado")){
                        document.querySelector("#celInfoCobrado").classList.remove("d-none");
                    }
                }else{
                    if(document.querySelector("#celCobrado")){
                        document.querySelector("#celCobrado").innerHTML = /*smoney+" "+*/" 0";
                    }
                }

                //VENTAS
                let ventasJ = objData.ventas;
                if(ventasJ > 0){
                    document.querySelector("#celVentas").innerHTML = /*smoney+" "+*/ventasJ;
                    document.querySelector("#ventas").value = ventasJ;  
                    document.querySelector("#celInfoVentas").classList.remove("d-none"); 
                }

                //TOTAL
                let totalJ = objData.total;
                if(document.querySelector("#celTotal"))
                {
                    document.querySelector("#celTotal").innerHTML = /*smoney+" "+*/totalJ;
                    document.querySelector("#total").value = totalJ;   
                }

                //RESUMEN
                let resumenJ = objData.idresumen;
                
                if(resumenJ > 0){
                    //console.log(objData.gastos);
                    if(document.querySelector("#card")){
                        document.querySelector("#card").classList.add('d-none');
                    }

                    if(document.querySelector("#resumenCerrado")){
                        document.querySelector("#resumenCerrado").classList.remove('d-none');
                    }

                    if(document.querySelector("#spanBase")){
                        document.querySelector("#spanBase").innerHTML = baseJ;
                    }

                    if(document.querySelector("#spanCobrado")){
                        document.querySelector("#spanCobrado").innerHTML = cobradoJ;
                    }

                    if(document.querySelector("#spanVentas")){
                        document.querySelector("#spanVentas").innerHTML = ventasJ;
                    }

                    if(document.querySelector("#spanGastos")){
                        document.querySelector("#spanGastos").innerHTML = gastosJ;
                    }

                    if(document.querySelector("#spanTotal")){
                        if(totalJ < 0){
                            document.querySelector("#spanTotal").classList.add('badge-danger');
                        }else{
                            document.querySelector("#spanTotal").classList.add('badge-success');
                        }
                        document.querySelector("#spanTotal").innerHTML = totalJ;
                    }

                    document.querySelector("#idResumen").value = resumenJ;
                    
                }else{
                    if(document.querySelector("#card")){
                        document.querySelector("#card").classList.remove('d-none');
                    }
                    if(document.querySelector("#resumenCerrado")){
                        document.querySelector("#resumenCerrado").classList.add('d-none');
                    }
                }
                
                $(function(){
                    $('.infoBase').popover({
                        title: "BASE ACTUALIZADA",
                        content: "Base Anterior:"+" "/*smoney+" "+*/+objData.base2,
                        html: true,
                        placement: "right",
                        trigger: "focus",
                        animation: true
                    });
                    $('.infoCobrado').popover({
                        title: "PAGOS",
                        content: objData.cliente,
                        html: true,
                        placement: "right",
                        trigger: "focus",
                        animation: true
                    });
                    $('.infoGastos').popover({
                        title: "GASTOS",
                        content: objData.nombreGasto+' = '/*smoney+" "+*/+objData.gasto,
                        trigger: "focus",
                        animation: true
                    });
                    $('.infoVentas').popover({
                        title: "VENTAS",
                        content: objData.ventasC,
                        html: true,
                        trigger: "focus",
                        animation: true
                    });
                });
                
            }else{
                swal("Error", objData.msg, "error");
            }

        }
    }
}

function fntDelBase()
{
    swal({
        title: "Eliminar Base",
        text: "¿Realmente quiere eliminar la Base?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function(isConfirm){
        if(isConfirm)
        {
            let baseId = document.querySelector("#idBase").value;
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url+'/Resumen/delBase';
            let strData = "baseId="+baseId;
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
                        if(document.querySelector("#celDelBase")){
                            document.querySelector("#celDelBase").classList.add("d-none");
                        }
                        if(document.querySelector("#celInfoBase")){
                            document.querySelector("#celInfoBase").classList.add("d-none");
                        }
                        if(document.querySelector("#celAddBase")){
                            document.querySelector("#celAddBase").classList.remove("d-none");
                        }
                        swal("Eliminar!", objData.msg , "success");
                        // if(document.querySelector("#celDelBaseAnterior"))
                        // {
                            swal({
                                title: "",
                                text: objData.msg,
                                type: "success",
                                confirmButtonText: "Continuar",
                                closeOnConfirm: false,
                            }, function(isConfirm){
                                location.reload();
                            });
                        // }
                        //fntViewResumen();
                    }else{
                        swal("Atención!", objData.msg , "error");
                    }
                }
            }
        }
    });
}

function fntDelGasto(idgasto)
{
    swal({
        title: "Eliminar Gasto",
        text: "¿Realmente quiere eliminar el Gasto?",
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
            let ajaxUrl = base_url+'/Resumen/delGasto';
            let strData = "gastoId="+idgasto;
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
                        // if(document.querySelector("#celDelGastosAnterior"))
                        // {
                            swal({
                                title: "",
                                text: objData.msg,
                                type: "success",
                                confirmButtonText: "Continuar",
                                closeOnConfirm: false,
                            }, function(isConfirm){
                                location.reload();
                            });
                        // }
                        // fntViewResumen();
                    }else{
                        swal("Atención!", objData.msg , "error");
                    }
                }
            }
        }
    });
}

function fntDelResumen()
{
    swal({
        title: "Eliminar Resumen",
        text: "Si ingresaste base o gastos, también se eliminarán!",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function(isConfirm){
        if(isConfirm)
        {
            let resumenId = document.querySelector("#idResumen").value;
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url+'/Resumen/delResumen';
            let strData = "resumenId="+resumenId;
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

function modalBase()
{  
    $('#modalFormBase').modal('show');
}
function modalGasto()
{  
    $('#modalFormGasto').modal('show');
}