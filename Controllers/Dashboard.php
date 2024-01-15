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

			$ruta = $_SESSION['idRuta'];

			$data['totalCartera'] = $this->model->totalCartera($ruta, date("Y-m-d"));

			$data['usuarios'] = $this->model->cantUsuarios();
			$data['clientes'] = $this->model->cantClientes();
			$data['prestamos'] = $this->model->cantPrestamos();
			$data['prestamosFinalizados'] = $this->model->cantPrestamosFinalizados();
			$data['cartera'] = $this->model->selectCartera();
			
			$data['ultimosPrestamo'] = $this->model->ultimosPrestamo($ruta);
			$anio = date("Y");
			$mes = date("m");
			$data['ventasMDia'] = $this->model->selectVentasMes($anio,$mes);
			$data['CobradoMDia'] = $this->model->selectCobradoMes($anio,$mes);
			$data['gastosMDia'] = $this->model->selectGastosMes($anio,$mes);

			$data['ventasAnio'] = $this->model->selectVentasAnio($anio);
			$data['cobradoAnio'] = $this->model->selectCobradoAnio($anio);
			$data['gastosAnio'] = $this->model->selectGastosAnio($anio);

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

		public function getUltimosResumenes()
		{
			$resumenes = $this->model->ultimosResumenes();
			$dias = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");
			$datos = '';

			for ($i=0; $i < COUNT($resumenes); $i++)
			{ 
				$dia = $dias[date('w', strtotime($resumenes[$i]['datecreated']))];
				$datos .= '<tr class="text-center">';
				$datos .= '<td>'.date("d-m-Y", strtotime($resumenes[$i]['datecreated'])).'</td>';
				$datos .= '<td>'.round($resumenes[$i]['base'], 0).'</td>';
				$datos .= '<td>
							<button class="btn btn-outline-secondary btn-sm" data-toggle="popover" data-placement="left" data-content="'.forClientesPagos($resumenes[$i]['datecreated']).'" title="Fecha:&nbsp; <small>'.$dia.'</small>">
								'.round($resumenes[$i]['cobrado'], 0).'
							</button>
						   </td>';
				$datos .= '<td>
							<button class="btn btn-outline-secondary btn-sm" data-toggle="popover" data-placement="left" data-content="'.forClientesVentas($resumenes[$i]['datecreated']).'" title="Fecha:&nbsp; <small>'.$dia.'</small>">
								'.round($resumenes[$i]['ventas'], 0).'
							</button>
							</td>';
				$datos .= '<td>
							<button class="btn btn-outline-secondary btn-sm" data-toggle="popover" data-placement="left" data-content="'.forGastoResumen($resumenes[$i]['datecreated']).'" title="Fecha:&nbsp; <small>'.$dia.'</small>">
								'.round($resumenes[$i]['gastos'], 0).'
							</button>
							</td>';
				$datos .= '<td>'.round($resumenes[$i]['total'], 0).'</td>';
				$datos .= '</tr>';
			}
			
			$arrResponse = array('resumenes' => $datos);

			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);	
		}

		public function getResumenD()
		{
			if($_POST)
			{
				$arrayFechas = explode("-", $_POST['fecha']);
				$fechaI = date("Y-m-d", strtotime(str_replace("/", "-", $arrayFechas[0])));
				$fechaF = date("Y-m-d", strtotime(str_replace("/", "-", $arrayFechas[1])));
				$ruta = $_SESSION['idRuta'];
				$detalles = '';
				$dias = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");

				$resumenD = $this->model->selectResumenD($fechaI, $fechaF, $ruta);

				for ($i=0; $i < COUNT($resumenD); $i++)
				{ 
					$fechaFormateada = date('d-m-Y', strtotime($resumenD[$i]['fecha']));
					$dia = $dias[date('w', strtotime($resumenD[$i]['fecha']))];
					$datosResumen = "Base = ". number_format($resumenD[$i]['base'], 0). "<br>".
									"Cobrado = ". number_format($resumenD[$i]['cobrado']). "<br>".
									"Ventas = ". number_format($resumenD[$i]['ventas']). "<br>".
									"Gastos = ". number_format($resumenD[$i]['gastos']). "<br>".
									"Total = ". number_format($resumenD[$i]['total']);

					$detalles .= '<tr class="text-center">';
					$detalles .= '<td>'.$fechaFormateada.'</td>';
					$detalles .= '<td>'.$resumenD[$i]['base'].'</td>';
					$detalles .= '<td><button class="btn btn-outline-secondary btn-sm" data-toggle="popover" data-placement="left" data-content="'.forClientesPagos($resumenD[$i]['fecha']).'" title="Fecha:&nbsp; <small>'.$dia.'</small>">'.round($resumenD[$i]['cobrado'], 0).'</button></td>';
					$detalles .= '<td><button class="btn btn-outline-secondary btn-sm" data-toggle="popover" data-placement="left" data-content="'.forClientesVentas($resumenD[$i]['fecha']).'" title="Fecha:&nbsp; <small>'.$dia.'</small>">'.round($resumenD[$i]['ventas'], 0).'</button></td>';
					$detalles .= '<td><button class="btn btn-outline-secondary btn-sm" data-toggle="popover" data-placement="left" data-content="'.forGastoResumen($resumenD[$i]['fecha']).'" title="Fecha:&nbsp; <small>'.$dia.'</small>">'.round($resumenD[$i]['gastos'], 0).'</button></td>';
					$detalles .= '<td>'.$resumenD[$i]['total'].'</td>';
					// $detalles .= '<td>
					// 				<a tabindex="0" role="button" class="btn btn-info btn-sm" data-toggle="popover" data-placement="left" data-content="'.$datosResumen.'" title="Fecha:&nbsp; <small>'.$fechaFormateada.'</small>">
					// 					<i class="fas fa-info-circle fa-sm" aria-hidden="true"></i>
					// 				</a>
					// 				</td>';	
					$detalles .= '</tr>';
				}
				
				$arrResponse = array('resumenD' => $detalles);

				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}
		}

		public function getPrestamosFD()
		{
			if($_POST)
			{
				$intClienteId = $_POST['intClienteId'];
				$ruta = $_SESSION['idRuta'];
				$detalles = '';
				$totalJuros = 0;

				$clientesD = $this->model->selectPrestamosFD($intClienteId,$ruta);

				for ($i=0; $i < COUNT($clientesD); $i++)
				{ 
					$arrCliente = explode("|", $clientesD[$i]);
					//dep($arrCliente);exit;
					$fechaFormateada = $arrCliente[0];
					//$dia = $dias[date('w', strtotime($arrCliente[$i]['fecha']))];

					$detalles .= '<tr class="text-center">';
					$detalles .= '<td>'.$fechaFormateada.'</td>';
					$detalles .= '<td>'.$arrCliente[1].'</td>';
					$detalles .= '<td>
									<a tabindex="0" role="button" class="btn btn-secondary btn-sm" data-toggle="popover" data-placement="left" data-content="'.$arrCliente[2].'" title="PAGAMENTOS:&nbsp; <small>'.$arrCliente[1].'</small>">
										<i class="fas fa-hand-holding-usd fa-sm" aria-hidden="true"></i>
									</a>
								  </td>';
					$detalles .= '<td>
									<a tabindex="0" role="button" class="btn btn-secondary btn-sm" data-toggle="popover" data-placement="left" data-content="'.$arrCliente[3].'" title="DETALLES:&nbsp; <small>'.$arrCliente[1].'</small>">
										<i class="fas fa-eye fa-sm" aria-hidden="true"></i>
									</a>
									</td>';	
					$detalles .= '</tr>';

					$totalJuros += $arrCliente[4];
				}
				
				$arrResponse = array('clientesD' => $detalles, 'totalJuros' => $totalJuros);

				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
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

		public function ventasAnio(){
			if($_POST){
				$grafica = "ventasAnio";
				$anio = intval($_POST['anio']);
				$pagos = $this->model->selectVentasAnio($anio);
				$script = getFile("Template/Modals/graficaAnoVentas",$pagos);
				echo $script;
				die();
			}
		}

		public function cobradoAnio(){
			if($_POST){
				$grafica = "cobradoAnio";
				$anio = intval($_POST['anio']);
				$pagos = $this->model->selectCobradoAnio($anio);
				$script = getFile("Template/Modals/graficaAnoCobrado",$pagos);
				echo $script;
				die();
			}
		}

		public function gastosAnio(){
			if($_POST){
				$grafica = "gastosAnio";
				$anio = intval($_POST['anio']);
				$pagos = $this->model->selectGastosAnio($anio);
				$script = getFile("Template/Modals/graficaAnoGastos",$pagos);
				echo $script;
				die();
			}
		}
	}
 ?>