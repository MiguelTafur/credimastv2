<?php 

	class Dashboard extends Controllers{
		public function __construct()
		{
			session_start();
			parent::__construct();
			if(empty($_SESSION['login'])){
				header('Location: '.base_url().'/login');
			}
			getPermisos(MDASHBOARD);
		}

		public function dashboard()
		{
			$data['page_id'] = 2;
			$data['page_tag'] = "Dashboard - Credimast";
			$data['page_title'] = "Dashboard - Credimast";
			$data['page_name'] = "credimast";

			$data['totalCartera'] = $this->model->totalCartera($_SESSION['idRuta'], date("Y-m-d"));

			$data['usuarios'] = $this->model->cantUsuarios();
			$data['clientes'] = $this->model->cantClientes();
			$data['prestamos'] = $this->model->cantPrestamos();
			$data['prestamosFinalizados'] = $this->model->cantPrestamosFinalizados();
			$data['cartera'] = $this->model->selectCartera();
			
			$data['ultimosPrestamo'] = $this->model->ultimosPrestamo();
			$data['ultimosResumenes'] = $this->model->ultimosResumenes();
			$anio = date("Y");
			$mes = date("m");
			$data['ventasMDia'] = $this->model->selectVentasMes($anio,$mes);
			$data['CobradoMDia'] = $this->model->selectCobradoMes($anio,$mes);
			$data['gastosMDia'] = $this->model->selectGastosMes($anio,$mes);
			//dep($data['gastosMDia']);exit;
			$data['totalResumen'] = $this->model->selectResumenAnterior();
			if(!empty($data['totalResumen']))
			{
				$data['totalResumen']['cartera'] = $data['totalResumen']['total'] + ($data['cartera']['total']);
			}
			$data['pagamentos'] = $this->model->selectDatePagoPrestamo();

			$data['page_functions_js'] = "functions_dashboard.js";
			$this->views->getView($this,"dashboard",$data);
		}

		public function ventasMes()
		{
			if($_POST)
			{
				$grafica = "ventasMes";
				$nFecha = str_replace(" ", "", $_POST['fecha']);
				$arrFecha = explode('-', $nFecha);
				$mes = $arrFecha[0];
				$anio = $arrFecha[1];
				$pagos = $this->model->selectVentasMes($anio,$mes);
				$script = getFile("Template/Modals/graficas", $pagos);
				echo $script;
				die();
			}
		}

		public function cobradoMes()
		{
			if($_POST)
			{
				$grafica = "cobradoMes";
				$nFecha = str_replace(" ", "", $_POST['fecha']);
				$arrFecha = explode('-', $nFecha);
				$mes = $arrFecha[0];
				$anio = $arrFecha[1];
				$pagos = $this->model->selectCobradoMes($anio,$mes);
				$script = getFile("Template/Modals/graficaCobrado", $pagos);
				echo $script;
				die();
			}
		}

		public function gastosMes()
		{
			if($_POST)
			{
				$grafica = "gastosMes";
				$nFecha = str_replace(" ", "", $_POST['fecha']);
				$arrFecha = explode('-', $nFecha);
				$mes = $arrFecha[0];
				$anio = $arrFecha[1];
				$gastos = $this->model->selectGastosMes($anio,$mes);
				$script = getFile("Template/Modals/graficaGastos", $gastos);
				echo $script;
				die();
			}
		}

		public function getCobradoD()
		{
			if($_POST)
			{
				$arrayFechas = explode("-", $_POST['fecha']);
				//dep($_POST['fecha']);exit;
				$fechaI = date("Y-m-d", strtotime(str_replace("/", "-", $arrayFechas[0])));
				$fechaF = date("Y-m-d", strtotime(str_replace("/", "-", $arrayFechas[1])));
				$ruta = $_SESSION['idRuta'];
				$detalles = '';
				$arrExplode = '';
				$totalCobrado = 0;
				$dias = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");

				$cobradoD = $this->model->selectCobradoD($fechaI, $fechaF, $ruta);

				for ($i=0; $i < COUNT($cobradoD['cobrado']); $i++)
				{ 
					$arrExplode = explode("|",$cobradoD['cobrado'][$i]);
					$detalles .= '<tr class="text-center">';
					$detalles .= '<td>'.$dias[date('w', strtotime($arrExplode[0]))].'</td>';;
					$detalles .= '<td>'.$arrExplode[1].'</td>';
					$detalles .= '<td>
									<a tabindex="0" role="button" class="btn btn-info btn-sm" data-toggle="popover" data-placement="left" data-content="'.$arrExplode[2].'" title="CLIENTES">
										<i class="fas fa-info-circle fa-sm" aria-hidden="true"></i>
									</a>
									</td>';	
					$detalles .= '</tr>';
					$totalCobrado += $arrExplode[1];
				}
				
				$arrResponse = array('cobradoD' => $detalles, 'totalCobrado' => $totalCobrado);

				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}
		}

		public function getVentasD()
		{
			if($_POST)
			{
				$arrayFechas = explode("-", $_POST['fecha']);
				$fechaI = date("Y-m-d", strtotime(str_replace("/", "-", $arrayFechas[0])));
				$fechaF = date("Y-m-d", strtotime(str_replace("/", "-", $arrayFechas[1])));
				$ruta = $_SESSION['idRuta'];
				$detalles = '';
				$arrExplode = '';
				$totalVentas = 0;
				$dias = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");

				$ventasD = $this->model->selectVentasD($fechaI, $fechaF, $ruta);


				for ($i=0; $i < COUNT($ventasD['ventas']); $i++)
				{ 
					$arrExplode = explode("|",$ventasD['ventas'][$i]);
					$detalles .= '<tr class="text-center">';
					$detalles .= '<td>'.$dias[date('w', strtotime($arrExplode[0]))].'</td>';
					$detalles .= '<td>'.$arrExplode[1].'</td>';
					if($arrExplode[1] == 0)
					{
						$detalles .= '<td>
									<a style="cursor: not-allowed;opacity: 0.65;" tabindex="0" role="button" class="btn btn-info btn-sm">
										<i class="fas fa-info-circle fa-sm" aria-hidden="true"></i>
									</a>
									</td>';	
					}else{
						$detalles .= '<td>
									<a tabindex="0" role="button" class="btn btn-info btn-sm" data-toggle="popover" data-placement="left" data-content="'.$arrExplode[2].'" title="VENTAS">
										<i class="fas fa-info-circle fa-sm" aria-hidden="true"></i>
									</a>
									</td>';	
					}
					$detalles .= '</tr>';
					$totalVentas += $arrExplode[1];
				}

				//dep($detalles);exit;
				
				$arrResponse = array('ventasD' => $detalles, 'totalVentas' => $totalVentas);

				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);

				//$cliente = forClientesPagos($fecha_actual);
			}
		}

		public function getGastosD()
		{
			if($_POST)
			{
				$arrayFechas = explode("-", $_POST['fecha']);
				$fechaI = date("Y-m-d", strtotime(str_replace("/", "-", $arrayFechas[0])));
				$fechaF = date("Y-m-d", strtotime(str_replace("/", "-", $arrayFechas[1])));
				$ruta = $_SESSION['idRuta'];
				$detalles = '';
				$arrExplode = '';
				$totalGastos = 0;
				$dias = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");

				$gastosD = $this->model->selectGastosD($fechaI, $fechaF, $ruta);


				for ($i=0; $i < COUNT($gastosD['gastos']); $i++)
				{ 
					$arrExplode = explode("|",$gastosD['gastos'][$i]);
					$detalles .= '<tr class="text-center">';
					$detalles .= '<td>'.$dias[date('w', strtotime($arrExplode[0]))].'</td>';
					$detalles .= '<td>'.$arrExplode[1].'</td>';
					if($arrExplode[1] == 0)
					{
						$detalles .= '<td>
									<a style="cursor: not-allowed;opacity: 0.65;" tabindex="0" role="button" class="btn btn-info btn-sm">
										<i class="fas fa-info-circle fa-sm" aria-hidden="true"></i>
									</a>
									</td>';	
					}else{
						$detalles .= '<td>
									<a tabindex="0" role="button" class="btn btn-info btn-sm" data-toggle="popover" data-placement="left" data-content="'.$arrExplode[2].'" title="GASTOS">
										<i class="fas fa-info-circle fa-sm" aria-hidden="true"></i>
									</a>
									</td>';	
					}
					$detalles .= '</tr>';
					$totalGastos += $arrExplode[1];
				}

				//dep($detalles);exit;
				
				$arrResponse = array('gastosD' => $detalles, 'totalGastos' => $totalGastos);

				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);

				//$cliente = forClientesPagos($fecha_actual);
			}
		}
	}
 ?>