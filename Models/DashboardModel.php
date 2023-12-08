<?php 

class DashboardModel extends Mysql
{
	public function __construct()
	{
		parent::__construct();
	}

	PRIVATE $intIdRuta;
	PRIVATE $strFecha;
	PRIVATE $strFecha2;

	public function totalCartera($ruta,$fecha)
	{
		$this->intIdRuta = $ruta;
		$this->strFecha = $fecha;
		$base = 0;
		//$gastos = 0;
		$total = 0;
		$fechaAnterior = $this->selectDatePagoPrestamo();
		//dep($fechaAnterior);exit;
		if($fechaAnterior == 2)
		{
		 	$fechaAnterior = "";
		}else{
			$fechaAnterior = $fechaAnterior['fechaPago'];
		}

		//BASE
		$sqlBa = "SELECT monto as total FROM base WHERE codigoruta = $this->intIdRuta and datecreated = '{$this->strFecha}' ORDER BY datecreated DESC";
		$requestBa = $this->select($sqlBa);

		if(!empty($requestBa))
		{
			$base = $requestBa;
		}else{
			if($fechaAnterior != "")
			{
				$sqlBa = "SELECT monto as total FROM base WHERE codigoruta = $this->intIdRuta and datecreated = '{$fechaAnterior}' ORDER BY datecreated DESC";
		 		$requestBa = $this->select($sqlBa);
				
				if(!empty($requestBa))
				{
					$base = $requestBa;
				}else{
					$base = $this->selectResumenAnterior($this->intIdRuta);	
				}
			}else{
				$base = $this->selectResumenAnterior($this->intIdRuta);
			}
		}

		//GASTOS
		$sqlGa = "SELECT SUM(monto) AS totalGa FROM gastos WHERE codigoruta = $this->intIdRuta AND datecreated = '{$this->strFecha}' AND nombre != ''";
		$requestGa = $this->select($sqlGa);
		
		if(!empty($requestGa['totalGa']))
		{
			$gastos = $requestGa;
		}else{
			if($fechaAnterior != "")
			{
				$sqlGa = "SELECT SUM(monto) as totalGa FROM gastos WHERE codigoruta = $this->intIdRuta and datecreated = '{$fechaAnterior}' AND nombre != ''";
				$requestGa = $this->select($sqlGa);
				$gastos = $requestGa;
			}else{
				$gastos['totalGa'] = 0;
			}
		}

		//COBRADO
		$sqlCo = "SELECT SUM(pa.abono) as totalCo FROM pagos pa 
					INNER JOIN prestamos pr ON(pa.prestamoid = pr.idprestamo)
					INNER JOIN persona pe ON(pr.personaid = pe.idpersona)
					WHERE pe.codigoruta = $this->intIdRuta and pa.datecreated = '{$this->strFecha}'";
		$requestCo = $this->select($sqlCo);
		
		if(!empty($requestCo['totalCo']))			
		{
			$cobrado = $requestCo;
		}else{
			if($fechaAnterior != "")
			{
				$sqlCo = "SELECT SUM(pa.abono) as totalCo FROM pagos pa 
					INNER JOIN prestamos pr ON(pa.prestamoid = pr.idprestamo)
					INNER JOIN persona pe ON(pr.personaid = pe.idpersona)
					WHERE pe.codigoruta = $this->intIdRuta and pa.datecreated = '{$fechaAnterior}'";
				$requestCo = $this->select($sqlCo);
				$cobrado = $requestCo;
			}else{
				$cobrado['totalCo'] = 0;
			}
		}

		//VENTAS
		$sqlVe = "SELECT SUM(pr.monto) as totalVe FROM prestamos pr 
					INNER JOIN persona pe ON(pr.personaid = pe.idpersona) 
					WHERE pe.codigoruta = $this->intIdRuta and pr.datecreated = '{$this->strFecha}' AND pr.status != 0";
		$requestVe = $this->select($sqlVe);
		
		if(!empty($requestVe['totalVe']))
		{
			$ventas = $requestVe;
		}else{
			if($fechaAnterior != "")
			{
				$sqlVe = "SELECT SUM(pr.monto) as totalVe FROM prestamos pr 
					INNER JOIN persona pe ON(pr.personaid = pe.idpersona) 
					WHERE pe.codigoruta = $this->intIdRuta and pr.datecreated = '{$fechaAnterior}' AND pr.status = 1";
				$requestVe = $this->select($sqlVe);
				$ventas = $requestVe;
			}else{
				$ventas['totalVe'] = 0;
			}
			
		}		

		//TOTAL
		$total = ($base['total'] + $cobrado['totalCo']) - ($ventas['totalVe'] + $gastos['totalGa']);
		
		//dep($base['total'].' - '.$cobrado['totalCo'].' - '.$ventas['totalVe']. ' - ' .$gastos['totalGa']. ' - ' .$total);exit;
		
		return $total;
	}

	public function cantUsuarios()
	{
		$whereAdmin = "";
		$rutaId = $_SESSION['idRuta'];
		if($_SESSION['idUser'] != 1){
			$whereAdmin = " and idpersona != 1";
		}
		$sql = "SELECT COUNT(*) AS total FROM persona WHERE status = 1 AND (rolid = 1 || rolid = 3) AND codigoruta = $rutaId".$whereAdmin;
		$request = $this->select($sql);
		$total = $request['total'];
		return $total;
	}

	public function cantClientes()
	{
		$rutaId = $_SESSION['idRuta'];
		$sql = "SELECT COUNT(*) AS total FROM persona WHERE codigoruta = $rutaId AND status != 0 AND rolid = ".RCLIENTES;
		$request = $this->select($sql);
		$total = $request['total'];
		return $total;
	}
	public function cantPrestamos()
	{
		$rutaId = $_SESSION['idRuta'];
		$uno = 0;
		$sql = "SELECT pe.nombres, pr.total FROM prestamos pr INNER JOIN persona pe ON(pr.personaid = pe.idpersona) WHERE pr.status = 1 AND pe.codigoruta = $rutaId";
		$request = $this->select_all($sql);
		$ids = array_column($request, 'nombres');
		$ids = array_unique($ids);
		$array = array_filter($request, function ($key, $value) use ($ids) {
			return in_array($value, array_keys($ids));
		}, ARRAY_FILTER_USE_BOTH);

		return count($array);
	}

	public function cantPrestamosFinalizados()
	{
		$ruta = $_SESSION['idRuta'];
		$sql = "SELECT COUNT(*) AS total FROM prestamos pr INNER JOIN persona pe ON(pr.personaid = pe.idpersona) WHERE pr.status = 2 AND pe.codigoruta = $ruta";
		$request = $this->select($sql);
		$total = $request['total'];
		return $total;
	}

	public function ultimosPrestamo()
	{
		$rutaId = $_SESSION['idRuta'];
		$sql = "SELECT p.idprestamo, pr.nombres,pr.apellidos, p.monto, DATE_FORMAT(p.datecreated, '%d/%m/%Y') as datecreated, p.status FROM prestamos p 
				INNER JOIN persona pr ON (p.personaid = pr.idpersona) where p.status = 1 AND pr.codigoruta = $rutaId ORDER BY p.idprestamo DESC LIMIT 6";
		$request = $this->select_all($sql);
		return $request;
	}

	public function ultimosResumenes()
	{
		$rutaId = $_SESSION['idRuta'];
				$sql = "SELECT re.idresumen, ba.monto as base ,ga.nombre,ga.monto as gastos ,re.ventas,re.cobrado ,re.total, re.datecreated
						FROM resumen re 
						INNER JOIN base ba ON re.baseid = ba.idbase 
						INNER JOIN gastos ga ON re.gastoid = ga.idgasto WHERE re.codigoruta = $rutaId ORDER BY datecreated DESC LIMIT 6";
		$request = $this->select_all($sql);
		return $request;
	}

	public function selectVentasMes(int $anio, int $mes)
	{
		$totalVentasMes = 0;
		$arrVentasDias = array();
		$rutaId = $_SESSION['idRuta'];
		$dias = cal_days_in_month(CAL_GREGORIAN,$mes,$anio);
		$n_dia = 1;
		for ($i=0; $i < $dias; $i++)
		{ 
			$date = date_create($anio.'-'.$mes.'-'.$n_dia);
			$fechaVenta = date_format($date, "Y-m-d");
			//$sql = "SELECT DAY(datecreated) as dia, COUNT(idprestamo) as cantidad, SUM(monto) as total FROM prestamos WHERE DATE(datecreated) = '$fechaVenta' AND status = 1";
			$sql = "SELECT DAY(pr.datecreated) as dia, idprestamo, monto FROM prestamos pr INNER JOIN
												 persona pe ON(pe.idpersona = pr.personaid) WHERE 
												 DATE(pr.datecreated) = '$fechaVenta' AND pr.status != 0 AND pe.codigoruta = $rutaId";
			$ventaDia = $this->select($sql);

			$sqlCantidad = "SELECT COUNT(pr.idprestamo) as cantidad FROM prestamos pr INNER JOIN
												 persona pe ON(pe.idpersona = pr.personaid) WHERE 
												 DATE(pr.datecreated) = '$fechaVenta' AND pr.status != 0 AND pe.codigoruta = $rutaId";
			$ventaDiaCantidad = $this->select($sqlCantidad);

			$sqlTotal = "SELECT SUM(pr.monto) as total FROM prestamos pr INNER JOIN
												 persona pe ON(pe.idpersona = pr.personaid) WHERE 
												 DATE(pr.datecreated) = '$fechaVenta' AND pr.status != 0 AND pe.codigoruta = $rutaId";
			$ventaDiaTotal = $this->select($sqlTotal);

			$ventaDia['dia'] = $n_dia;
			$ventaDiaTotal['total'] = $ventaDiaTotal['total'] == "" ? 0 : $ventaDiaTotal['total'];
			$totalVentasDia = $ventaDiaTotal['total'];
			$ventaDia['idprestamo'] = $ventaDiaCantidad;
			$ventaDia['monto'] = $ventaDiaTotal;
			$totalVentasMes += $totalVentasDia;
			array_push($arrVentasDias, $ventaDia);
			$n_dia++;
			
		}
		$meses = Meses();
		$arrData = array('anio' => $anio, 'mes' => $meses[intval($mes - 1)], 'total' => $totalVentasMes, 'ventas' => $arrVentasDias);
		//dep($arrData);exit;
		return $arrData;
	}

	public function selectCobradoMes(int $anio, int $mes)
	{
		$totalCobradoMes = 0;
		$arrVentasDias = array();
		$rutaId = $_SESSION['idRuta'];
		$dias = cal_days_in_month(CAL_GREGORIAN,$mes,$anio);
		$n_dia = 1;
		for ($i=0; $i < $dias; $i++)
		{ 
			$date = date_create($anio.'-'.$mes.'-'.$n_dia);
			$fechaVenta = date_format($date, "Y-m-d");
			//$sql = "SELECT DAY(datecreated) as dia, COUNT(idprestamo) as cantidad, SUM(monto) as total FROM prestamos WHERE DATE(datecreated) = '$fechaVenta' AND status = 1";
			$sql = "SELECT DAY(datecreated) as dia, cobrado FROM resumen WHERE DATE(datecreated) = '$fechaVenta' AND codigoruta = $rutaId";
			$ventaDia = $this->select($sql);

			$sqlTotal = "SELECT SUM(cobrado) as total FROM resumen WHERE DATE(datecreated) = '$fechaVenta' AND codigoruta = $rutaId";
			$cobradoDiaTotal = $this->select($sqlTotal);
			$cobradoDiaTotal = $cobradoDiaTotal['total'];

			$ventaDia['dia'] = $n_dia;
			$ventaDia['cobrado'] = $cobradoDiaTotal;
			$ventaDia['cobrado'] = $ventaDia['cobrado'] == "" ? 0 : $ventaDia['cobrado'];
			$totalCobradoMes += $cobradoDiaTotal;
			array_push($arrVentasDias, $ventaDia);
			$n_dia++;
			
		}
		$meses = Meses();
		$arrData = array('anio' => $anio, 'mes' => $meses[intval($mes - 1)], 'total' => $totalCobradoMes, 'ventas' => $arrVentasDias);
		return $arrData;
	}

	public function selectGastosMes(int $anio, int $mes)
	{
		$totalCobradoMes = 0;
		$arrVentasDias = array();
		$rutaId = $_SESSION['idRuta'];
		$dias = cal_days_in_month(CAL_GREGORIAN,$mes,$anio);
		$n_dia = 1;
		for ($i=0; $i < $dias; $i++)
		{ 
			$date = date_create($anio.'-'.$mes.'-'.$n_dia);
			$fechaVenta = date_format($date, "Y-m-d");
			//$sql = "SELECT DAY(datecreated) as dia, COUNT(idprestamo) as cantidad, SUM(monto) as total FROM prestamos WHERE DATE(datecreated) = '$fechaVenta' AND status = 1";
			$sql = "SELECT DAY(datecreated) as dia FROM gastos WHERE DATE(datecreated) = '$fechaVenta' AND codigoruta = $rutaId";
			$ventaDia = $this->select($sql);

			$sqlTotal = "SELECT SUM(monto) as total FROM gastos WHERE nombre != '' AND DATE(datecreated) = '$fechaVenta' AND codigoruta = $rutaId";
			$cobradoDiaTotal = $this->select($sqlTotal);
			$cobradoDiaTotal = $cobradoDiaTotal['total'];

			$ventaDia['dia'] = $n_dia;
			$ventaDia['gasto'] = $cobradoDiaTotal;
			$ventaDia['gasto'] = $ventaDia['gasto'] == "" ? 0 : $ventaDia['gasto'];
			$totalCobradoMes += $cobradoDiaTotal;
			array_push($arrVentasDias, $ventaDia);
			$n_dia++;
			
		}
		$meses = Meses();
		$arrData = array('anio' => $anio, 'mes' => $meses[intval($mes - 1)], 'total' => $totalCobradoMes, 'gastos' => $arrVentasDias);
		return $arrData;
	}

	public function selectCartera()
	{
		$fecha_actual = date("Y-m-d");
		$rutaId = $_SESSION['idRuta'];
		$sql = "SELECT SUM(pr.total) as total, 
		SUM(pr.parcela) as parcela, 
		SUM(pr.monto) as monto 
		FROM prestamos pr INNER JOIN 
		persona pe ON(pe.idpersona = pr.personaid) 
		WHERE pr.status = 1 AND pe.codigoruta = $rutaId";
		$request = $this->select($sql);

		$sql2 = "SELECT SUM(pr.parcela) as parcela
		FROM prestamos pr INNER JOIN 
		persona pe ON(pe.idpersona = pr.personaid) 
		WHERE (pr.status = 1 || (pr.datefinal = '$fecha_actual')) AND pr.formato = 1 AND pe.codigoruta = $rutaId";
		$request2 = $this->select($sql2);

		$arrData = array('total' => $request['total'], 'monto' => $request['monto'], 'parcela' => $request2['parcela']);
		return $arrData;
	}

	public function selectResumenAnterior()
	{
		$rutaId = $_SESSION['idRuta'];
		$sql = "SELECT total FROM resumen WHERE codigoruta = $rutaId ORDER BY datecreated DESC";
		$request = $this->select($sql);
		return $request;
	}	

	public function selectCobradoD(string $fechaI, string $fechaF, int $ruta)
	{
		$this->strFecha = $fechaI;
		$this->strFecha2 = $fechaF;
		$this->intIdRuta = $ruta;
		$contador = 1;
		$arrDatos = array();

		$sql = "SELECT datecreated, cobrado FROM resumen 
											WHERE datecreated 
											BETWEEN '{$this->strFecha}' AND '{$this->strFecha2}' 
											AND codigoruta = $ruta";
		$request = $this->select_all($sql);

		foreach ($request as $cobrado)
		{
			$clientes = $cobrado['datecreated'];
			$clientes .= " | ";
			$clientes .= $cobrado['cobrado'];
			$clientes .= " | ";
			$clientes .= forClientesPagos($cobrado['datecreated']);
			array_push($arrDatos, $clientes);
			$contador++;
			
		}

		$arrData = array("cobrado" => $arrDatos);
		
		//dep($arrData);exit;

		return $arrData;
		
	}

	public function selectVentasD(string $fechaI, string $fechaF, int $ruta)
	{
		$this->strFecha = $fechaI;
		$this->strFecha2 = $fechaF;
		$this->intIdRuta = $ruta;
		$arrDatos = array();

		$sql = "SELECT datecreated, ventas FROM resumen 
											WHERE datecreated 
											BETWEEN '{$this->strFecha}' AND '{$this->strFecha2}' 
											AND codigoruta = $ruta";
		$request = $this->select_all($sql);

		foreach ($request as $ventas)
		{
			$clientes = $ventas['datecreated'];
			$clientes .= " | ";
			$clientes .= $ventas['ventas'];
			$clientes .= " | ";
			$clientes .= forClientesVentas($ventas['datecreated']);
			array_push($arrDatos, $clientes);
		}

		$arrData = array("ventas" => $arrDatos);
		
		return $arrData;
		
	}

	public function selectGastosD(string $fechaI, string $fechaF, int $ruta)
	{
		$this->strFecha = $fechaI;
		$this->strFecha2 = $fechaF;
		$this->intIdRuta = $ruta;
		$arrDatos = array();

		$sql = "SELECT re.datecreated, ga.monto FROM resumen re INNER JOIN gastos ga ON(re.gastoid = ga.idgasto) 
											WHERE re.datecreated 
											BETWEEN '{$this->strFecha}' AND '{$this->strFecha2}' 
											AND re.codigoruta = $ruta";
		$request = $this->select_all($sql);

		//dep($request);exit;

		foreach ($request as $gastos)
		{
			$gastosD = $gastos['datecreated'];
			$gastosD .= " | ";
			$gastosD .= $gastos['monto'];
			$gastosD .= " | ";
			$gastosD .= forGastoResumen($gastos['datecreated']);
			array_push($arrDatos, $gastosD);
		}

		$arrData = array("gastos" => $arrDatos);
		
		return $arrData;
		
	}

	public function selectDatePagoPrestamo()
	{
		$ruta = $_SESSION['idRuta'];
		$fecha_actual = date("Y-m-d");

		$sqlR = "SELECT datecreated FROM resumen WHERE codigoruta = $ruta AND datecreated != '$fecha_actual' ORDER BY datecreated DESC";
		$requestR = $this->select($sqlR);

		//dep($requestR);exit;

		// $sql = "SELECT * FROM prestamos pr INNER JOIN persona pe ON(pr.personaid = pe.idpersona) 
		// 		WHERE (pr.pagoid != '' AND pr.datepago != '$fecha_actual') AND (pe.codigoruta = $ruta AND pr.status != 0)";
		$sql = "SELECT pa.datecreated as fechaPago FROM prestamos pr 
					INNER JOIN persona pe ON(pr.personaid = pe.idpersona) 
					INNER JOIN pagos pa ON(pr.idprestamo = pa.prestamoid)
					WHERE (pa.datecreated != '$fecha_actual') AND (pe.codigoruta = $ruta AND pr.status != 0)
					ORDER BY pa.datecreated desc";
		$request = $this->select($sql);

		//dep($request);exit;

		if(!empty($request) && ($request['fechaPago'] > $requestR['datecreated']))
		{
			return $request;
		}else{
			return 2;
		}
	}
}