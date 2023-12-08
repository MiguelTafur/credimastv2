<?php 

	class Ventas extends Controllers
	{
		public function __construct()
		{
			parent::__construct();
			session_start();
			if(empty($_SESSION['login'])){
				header('Location: '.base_url().'/login');
			}
			getPermisos(MPRESTAMOS);
		}

		public function Ventas()
		{
			if(empty($_SESSION['permisosMod']['r'])){
				header("Location: ".base_url().'/prestamos');
			}
			$data['page_tag'] = "Ventas";
			$data['page_title'] = "VENTAS";
			$data['page_name'] = "ventas";
			$data['pagamentos'] = $this->model->selectDatePagoPrestamo();
			$data['page_functions_js'] = "functions_ventas.js";
			$this->views->getView($this,"ventas",$data);
		}

		public function setPrestamo()
		{
			if($_POST){
				//dep($_POST);exit;
				if(empty($_POST['listClientId']) || empty($_POST['txtMonto']) || empty($_POST['txtTaza']) || empty($_POST['txtPlazo']) || empty($_POST['listFormato']))
				{
					$arrResponse = array("status" => false, "msg" => "Datos incorrectos.");
				}else{
					$intClienteId = intval($_POST['listClientId']);
					$intMonto = intval($_POST['txtMonto']);
					$intTaza = intval($_POST['txtTaza']);
					$intPlazo = intval($_POST['txtPlazo']);
					$intFormato = intval($_POST['listFormato']);
					$strObservacion = strClean($_POST['txtObservacion']);
					$contadorPlazo = 0;
					if(!empty($_POST['fechaAnterior']))
					{
						$fecha_actual = $_POST['fechaAnterior'];
					}else{
						$fecha_actual = date("Y-m-d");
					}
					
					//Calculando el vencimiento del crédito
					$fechaEnSegundos = strtotime($fecha_actual);
					$dia = 86400;
					$contador = 1;

					//DIARIO
					if($intFormato == 1)
					{
						while($contador <= $intPlazo)
						{
							if(date("N", $fechaEnSegundos) == 7)
							{
								$fechaEnSegundos += $dia;
							}else{
								$fechaEnSegundos += $dia;
								$contador += 1;
							}
						}	
					//SEMANAL
					}
					if($intFormato == 2)
					{
						$contadorPlazo = $intPlazo * 6;
						while($contador <= $contadorPlazo)
						{
							if(date("N", $fechaEnSegundos) == 7)
							{
								$fechaEnSegundos += $dia;
							}else{
								$fechaEnSegundos += $dia;
								$contador += 1;
							}
						}
						//dep($intPlazo);exit;
					//MES		
					}
					if($intFormato == 3){
						$contadorPlazo = $intPlazo * 6;
						while($contador <= $contadorPlazo)
						{
							$fechaEnSegundos += $dia;
							$contador += 1;
						}	
					}	
					
					$fechaFinal = date('Y-m-d' , $fechaEnSegundos);

					$request_prestamo = "";

					if($_SESSION['permisosMod']['w']){
						//Calculando el valor sumado con los intereses
						$intTazaTemporal = ($intTaza * 0.01);
						$subtotal = ($intMonto * $intTazaTemporal);
						$intTotal = ($intMonto + $subtotal);
						$intParcela = ($intTotal / $intPlazo);
						$request_prestamo = $this->model->insertPrestamo($intClienteId, 
																		 $intMonto,
																		 $intTotal,
																		 $intTaza,
																		 $intParcela,
																		 $intPlazo,
																		 $intFormato,
																		 $strObservacion,
																		 $fecha_actual,
																		 $fechaFinal);
					}
					if($request_prestamo > 0)
					{
						$arrResponse = array('status' => true, 'msg' => 'Préstamo guardado con éxito.');
					}else if($request_prestamo == '0')
					{
						$arrResponse = array('status' => false, 'msg' => 'Atencion! Error al ingresar un préstamo, inténtalo otro día.');
					}else
					{
						$arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
					}	
				}	
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}
			die();
		}
	}
?>