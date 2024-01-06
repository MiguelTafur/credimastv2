<?php 

class Prestamos extends Controllers{
	public function __construct()
	{
		parent::__construct();
		session_start();
		if(empty($_SESSION['login'])){
			header('Location: '.base_url().'/login');
		}
		getPermisos(MPRESTAMOS);
	}

	public function Prestamos()
	{
		if(empty($_SESSION['permisosMod']['r'])){
			header("Location: ".base_url().'/prestamos');
		}
		$data['page_tag'] = "Prestamos";
		$data['page_title'] = "PRESTAMOS";
		$data['page_name'] = "prestamos";
		$data['resumen'] = $this->model->selectResumen();
		$data['pagamentos'] = $this->model->selectDatePagoPrestamo();
		$fechaPagamento = $data['pagamentos'] == 2 ? NULL : $data['pagamentos']['datepago'];
		
		$data['page_functions_js'] = "functions_prestamos.js";
		if($data['pagamentos'] != 2)
		{
			$data['prestamos'] = $this->model->selectPrestamos2($fechaPagamento);
			//dep($data['prestamos']);exit();
		}
		$this->views->getView($this,"prestamos",$data);
	}

	public function setPago()
	{
		if($_POST)
		{
			//dep($_POST);exit;
			if(empty($_POST['txtMontoPago']))
			{
				$arrResponse = array("status" => false, "msg" => "Debes ingresar un valor.");
			}else
			{
				$codigoPrestamo = intval(($_POST['codigoPrestamo']));
				$intMonto = intval($_POST['txtMontoPago']);
				if(!empty($_POST['fechaAnterior']))
				{
					$fecha_actual = $_POST['fechaAnterior'];
				}else{
					$fecha_actual = date("Y-m-d");
				}

				$request_prestamo_total = $this->model->selectTotalPrestamo($codigoPrestamo);
				if($request_prestamo_total['total'] < $intMonto)
				{
					$arrResponse = array("status" => false, "msg" => "El pago ingresado no puede ser mayor al saldo.");
				}else
				{
					$request_pago = $this->model->insertPago($codigoPrestamo,$intMonto,$fecha_actual);

					if($request_pago > 0)
					{
						$selectPagos = $this->model->selectPagos($codigoPrestamo);
						$pagado = 0;
						for ($i=0; $i < count($selectPagos); $i++)
						{
							$pagado += $selectPagos[$i]['abono'];
						}
						//ID del pago
						$idpago = $request_pago;
						$request_prestamo_total['datepago'] = $fecha_actual;
						$fecha_pago = $request_prestamo_total['datepago'];
						//Préstamo total
						$total = ($request_prestamo_total['total'] - $intMonto);
						//Préstamo pago
						$request_prestamo_total['pago'] = $intMonto;
						$pago = $request_prestamo_total['pago'];

						$request_prestamo_update = $this->model->updateTotalPrestamo($codigoPrestamo,$idpago,$total,$pagado,$pago,$fecha_pago);

						if($request_prestamo_update > 0)
						{
							$request_prestamo_total = $this->model->selectTotalPrestamo($codigoPrestamo);
							$request_prestamo_total['total'] > 0 ? $status = 1 : $status = 2;
							
							$request_prestamo_update = $this->model->updateStatusPrestamo($codigoPrestamo,$fecha_actual,$status);	

							$arrResponse = array('status' => true, 'msg' => 'Datos guardados correctamente.', 'pago' => $intMonto, 'idpago' => $request_pago, 'total' => $total);
						}else{
							$arrResponse = array("status" => false, "msg" => "No es posible almacenar los datos.");		
						}

					}else if($request_pago == '0')
					{
						$arrResponse = array("status" => false, "msg" => "Pago ya realizado.");	
					}else
					{
						$arrResponse = array("status" => false, "msg" => "No es posible almacenar los datos.");
					}
				}

			}
			echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		}
		die();
	}

	public function setPayAll($dados)
	{
		$datos = json_decode($dados, true);
		if($datos['pago'] == 0)
		{
			$arrResponse = array("status" => false, "msg" => "El valor no puede ser 0.");
		}else{
			$codigoPrestamo = intval($datos['id']);
			$intMonto = intval($datos['pago']);
			$fecha_actual = date("Y-m-d");
			$request_prestamo_total = $this->model->selectTotalPrestamo($codigoPrestamo);

			if($request_prestamo_total['total'] < $intMonto)
			{
				$arrResponse = array("status" => false, "msg" => 'El pago de: '.SMONEY.' '.$intMonto.' no puede ser mayor al saldo.');
			}else{
				$request_pago = $this->model->insertPago($codigoPrestamo,$intMonto,$fecha_actual);
				if($request_pago > 0)
				{
					$selectPagos = $this->model->selectPagos($codigoPrestamo);
					$pagado = 0;
					for ($i=0; $i < count($selectPagos); $i++)
					{
						$pagado += $selectPagos[$i]['abono'];
					}
					$idpago = $request_pago;
					$request_prestamo_total['datepago'] = $fecha_actual;
					$fecha_pago = $request_prestamo_total['datepago'];
					$total = ($request_prestamo_total['total'] - $intMonto);
					$request_prestamo_total['pago'] = $intMonto;
					$pago = $request_prestamo_total['pago'];

					$request_prestamo_update = $this->model->updateTotalPrestamo($codigoPrestamo,$idpago,$total,$pagado,$pago,$fecha_pago);

					if($request_prestamo_update > 0)
					{
						$request_prestamo_total = $this->model->selectTotalPrestamo($codigoPrestamo);
						$request_prestamo_total['total'] > 0 ? $status = 1 : $status = 2;
						
						$request_prestamo_update = $this->model->updateStatusPrestamo($codigoPrestamo,$fecha_actual,$status);						

						$arrResponse = array('status' => true, 'msg' => 'Datos guardados correctamente.');
					}else{
						$arrResponse = array("status" => false, "msg" => "No es posible almacenar los datos.");		
					}
				}else if($request_pago == '0')
				{
					$arrResponse = array("status" => false, "msg" => "Pago ya realizado.");
				}else{
					$arrResponse = array("status" => false, "msg" => "No es posible almacenar los datos.");
				}
			}
		}
		echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		die();
	}

	public function setUpdateList()
	{
		if($_POST)
		{
			$idsPrestamo = $_POST['listPrestamos'];
			$pos = 0;
			foreach ($idsPrestamo as $idprestamo) { 
				$this->model->updateListPrestamo($pos,$idprestamo);
				$pos++;
			}
		}
		die();
	}

	public function getPrestamos()
	{
		if($_SESSION['permisosMod']['r'])
		{
			$arrData = $this->model->selectPrestamos();
			$arrDataR = $this->model->selectResumen();
			if($arrDataR != 0){
				for ($i=0; $i < count($arrData); $i++)
				{ 
					$fecha_actual = date('Y-m-d');
					$btnView = '';
					$btnDelete = '';
					$btnAbono = '';
					$taza = ($arrData[$i]['taza'] * 0.01);
					$subtotal = ($arrData[$i]['monto'] * $taza);
					$total = ($arrData[$i]['monto'] + $subtotal);
					$parcela = ($total/$arrData[$i]['plazo']);

					$arrData[$i]['pa'] = (SMONEY.$parcela.' x '.$arrData[$i]['plazo']);

					$btnAbono = '<button class="btn btn-secondary btn-sm" disabled>
									  RESUMEN CERRADO
									</button>';

					$arrData[$i]['pagamento'] = '<div class="text-center">'.$btnAbono.'</div>';

					if($arrData[$i]['formato'] == 1)
					{
						$arrData[$i]['formato'] = 'Diário';
						if($arrData[$i]['plazo'] == 1)
						{
							$arrData[$i]['plazo'] = $arrData[$i]['plazo'].' '.'Día';
						}else
						{
							$arrData[$i]['plazo'] = $arrData[$i]['plazo'].' '.'Días';
						}
					}
					if($arrData[$i]['formato'] == 2)
					{
						$arrData[$i]['formato'] = 'Semanal';
						if($arrData[$i]['plazo'] == 1){
							$arrData[$i]['plazo'] = $arrData[$i]['plazo'].' '.'Semana';
						}else
						{
							$arrData[$i]['plazo'] = $arrData[$i]['plazo'].' '.'Semanas';
						}
					}
					if($arrData[$i]['formato'] == 3)
					{
						$arrData[$i]['formato'] = 'Mensual';
						if($arrData[$i]['plazo'] == 1)
						{
							$arrData[$i]['plazo'] = $arrData[$i]['plazo'].' '.'Mes';
						}else
						{
							$arrData[$i]['plazo'] = $arrData[$i]['plazo'].' '.'Meses';
						}
					}

					$arrData[$i]['nombres'] = strtok($arrData[$i]['nombres'], " ").' - <i>'.$arrData[$i]['apellidos'].'</i>';

					$arrData[$i]['monto'] = SMONEY.' '.$arrData[$i]['monto'];				

					$arrData[$i]['taza'] = $arrData[$i]['taza'].' '.'%';

					$arrData[$i]['total'] = '<p class="font-weight-bold font-italic text-danger">'.SMONEY.$arrData[$i]['total'].'</p>';


					if($_SESSION['permisosMod']['w'])
					{
						$btnView = '<button class="btn btn-info " onclick="fntViewPrestamo('.$arrData[$i]['idprestamo'].')" title="Ver usuario"><i class="far fa-eye"></i></button>&nbsp;&nbsp;';
					}
					if($_SESSION['permisosMod']['d'])
					{
						if($arrData[$i]['datecreated'] == $fecha_actual){
							
							$btnDelete = '<button class="btn btn-danger" disabled><i class="far fa-trash-alt"></i></button>';
						}
					}
					$arrData[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnDelete.'</div>';
				}
			}else{
				for ($i=0; $i < count($arrData); $i++)
				{ 
					//dep($arrData);exit;
					$fecha_actual = date('Y-m-d');
					$btnView = '';
					$btnDelete = '';
					$btnAbono = '';
					$taza = ($arrData[$i]['taza'] * 0.01);
					$subtotal = ($arrData[$i]['monto'] * $taza);
					$total = ($arrData[$i]['monto'] + $subtotal);
					$parcela = ($total/$arrData[$i]['plazo']);
					$dias = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");

					$arrData[$i]['monto'] = '<strong>'.$arrData[$i]['monto'].'</strong>';
					$arrData[$i]['pa'] = (' <strong>'.$parcela.' x '.$arrData[$i]['plazo'].'</strong>');

					if($arrData[$i]['pago'] != 0 && $arrData[$i]['datepago'] == $fecha_actual && $arrData[$i]['status'] == 2)
					{
						$btnAbono = '<p class="text-danger h5">
										<button class="btn btn-success btn-sm" onclick="fntRenovarPrestamo('.$arrData[$i]['idprestamo'].', '."".')">RENOVAR</button> &nbsp;&nbsp;
										<button class="btn btn-danger btn-sm" onclick="fntDelPago('.$arrData[$i]['pagoid'].')" title="Eliminar pago">
									  '.$arrData[$i]['pago'].'
										</button>
									</p>';

					}else if($arrData[$i]['pago'] != 0 && $arrData[$i]['datepago'] == $fecha_actual && $arrData[$i]['pagoid'] != NULL){
						$btnAbono = '<button class="btn btn-success btn-sm" onclick="fntDelPago('.$arrData[$i]['pagoid'].')" title="Eliminar pago">
									  '.$arrData[$i]['pago'].'
									</button>';
					}else{
						$btnAbono = '<div class="text-center divPagoPrestamo">
										<input type="tel" class="inpPago '.$arrData[$i]['idprestamo'].' my-1" id="'.$arrData[$i]['idprestamo'].'" style="width: 73px; height: 35px; padding: 5px" placeholder="'/*.SMONEY.' '*/.$arrData[$i]['parcela'].'" onkeypress="return controlTag(event)">
										<button id="btn-'.$arrData[$i]['idprestamo'].'" class="btn btn-secondary btn-sm pagoPrestamo" title="Agregar Pago" onclick="fntPagoPrestamo('.$arrData[$i]['idprestamo'].')"><i class="fas fa-hand-holding-usd"></i> Pagar
										</button>
									</div>';
					}

					$arrData[$i]['pagamento'] = '<div id="div-'.$arrData[$i]['idprestamo'].'" class="text-center">
													'.$btnAbono.' 
													<button class="btn btn-success btn-sm d-none" onclick="fntDelPago('.$arrData[$i]['pagoid'].')" id="btn2-'.$arrData[$i]['idprestamo'].'" title="Eliminar pago">
									  					'.$arrData[$i]['pago'].';
													</button>
												</div>';

					if($arrData[$i]['formato'] == 1)
					{
						$arrData[$i]['formato'] = 'Diário';
						if($arrData[$i]['plazo'] == 1)
						{
							$arrData[$i]['plazo'] = $arrData[$i]['plazo'].' '.'Día';
						}else
						{
							$arrData[$i]['plazo'] = $arrData[$i]['plazo'].' '.'Días';
						}
					}
					
					$diaPagamento = $dias[date("w", strtotime($arrData[$i]['datecreated']))];
					if($arrData[$i]['formato'] == 2)
					{
						$arrData[$i]['formato'] = '<h6>Semanal <span class="badge badge-secondary">'.$diaPagamento.'</span></h6>';
						if($arrData[$i]['plazo'] == 1){
							$arrData[$i]['plazo'] = $arrData[$i]['plazo'].' '.'Semana';
						}else
						{
							$arrData[$i]['plazo'] = $arrData[$i]['plazo'].' '.'Semanas';
						}
					}
					if($arrData[$i]['formato'] == 3)
					{
						$arrData[$i]['formato'] = 'Mensual';
						if($arrData[$i]['plazo'] == 1)
						{
							$arrData[$i]['plazo'] = $arrData[$i]['plazo'].' '.'Mes';
						}else
						{
							$arrData[$i]['plazo'] = $arrData[$i]['plazo'].' '.'Meses';
						}
					}

					$arrData[$i]['nombres'] = '<strong>'.strtok(strtoupper($arrData[$i]['nombres']), " ").'</strong> <i>'.$arrData[$i]['apellidos'].'</i>';

					$arrData[$i]['monto'] = /*SMONEY.*/' '.$arrData[$i]['monto'];				

					$arrData[$i]['taza'] = $arrData[$i]['taza'].' '.'%';

					$arrData[$i]['total'] = '<p id="tot-'.$arrData[$i]['idprestamo'].'" class="font-weight-bold font-italic text-danger">'/*.SMONEY*/.' '.$arrData[$i]['total'].'</p>';				

					if($_SESSION['permisosMod']['w'])
					{
						$btnView = '<button class="btn btn-info " onclick="fntViewPrestamo('.$arrData[$i]['idprestamo'].')" title="Ver Prestamo"><i class="far fa-eye"></i></button>&nbsp;&nbsp;';
					}

					if($_SESSION['permisosMod']['d'])
					{
						if($arrData[$i]['datecreated'] == $fecha_actual)
						{
							$btnDelete = '<button class="btn btn-danger " onclick="fntDelPrestamo('.$arrData[$i]['idprestamo'].')" title="Eliminar Prestamo"><i class="far fa-trash-alt"></i></button>';
						}
					}

					if($arrData[$i]['fechavence'] != NULL)
					{
						$diasVencimiento4 = date("Y-m-d", strtotime('-4 day', strtotime($arrData[$i]['fechavence'])));
						$diasVencimiento3 = date("Y-m-d", strtotime('-3 day', strtotime($arrData[$i]['fechavence'])));
						$diasVencimiento2 = date("Y-m-d", strtotime('-2 day', strtotime($arrData[$i]['fechavence'])));
						$diasVencimiento1 = date("Y-m-d", strtotime('-1 day', strtotime($arrData[$i]['fechavence'])));

						//$arrData[$i]['diasVence'] = $diasVencimiento4;
						
						 if($diasVencimiento4 == $fecha_actual || 
						 	$diasVencimiento3 == $fecha_actual || 
							$diasVencimiento2 == $fecha_actual || 
							$diasVencimiento1 == $fecha_actual || 
							$arrData[$i]['fechavence'] == $fecha_actual)
						 {
						 	$arrData[$i]['diasVence'] = false;
						 }else if($arrData[$i]['fechavence'] < $fecha_actual)
						 {
							$arrData[$i]['diasVence'] = "vencido";
						 }else{
							$arrData[$i]['diasVence'] = true;
						}
					}

					$arrData[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnDelete.'</div>';
				}
			}
			
			echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		}
		die();
	}

	public function getPrestamo()
	{
		//dep($_POST);exit;
		if($_SESSION['permisosMod']['r']){
			$idPrestamo = $_POST['idPrestamo'];
			$fecha = $_POST['datefinal'];
			$fecha_actual = date("Y-m-d");
			$fechaFinal = ($fecha != 'undefined') ? $fecha : $fecha_actual;
			
			//dep($bool);exit;
			if($idPrestamo > 0){
				$arrData = $this->model->selectPrestamo($idPrestamo,$fechaFinal);
				$dias = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");
				$diaPagamento = "";

				$plazo = $arrData['plazo']; 

				$arrData['datecreated'] = date("d-m-Y", strtotime($arrData['datecreated']));
				
				if($arrData['fechavence'] == NULL)
				{
					$arrData['fechavence'] = "";
				}else{
					$arrData['fechavence'] = date("d-m-Y", strtotime($arrData['fechavence']));
				}
				
				$taza = ($arrData['taza'] * 0.01);
				$arrData['taza'] = $arrData['taza'].' '.'%';
				$subtotal = ($arrData['monto'] * $taza);
				$total = ($arrData['monto'] + $subtotal);
				$restante = ($total - $arrData['pagado']);
				$parcela = ($total/$arrData['plazo']);
				if($arrData['formato'] == 1){
					$arrData['formato'] = 'Diário';
					if($arrData['plazo'] == 1){
						$arrData['plazo'] = $arrData['plazo'].' '.'Día';
					}else{
						$arrData['plazo'] = $arrData['plazo'].' '.'Días';
					}
				}
				if($arrData['formato'] == 2){
					$arrData['formato'] = 'Semanal';
					$diaPagamento = $dias[date("w", strtotime($arrData['datecreated']))];
					if($arrData['plazo'] == 1){
						$arrData['plazo'] = $arrData['plazo'].' '.'Semana';
					}else{
						$arrData['plazo'] = $arrData['plazo'].' '.'Semanas';
					}
				}
				if($arrData['formato'] == 3){
					$arrData['formato'] = 'Mensual';
					if($arrData['plazo'] == 1){
						$arrData['plazo'] = $arrData['plazo'].' '.'Mes';
					}else{
						$arrData['plazo'] = $arrData['plazo'].' '.'Meses';
					}
				}

				$arrData['plazo'] = (isset($_POST['bool'])) ? $plazo : $arrData['plazo'];

				$arrData['pendiente'] = round(($restante/$parcela), 0, PHP_ROUND_HALF_UP);
				$arrData['cancelado'] = round(($arrData['pagado']/$parcela), 0, PHP_ROUND_HALF_DOWN);
				$arrData['diaPagamento'] = $diaPagamento;

				//dep($arrData['plazo']);

				if(empty($arrData))
				{
					$arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
				}else{
					$arrResponse = array('status' => true, 'data' => $arrData);
				}
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}
		}
		die();
	}

	public function getPrestamoPago($idprestamo)
	{
		if($_SESSION['permisosMod']['r']){
			$idPersona = intval($idprestamo);
			if($idPersona > 0)
				$arrData = $this->model->selectCodigoPrestamo($idPersona);
			{
				if(empty($arrData))
				{
					$arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
				}else{
					$arrResponse = array('status' => true, 'data' => $arrData);
				}
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}
		}
		die();
	}

	public function getListPrestamos()
	{
		if($_SESSION['permisosMod']['r'])
		{
			$arrData = $this->model->selectPrestamos();
			$nombres = "";
			for ($i=0; $i < count($arrData); $i++)
			{ 
				$nombres .= '<li class="list-group-item list-group-item-info item cursor" id="'.$arrData[$i]['idprestamo'].'">'.$arrData[$i]['nombres'].' - '.$arrData[$i]['apellidos'].'</li>';
			}
			$arrResponse = array('nombres' => $nombres);

			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		}
		die();
	}

	public function getPagos($idprestamo)
	{
		if($_SESSION['permisosMod']['r']){
			$idPrestamo = intval($idprestamo);
			if($idPrestamo > 0)
				$arrData = $this->model->selectPagos($idPrestamo);
			{
				if(empty($arrData))
				{
					$arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
				}else{
					$arrResponse = array('status' => true, 'data' => $arrData);
				}
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}
		}
		die();
	}

	public function getListPagos($idprestamo)
	{
		if($_SESSION['permisosMod']['r']){
			$idPrestamo = intval($idprestamo);
			$fecha_actual = date("d-m-Y");
			if($idPrestamo > 0)
				$arrData = $this->model->selectPagos($idPrestamo);
			{
				if(empty($arrData))
				{
					$arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
				}else{
					$trPagos = "";
					for ($i=0; $i < count($arrData); $i++)
					{ 
						$arrData[$i]['datecreated'] = date("d-m-Y", strtotime($arrData[$i]['datecreated']));
						$trPagos .= '
						<tr class="text-center">
							<td>'.$arrData[$i]['datecreated'].'</td>';
							if($arrData[$i]['datecreated'] == $fecha_actual)
							{
								$trPagos .= '<td>'.'<button class="btn btn-success btn-sm" onclick="fntDelPago('.$arrData[$i]['idpago'].')" title="Eliminar pago">
									  './*SMONEY.*/$arrData[$i]['abono'].'
									</button>'.'</td>';
									
							}else{
								$trPagos .= '<td>'.$arrData[$i]['abono'].'</td>';
							}
							$trPagos .= '</tr>';
					}
					$arrResponse = array('status' => true, 'data' => $trPagos);
				}
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}
		}
		die();
	}

	public function getPayToday()
	{
		$arrDataC = $this->model->selectPagos2();
		$cobrado = 0;
		for ($i=0; $i < count($arrDataC); $i++)
		{ 
			$cobrado += $arrDataC[$i]['abono'];
		}

		$arrResponse = array('cobrado'=> $cobrado);

		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);

		die();
	}

	public function getSalesToday()
	{
		$arrData = $this->model->selectPrestamos();
		$fecha_actual = date("Y-m-d");
		$ventas = 0;

		for ($i=0; $i < count($arrData); $i++)
		{ 
			if($arrData[$i]['datecreated'] == $fecha_actual)
			{
				$ventas += $arrData[$i]['monto'];
			}
		}

		$arrResponse = array('ventas' => $ventas);

		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);

		die();
	}

	public function delPrestamo()
	{
		if($_POST)
		{
			if($_SESSION['permisosMod']['d']){

				$intIdprestamo = intval($_POST['idPrestamo']);

				$arrDataP = $this->model->selectDatePagoPrestamo();

				$fecha = "";

				if($arrDataP == 2){
					$fecha = date("Y-m-d");
				}else{
					$fecha = $arrDataP['datepago'];					
				}

				$requestDelete = $this->model->deletePrestamo($intIdprestamo, $fecha);
				if($requestDelete)
				{
					$arrResponse = array('status' => true, 'msg' => 'Se ha eliminado el préstamo.');
				}else{
					$arrResponse = array('status' => false, 'msg' => 'Error al eliminar el préstamo.');
				}
				echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);	
			}
		}
		die();
	}

	public function delPago()
	{
		if($_POST)
		{
			if($_SESSION['permisosMod']['d']){
				$intIdPago = intval($_POST['pagoId']);

				//Préstamo total
				//dep($_POST);exit;
				$requestSelect = $this->model->selectTotalPrestamo2($intIdPago);
				$nuevoTotal = ($requestSelect['total'] + $requestSelect['pago']);

				//dep($requestSelect);exit;

				//Préstamo pagado
				$pagado = ($requestSelect['pagado'] - $requestSelect['pago']);

				$datePrestamo = $requestSelect['datecreated'];
				
				$requestDelete = $this->model->deletePago($intIdPago,$nuevoTotal,$pagado,$datePrestamo);
				if($requestDelete)
				{
					$arrResponse = array('status' => true, 'msg' => 'Se ha eliminado el pago.');
				}else if($requestDelete == false){
					$arrResponse = array('status' => false, 'msg' => 'No es posible eliminar el Pagamento.');
				}else{
					$arrResponse = array('status' => false, 'msg' => 'Error en el sistema.');
				}
				echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);	
			}
		}
		die();
	}

	public function delPagoAnterior()
	{
		if($_POST)
		{
			//dep($_POST);exit;
			if($_SESSION['permisosMod']['d']){
				$intIdPago = intval($_POST['pagoId']);

				//Préstamo total
				$requestSelect = $this->model->selectTotalPrestamo2($intIdPago);
				$nuevoTotal = ($requestSelect['total'] + $requestSelect['pago']);

				//Préstamo pagado
				$pagado = ($requestSelect['pagado'] - $requestSelect['pago']);

				$datePrestamo = $requestSelect['datecreated'];
				
				$requestDelete = $this->model->deletePagoAnterior($intIdPago,$nuevoTotal,$pagado,$datePrestamo);
				if($requestDelete)
				{
					$arrResponse = array('status' => true, 'msg' => 'Se ha eliminado el pago.');
				}else if($requestDelete == false){
					$arrResponse = array('status' => false, 'msg' => 'Para eliminar el abono, primero tienes que quitar los préstamos.');
				}else{
					$arrResponse = array('status' => false, 'msg' => 'Error en el sistema.');
				}
				echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);	
			}
		}
		die();
	}
}

?>