<?php 

	//Retorla la url del proyecto
	function base_url()
	{
		return BASE_URL;
	}
    //Retorla la url de Assets
    function media()
    {
        return BASE_URL."/Assets";
    }
    function headerAdmin($data="")
    {
        $view_header = "Views/Template/header_admin.php";
        require_once ($view_header);
    }
    function footerAdmin($data="")
    {
        $view_footer = "Views/Template/footer_admin.php";
        require_once ($view_footer);        
    }
	//Muestra información formateada
	function dep($data)
    {
        $format  = print_r('<pre>');
        $format .= print_r($data);
        $format .= print_r('</pre>');
        return $format;
    }
    function getModal(string $nameModal, $data)
    {
        $view_modal = "Views/Template/Modals/{$nameModal}.php";
        require_once $view_modal;        
    }

    function getFile(string $url, $data)
    {
        ob_start();
        require_once("Views/{$url}.php");
        $file = ob_get_clean();
        return $file;
    }

    //Envío de correos
    function sendEmail($data,$template)
    {
        $asunto = $data['asunto'];
        $emailDestino = $data['email'];
        $empresa = NOMBRE_REMITENTE;
        $remitente = EMAIL_REMITENTE;
        //ENVÍO DE CORREO
        $de = "MIME-Version: 1.0\r\n";
        $de.= "Content-type: text/html; charset=UTF-8\r\n";
        $de.= "From: {$empresa} <{$remitente}>\r\n";
        ob_start();
        require_once("Views/Template/Email/".$template.".php");
        $mensaje = ob_get_clean();
        $send = mail($emailDestino, $asunto, $mensaje, $de);
        return $send;
    }

    function getPermisos(int $idmodulo)
    {
        require_once("Models/PermisosModel.php");
        $objPermisos = new PermisosModel();
        $idrol = $_SESSION['userData']['idrol'];
        $arrPermisos = $objPermisos->permisosModulo($idrol);
        $permisos = '';
        $permisosMod = '';

        if(count($arrPermisos) > 0){
            $permisos = $arrPermisos;
            $permisosMod = isset($arrPermisos[$idmodulo]) ? $arrPermisos[$idmodulo] : "";
        }
        $_SESSION['permisos'] = $permisos;
        $_SESSION['permisosMod'] = $permisosMod;
    }

    function sessionUser(int $idpersona){
        require_once ("Models/LoginModel.php");
        $objLogin = new LoginModel();
        $request = $objLogin->sessionLogin($idpersona);
        return $request;
    }

    function sessionStart(){
        session_start();
        $inactive = 300;
        if(isset($_SESSION['timeout'])){
            $session_in = time() - $_SESSION['inicio'];
            if($session_in > $inactive){
                header("Location: ".BASE_URL."/logout");
            }
        }else{
            header("Location: ".BASE_URL."/logout");
        }
    }

    function forClientesPagos(string $fecha)
    {
        require_once("Models/PrestamosModel.php");
        $objPrestamos = new PrestamosModel();
        $request = $objPrestamos->selectClientesPagos($fecha);
        $pagos = "";
        for ($i=0; $i < count($request); $i++) { 
            $pagos .= strtoupper($request[$i]['nombres']).' = './*SMONEY.*/$request[$i]['abono'].'<br>';
        }
        return $pagos;
    }

    function forClientesVentas(string $fecha)
    {
        require_once("Models/PrestamosModel.php");
        $objPrestamos = new PrestamosModel();
        $request = $objPrestamos->selectClientesVentas($fecha);
        $ventas = "";
        for ($i=0; $i < count($request); $i++) { 
            $ventas .= strtoupper($request[$i]['nombres']).' = '/*.SMONEY*/.$request[$i]['monto'].'<br>';
        }
        return $ventas;
    }

    function forVentasResumen(string $fecha)
    {
        require_once("Models/PrestamosModel.php");
        $objPrestamos = new PrestamosModel();
        $request = $objPrestamos->selectVentas();
        //dep($request);exit;
        $ventas = "";
        for ($i=0; $i < count($request); $i++) { 
            if($request[$i]['datecreated'] == $fecha){
                $ventas .= strtoupper($request[$i]['nombres']).' = '/*.SMONEY*/.$request[$i]['monto'].'<br>';
            }
        }
        return $ventas;
    }

    function forGastoResumen(string $fecha)
    {
        require_once("Models/PrestamosModel.php");
        $objPrestamos = new PrestamosModel();
        $request = $objPrestamos->selectGastos($fecha);
        if(is_array($request))
        {
            $gasto = "";
            for ($i=0; $i < count($request); $i++) {
                if($request[$i]['nombre'] != "")
                {       
                    $gasto .= strtoupper($request[$i]['nombre']).' = '/*.SMONEY*/.$request[$i]['monto'].'<br>';
                }
            }
            return $gasto;
        }
    }

    function ultimoResumen()
    {
        require_once("Models/PrestamosModel.php");
        $objPrestamos = new PrestamosModel();
        $request = $objPrestamos->selectResumen();
        return $request;
        
        
    }

    function generar_codigo_aleatorio(string $letra, int $longitud, int $numero)
    {
        for ($i=1; $i<=$longitud ; $i++) { 
            $aleatorio = rand(0,9);
            $letra.=$aleatorio;
        }
        return $letra."-".$numero;
    }

    //Elimina exceso de espacios entre palabras
    function strClean($strCadena){
        $string = preg_replace(['/\s+/','/^\s|\s$/'],[' ',''], $strCadena);
        $string = trim($string); //Elimina espacios en blanco al inicio y al final
        $string = stripslashes($string); // Elimina las \ invertidas
        $string = str_ireplace("<script>","",$string);
        $string = str_ireplace("</script>","",$string);
        $string = str_ireplace("<script src>","",$string);
        $string = str_ireplace("<script type=>","",$string);
        $string = str_ireplace("SELECT * FROM","",$string);
        $string = str_ireplace("DELETE FROM","",$string);
        $string = str_ireplace("INSERT INTO","",$string);
        $string = str_ireplace("SELECT COUNT(*) FROM","",$string);
        $string = str_ireplace("DROP TABLE","",$string);
        $string = str_ireplace("OR '1'='1","",$string);
        $string = str_ireplace('OR "1"="1"',"",$string);
        $string = str_ireplace('OR ´1´=´1´',"",$string);
        $string = str_ireplace("is NULL; --","",$string);
        $string = str_ireplace("is NULL; --","",$string);
        $string = str_ireplace("LIKE '","",$string);
        $string = str_ireplace('LIKE "',"",$string);
        $string = str_ireplace("LIKE ´","",$string);
        $string = str_ireplace("OR 'a'='a","",$string);
        $string = str_ireplace('OR "a"="a',"",$string);
        $string = str_ireplace("OR ´a´=´a","",$string);
        $string = str_ireplace("OR ´a´=´a","",$string);
        $string = str_ireplace("--","",$string);
        $string = str_ireplace("^","",$string);
        $string = str_ireplace("[","",$string);
        $string = str_ireplace("]","",$string);
        $string = str_ireplace("==","",$string);
        return $string;
    }
    //Genera una contraseña de 10 caracteres
	function passGenerator($length = 10)
    {
        $pass = "";
        $longitudPass=$length;
        $cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $longitudCadena=strlen($cadena);

        for($i=1; $i<=$longitudPass; $i++)
        {
            $pos = rand(0,$longitudCadena-1);
            $pass .= substr($cadena,$pos,1);
        }
        return $pass;
    }
    //Genera un token
    function token()
    {
        $r1 = bin2hex(random_bytes(10));
        $r2 = bin2hex(random_bytes(10));
        $r3 = bin2hex(random_bytes(10));
        $r4 = bin2hex(random_bytes(10));
        $token = $r1.'-'.$r2.'-'.$r3.'-'.$r4;
        return $token;
    }
    //Formato para valores monetarios
    function formatMoney($cantidad){
        $cantidad = number_format($cantidad,2,SPD,SPM);
        return $cantidad;
    }

    function Meses()
    {
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        return $meses;
    }

    //Fecha formateada en linea recta
    function fechaInline(string $fecha) 
    {
        $fechaFormateada = explode("-", $fecha);
        $fechaFormateada = '<div class="d-flex justify-content-center">'.'<div>'.$fechaFormateada[0].'</div>-<div>'.$fechaFormateada[1].'</div>-<div>'.$fechaFormateada[2].'</div></div>';

        return $fechaFormateada;
    }
    

 ?>