<?php 

	class PrestamosFinalizados extends Controllers
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

		public function PrestamosFinalizados()
		{
			if(empty($_SESSION['permisosMod']['r'])){
				header("Location: ".base_url().'/prestamos');
			}
			$data['page_tag'] = "Prestamos Finalizados";
			$data['page_title'] = "PRÉSTAMOS FINZALIZADOS";
			$data['page_name'] = "finalizados";
			$data['pagamentos'] = $this->model->selectDatePagoPrestamo();
			$data['page_functions_js'] = "functions_prestamosFinalizados.js";
			$this->views->getView($this,"prestamosFinalizados",$data);
		}

		public function getPrestamosFinalizados()
		{
			$arrData = $this->model->selectPrestamosFinalizados();
			for ($i=0; $i < count($arrData); $i++)
			{ 
				$btnView = '';
				$btnAbono = '';
				
				$arrData[$i]['nombres'] = strtok($arrData[$i]['nombres'], " ").' - <i>'.$arrData[$i]['apellidos'].'</i>';

				if($_SESSION['permisosMod']['w'])
				{
					$btnView = '<button class="btn btn-info btn-sm" onclick="fntViewPrestamoFinalizado('.$arrData[$i]['idprestamo'].')" title="Ver Préstamo"><i class="far fa-eye"></i></button>';
				}
				if($_SESSION['permisosMod']['d'])
				{
					$btnAbono = '<button class="btn btn-secondary btn-sm" onclick="fntlistPagosFinalizados('.$arrData[$i]['idprestamo'].')" title="Ver abonos"><i class="fas fa-hand-holding-usd"></i></button>';
				}
				$arrData[$i]['abonos'] = '<div class="text-center">'.$btnAbono.'</div>';
				$arrData[$i]['options'] = '<div class="text-center">'.$btnView.'</div>';
			}
			echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		}
		
		public function getPrestamoFinalizado($idprestamo)
		{
			if($_SESSION['permisosMod']['r']){
				$idPrestamo = intval($idprestamo);
				if($idPrestamo > 0){
					$arrData = $this->model->selectPrestamoFinalizado($idPrestamo);
					//$arrData['datecreated'] = date("d-m-Y", strtotime($arrData['datecreated']));
					$arrData['taza'] = $arrData['taza'].' '.'%';
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
			if($_SESSION['permisosMod']['r'])
			{
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
										  '.SMONEY.$arrData[$i]['abono'].' &nbsp;
										</button>'.'</td>';
										
								}else{
									$trPagos .= '<td>'.SMONEY.$arrData[$i]['abono'].'</td>';
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

		public function delPago()
		{
			if($_POST)
			{
				if($_SESSION['permisosMod']['d']){
					$intIdPago = intval($_POST['pagoId']);
					//Préstamo total
					$requestSelect = $this->model->selectTotalPrestamo2($intIdPago);
					$nuevoTotal = ($requestSelect['total'] + $requestSelect['pago']);
					//Préstamo pagado
					$pagado = ($requestSelect['pagado'] - $requestSelect['pago']);
					
					$requestDelete = $this->model->deletePago($intIdPago,$nuevoTotal,$pagado);
					if($requestDelete)
					{
						$arrResponse = array('status' => true, 'msg' => 'Se ha eliminado el pago.');
					}else if($requestDelete == '0'){
						$arrResponse = array('status' => false, 'msg' => 'Error al eliminar el pago.');
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