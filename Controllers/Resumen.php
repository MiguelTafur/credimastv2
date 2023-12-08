<?php 

	class Resumen extends Controllers
	{
		public function __construct()
		{
			parent::__construct();
			session_start();
			if(empty($_SESSION['login'])){
				header('Location: '.base_url().'/login');
			}
			getPermisos(MRESUMEN);
		}

		public function Resumen()
		{
			if(empty($_SESSION['permisosMod']['r'])){
				header("Location: ".base_url().'/prestamos');
			}
			$rutaId = $_SESSION['idRuta'];
			$data['page_tag'] = "Resumen";
			$data['page_title'] = "RESUMEN";
			$data['page_name'] = "resumen";
			$data['resumen'] = $this->model->selectResumen($rutaId);
			$data['resumenAnterior'] = $this->model->selectResumenAnterior($rutaId);
			$data['pagamentos'] = $this->model->selectDatePagoPrestamo();
			//dep($data['pagamentos']);exit();
			$data['page_functions_js'] = "functions_resumen.js";
			$fechaPagamento = $data['pagamentos'] == 2 ? NULL : $data['pagamentos']['datepago'];
			if($data['pagamentos'] != 2)
			{
				$data['gastos'] = $this->model->selectGastos($rutaId,$fechaPagamento);
				$data['base'] = $this->model->selectBase($fechaPagamento);
			
				$data['prestamos'] = $this->model->selectPrestamos2($fechaPagamento, $rutaId);	
				
				//dep($data['prestamos']);exit;
				$this->views->getView($this,"resumenAnterior",$data);
			}else{
				$this->views->getView($this,"resumen",$data);
			}
			
		}

		public function getResumen()
		{
			
			if($_SESSION['permisosMod']['r'])
			{
				$rutaId = $_SESSION['idRuta'];
				$cartera = 0;
				$clientes = 0;
				$parcela = 0;
				$cobrado = 0;			
				$ventas = 0;
				$idGasto = 0;
				$gasto = 0;
				$gastos = 0;
				$base2 = 0;
				$prestamos = 0;
				$baseAnterior = 0;
				$delGastos = '';
				$nombreGasto = "";
				$ventasC = "";
				$cliente = "";
				$idResumen = 0;
				$rutaBase = 0;
				$fecha_actual = date("Y-m-d");

				//CARTERA
				$arrDataCA = $this->model->selectCartera($rutaId);
				for ($i=0; $i < count($arrDataCA); $i++)
				{ 
					if($_SESSION['permisosMod']['d'])
					{	
						$cartera += $arrDataCA[$i]['total'];
					}else{
						$cartera = 0;
					}
					//ESTIMADO COBRAR
					$parcela += $arrDataCA[$i]['parcela'];
				}

				//ID RESUMEN
				$arrDataR = $this->model->selectResumen($rutaId);
				if($arrDataR > 0)
				{
					for ($i=0; $i < count($arrDataR); $i++) { 
						if($arrDataR[$i]['codigoruta'] == $rutaId){
							$idResumen = $arrDataR[$i]['idresumen'];
						}
					}					
				}

				//COBRADO
				$arrDataC = $this->model->selectPagos2($rutaId);
				for ($i=0; $i < count($arrDataC); $i++)
				{ 
					$cobrado += $arrDataC[$i]['abono'];
				}

				//VENTAS
				$arrDataV = $this->model->selectPrestamos($rutaId);
				for ($i=0; $i < count($arrDataV); $i++)
				{ 
					if($arrDataV[$i]['datecreated'] == $fecha_actual)
					{
						$ventas += $arrDataV[$i]['monto'];
						$ventasC .= strtoupper($arrDataV[$i]['nombres']).' = '/*.SMONEY*/.' '.$arrDataV[$i]['monto'].'<br>';
					}
				}

				$cliente = forClientesPagos($fecha_actual);

				//BASE
				$arrDataB = $this->model->selectBase($fecha_actual);
				$arrDataRA = $this->model->selectResumenAnterior($rutaId);
				$baseResumenAnterior = $arrDataRA > 0 ? $baseAnterior = $arrDataRA['total'] : $baseAnterior = 0; 
				
				if($arrDataB > 0){

					$base = $arrDataB['monto'];
					$base2 = $baseResumenAnterior;
					$idBase = $arrDataB['idbase'];
					$rutaBase = $arrDataB['codigoruta'];
					//dep($arrDataB);exit;
				}else{
					$base = $baseAnterior;
					$idBase = 0;
				}
				
				//GASTOS
				$arrDataG2 = $this->model->selectGastos($rutaId, $fecha_actual);
				if($arrDataG2 >= 1){
					for ($i=0; $i < count($arrDataG2); $i++) { 
						if($arrDataG2[$i]['nombre'] != ""){
							$gastos += $arrDataG2[$i]['monto'];
						}
						$delGastos .= '<button type="button" class="dropdown-item" onclick="fntDelGasto('.$arrDataG2[$i]['idgasto'].')">
										<i class="fa fa-times-circle fa-sm"></i> '.ucwords($arrDataG2[$i]['nombre']).' - '/*.SMONEY*/.$arrDataG2[$i]['monto'].'
									</button>';
						$idGasto = $arrDataG2[$i]['idgasto'];
					}
				}
				
				$total = (($cobrado + $base) - ($gastos + $ventas));
				$cartera = $cartera + ($total);

				$arrResponse = array('cobrado'=> $cobrado, 'cliente'=>$cliente, 'ventas'=>$ventas,'ventasC'=>$ventasC,
						'base' => $base, 'base2' => $base2,'idbase' => $idBase,'idGasto' => $idGasto,'gasto' => $gasto, 
						'gastos' => $gastos, 'delGastos' => $delGastos, 'nombreGasto' => $nombreGasto,'total' => $total,
						'idresumen' => $idResumen, 'idRutaBase' => $rutaBase);

				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		public function setBase()
		{
			if($_POST)
			{
				if($_SESSION['idUser'] == 1 || $_SESSION['idUser'] == 254 || $_SESSION['idUser'] == 1087)
				{
					$intBase = intval($_POST['txtBase']);
					$strObservacion = strClean($_POST['txtObservacion']);
					if(!empty($_POST['fechaAnterior']))
					{
						$fecha_actual = $_POST['fechaAnterior'];
					}else{
						$fecha_actual = date("Y-m-d");
					}

					$intRutaId = $_SESSION['idRuta'];

					$request_base = $this->model->insertBase($intRutaId,$intBase,$strObservacion,$fecha_actual);

					if($request_base > 0)
					{
						$arrResponse = array('status' => true, 'msg' => 'Datos guardados correctamente.');
					}else if($request_base == '0')
					{
						$arrResponse = array("status" => false, "msg" => "Base ya ingresada!");
					}else{
						$arrResponse = array("status" => false, "msg" => "No es posible almacenar los datos.");
					}
				}else{
					$arrResponse = array("status" => false, "msg" => "No tienes los permisos para esta acción.");
				}
				echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
				
			}
			die();
		}

		public function setGasto()
		{
			if($_POST)
			{
				//dep($_POST);exit;
				if(empty($_POST['txtGasto']) || empty($_POST['txtNombre']))
				{
					$arrResponse = array("status" => false, "msg" => "Datos incorrectos.");
				}else
				{
					$intGasto = intval($_POST['txtGasto']);
					$strNombre = strClean($_POST['txtNombre']);
					$rutaId = $_SESSION['idRuta'];
					//dep($rutaId);exit;
					if(!empty($_POST['fechaAnterior']))
					{
						$fecha_actual = $_POST['fechaAnterior'];
					}else{
						$fecha_actual = date("Y-m-d");
					}

					$request_gasto = $this->model->insertGasto($rutaId,$strNombre,$intGasto,$fecha_actual);

					if($request_gasto > 0)
					{
						$arrResponse = array('status' => true, 'msg' => 'Datos guardados correctamente.');
					}else
					{
						$arrResponse = array("status" => false, "msg" => "No es posible almacenar los datos.");
					}
				}
				echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		public function setResumen()
		{
			if($_POST)
			{
				//if(!empty($_POST['baseAnterior'])){
					$intBaseAnterior = intval($_POST['baseAnterior']);
				//}
				
				$intBase = intval($_POST['idBase']);
				$intGasto = intval($_POST['idGasto']);
				$intGastos = intval($_POST['gastos']);
				$intCobrado = intval($_POST['cobrado']);
				//if($_POST['ventas']){
					$intVentas = intval($_POST['ventas']);
				//}
				
				$intTotal = intval($_POST['total']);
				$intRutaId = $_SESSION['idRuta'];
				$obs = "";
				$nombre = "";

				//dep($_POST);exit();

				$fecha_actual = date("Y-m-d");
				$query_gastos = $this->model->selectGastos($intRutaId, $fecha_actual);
				$totalGastos = 0;
				for ($i=0; $i < count($query_gastos); $i++)
				{ 
					$totalGastos = count($query_gastos);
				}

				if($intBase > 0)
				{
					if($intGasto == 0)
					{
						$request_gasto = $this->model->insertGasto($intRutaId,$nombre,$intGasto,$fecha_actual);
						$request_resumen = $this->model->insertResumen($intBase,$request_gasto,$intRutaId,$intCobrado,$intVentas,$intTotal,$fecha_actual);
						if($request_resumen > 0)
						{
							$arrResponse = array('status' => true,'msg' => 'Datos guardados correctamente.');
						}else{
							$arrResponse = array("status" => false, "msg" => "No es posible almacenar los datos.");
						}
					}else{
						
						if($totalGastos > 1)
						{
							$request_gasto = $this->model->insertGasto($intRutaId,"",$intGastos,$fecha_actual);
						}else{
							$request_gasto = $intGasto;
						}
						$request_resumen = $this->model->insertResumen($intBase,$request_gasto,$intRutaId,$intCobrado,$intVentas,$intTotal,$fecha_actual);
						if($request_resumen > 0)
						{
							$arrResponse = array('status' => true,'msg' => 'Datos guardados correctamente.');
						}else{
							$arrResponse = array("status" => false, "msg" => "No es posible almacenar los datos.");
						}
					}
				}else{
					$request_base = $this->model->insertBase($intRutaId,$intBaseAnterior,$obs,$fecha_actual);
					if($intGasto == 0)
					{
						$request_gasto = $this->model->insertGasto($intRutaId,$nombre,$intGasto,$fecha_actual);
						$request_resumen = $this->model->insertResumen($request_base,$request_gasto,$intRutaId,$intCobrado,$intVentas,$intTotal,$fecha_actual);
						if($request_resumen > 0)
						{
							$arrResponse = array('status' => true,'msg' => 'Datos guardados correctamente.');
						}else{
							$arrResponse = array("status" => false, "msg" => "No es posible almacenar los datos.");
						}
					}else{
						if($totalGastos > 1)
						{
							$request_gasto = $this->model->insertGasto($intRutaId,"",$intGastos,$fecha_actual);
						}else{
							$request_gasto = $intGasto;
						}
						$request_resumen = $this->model->insertResumen($request_base,$request_gasto,$intRutaId,$intCobrado,$intVentas,$intTotal,$fecha_actual);
						if($request_resumen > 0)
						{
							$arrResponse = array('status' => true,'msg' => 'Datos guardados correctamente.');
						}else{
							$arrResponse = array("status" => false, "msg" => "No es posible almacenar los datos.");
						}
					}
				}

				echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		public function setResumenAnterior()
		{
			if($_POST)
			{
				$fechaR = $_POST['fechaResumen'];
				$intIdBase = intval($_POST['idBase']);
				//if(!empty($_POST['baseAnteriorRA'])){
					$intBaseAnterior = intval($_POST['baseAnteriorRA']);
				//}
				
				$intIdGasto = intval($_POST['idGasto']);
				$intGastos = intval($_POST['gastos']);
				$intCobrado = intval($_POST['cobradoAnterior']);
				//if($_POST['ventasAnterior']){
					$intVentas = intval($_POST['ventasAnterior']);
				//}
				
				$intTotal = intval($_POST['total']);
				$nombre = "";
				$ruta = $_SESSION['idRuta'];
				//dep($_POST);exit();

				$query_gastos = $this->model->selectGastos($ruta, $fechaR);
				if($intIdBase > 0)
				{
					if(!empty($query_gastos))
					{
						if(count($query_gastos) > 1)
						{
							$request_gasto = $this->model->insertGasto($ruta,$nombre,$intGastos,$fechaR);
						}else if(count($query_gastos) == 1)
						{
							$request_gasto = $intIdGasto;
						}else{
							$request_gasto = $intIdGasto;
						}

						$request_resumen = $this->model->insertResumen($intIdBase,$request_gasto,$ruta,$intCobrado,$intVentas,$intTotal,$fechaR);
						if($request_resumen > 0)
						{
							$arrResponse = array('status' => true,'msg' => 'Datos guardados correctamente.');
						}else{
							$arrResponse = array("status" => false, "msg" => "No es posible almacenar los datos.");
						}
					}else{
						$request_gasto = $this->model->insertGasto($ruta,$nombre,0,$fechaR);
						$request_resumen = $this->model->insertResumen($intIdBase,$request_gasto,$ruta,$intCobrado,$intVentas,$intTotal,$fechaR);
						if($request_resumen > 0)
						{
							$arrResponse = array('status' => true,'msg' => 'Datos guardados correctamente.');
						}else{
							$arrResponse = array("status" => false, "msg" => "No es posible almacenar los datos.");
						}
					}
				}else{
					$request_base = $this->model->insertBase($ruta,$intBaseAnterior,$nombre,$fechaR);
					if(!empty($query_gastos))
					{
						if(count($query_gastos) > 1)
						{
							$request_gasto = $this->model->insertGasto($ruta,$nombre,$intGastos,$fechaR);
						}else if(count($query_gastos) == 1)
						{
							$request_gasto = $intIdGasto;
						}else{
							$request_gasto = $intIdGasto;
						}
						$request_resumen = $this->model->insertResumen($request_base,$request_gasto,$ruta,$intCobrado,$intVentas,$intTotal,$fechaR);
						if($request_resumen > 0)
						{
							$arrResponse = array('status' => true,'msg' => 'Datos guardados correctamente.');
						}else{
							$arrResponse = array("status" => false, "msg" => "No es posible almacenar los datos.");
						}
					}else{
						$request_gasto = $this->model->insertGasto($ruta,$nombre,0,$fechaR);
						$request_resumen = $this->model->insertResumen($request_base,$request_gasto,$ruta,$intCobrado,$intVentas,$intTotal,$fechaR);
						if($request_resumen > 0)
						{
							$arrResponse = array('status' => true,'msg' => 'Datos guardados correctamente.');
						}else{
							$arrResponse = array("status" => false, "msg" => "No es posible almacenar los datos.");
						}
					}
				}
				echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		public function delBase()
		{
			if($_POST)
			{
				if($_SESSION['permisosMod']['d'] && $_SESSION['idRol'] == 1){
					$intIdBase = intval($_POST['baseId']);
					$requestDelete = $this->model->deleteBase($intIdBase);
					if($requestDelete)
					{
						$arrResponse = array('status' => true, 'msg' => 'Se ha eliminado la base.');
					}else
					{
						$arrResponse = array('status' => false, 'msg' => 'Error al eliminar la base.');
					}
				}else{
					$arrResponse = array('status' => false, 'msg' => 'No tienes los persmisos para esta acción.');
				}
				echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);	
			}
			die();
		}

		public function delGasto()
		{
			if($_POST)
			{
				
				if($_SESSION['permisosMod']['d']){
					$intIdGasto = intval($_POST['gastoId']);
					//dep($intIdGasto);exit;
					$requestDelete = $this->model->deleteGasto($intIdGasto);
					if($requestDelete)
					{
						$arrResponse = array('status' => true, 'msg' => 'Se ha eliminado el gasto.');
					}else
					{
						$arrResponse = array('status' => false, 'msg' => 'Error al eliminar el gasto.');
					}
					echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);	
				}
			}
			die();
		}

		public function delResumen()
		{
			if($_POST)
			{
				if($_SESSION['permisosMod']['d']){
					$intIdResumen = intval($_POST['resumenId']);
					$intIdRuta = intval($_SESSION['idRuta']);
					$request_select = $this->model->selectResumen($intIdRuta);
					$gasto = 0;
					$base = 0;
					for ($i=0; $i < count($request_select); $i++) { 
						$gasto = $request_select[$i]['gastoid'];
						$base = $request_select[$i]['baseid'];
					}

					$fecha_actual = date("Y-m-d");

					$this->model->deleteGastoF($gasto, $fecha_actual);
					$this->model->deleteBase($base);

					$requestDelete = $this->model->deleteResumen($intIdResumen, $fecha_actual);
					if($requestDelete)
					{
						$arrResponse = array('status' => true, 'msg' => 'Se ha eliminado el resumen.');
					}else
					{
						$arrResponse = array('status' => false, 'msg' => 'Error al eliminar el resumen.');
					}
					echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);	
				}
			}
			die();
		}
	}
?>