<?php 

	class ResumenModel extends Mysql
	{
		PRIVATE $fecha_actual;
		PRIVATE $intIdResumen;
		PRIVATE $strNombre;
		PRIVATE $intBase;
		PRIVATE $intGasto;
		PRIVATE $intMonto;
		PRIVATE $intPago;
		PRIVATE $intTotal;
		PRIVATE $strObservacion;
		PRIVATE $intRuta;

		public function __construct()
		{
			parent::__construct();
		}	

		public function selectResumen(int $ruta)
		{
			$fecha_actual = date("Y-m-d");
			$this->intRuta = $ruta;
			$sql = "SELECT idresumen,baseid,gastoid,codigoruta,datecreated FROM resumen WHERE datecreated = '{$fecha_actual}' AND codigoruta = $this->intRuta";
			$request = $this->select_all($sql);
			return $request;
		}

		public function selectResumenAnterior(int $ruta)
		{
			$this->intRuta = $ruta;
			$sql = "SELECT total FROM resumen WHERE codigoruta = $this->intRuta order by datecreated desc";
			$request = $this->select($sql);
			return $request;
		}

		public function selectCartera(int $ruta)
		{
			$this->intRuta = $ruta;
			$sql = "SELECT pr.total,pr.parcela FROM prestamos pr INNER JOIN persona pe ON(pr.personaid = pe.idpersona) where pr.status = 1 AND pe.codigoruta = $this->intRuta";
			$request = $this->select_all($sql);
			return $request;
		}

		public function selectPagos2(int $ruta)
		{
			$fecha_actual = date("Y-m-d");
			$this->intRuta = $ruta;
			$sql = "SELECT pa.abono FROM 
					pagos pa INNER JOIN prestamos pr ON(pa.prestamoid = pr.idprestamo) 
					INNER JOIN persona pe ON pe.idpersona = pr.personaid 
					WHERE pe.codigoruta = $this->intRuta AND pa.datecreated = '{$fecha_actual}'";
			$request = $this->select_all($sql);
			
			return $request;
		}

		public function selectPrestamos(int $ruta)
		{
			$this->intRuta = $ruta;
			$fecha_actual = date('Y-m-d');			
			$sql = "SELECT 
						pr.idprestamo, 
						pr.personaid,
						pe.nombres,
						pe.apellidos,
						pr.monto,
						pr.formato,
						pr.taza,
						pr.plazo,
						pr.parcela,
						pr.total,
						pr.datecreated,
						pr.datefinal,
						pr.pagoid,
						pr.pago,
						pr.datepago,
						pr.status,
						pr.orden
					FROM prestamos pr 
					INNER JOIN persona pe 
					ON (pr.personaid = pe.idpersona)
					WHERE (pr.status = 1 AND pe.codigoruta = $this->intRuta) or (pr.status = 2 AND pr.datefinal = '{$fecha_actual}'AND pe.codigoruta = $this->intRuta) ORDER BY orden";
			$request = $this->select_all($sql);
			return $request;
		}

		public function selectBase(string $fecha = NULL)
		{
			$this->fecha_actual = $fecha;
			$ruta = $_SESSION['idRuta'];
			$sql = "SELECT idbase,codigoruta,monto,datecreated FROM base WHERE codigoruta = $ruta AND datecreated = '{$this->fecha_actual}'";
			$request = $this->select($sql);
			return $request;
		}

		public function selectGastos(int $ruta,string $fecha = NULL)
		{
			$this->fecha_actual = $fecha;
			$this->intRuta = $ruta;
			$sql = "SELECT idgasto,nombre,monto,datecreated FROM gastos WHERE codigoruta = $this->intRuta AND datecreated = '{$this->fecha_actual}'";
			$request = $this->select_all($sql);
			return $request;
		}

		public function insertBase(int $ruta, int $base, string $observacion, string $fecha)
		{
			$this->fecha_actual = $fecha;
			$this->intBase = $base;
			$this->strObservacion = $observacion;
			$this->intRuta = $ruta;
			$return = 0;

			$sql = "SELECT datecreated FROM base WHERE datecreated = '{$this->fecha_actual}' AND codigoruta = $this->intRuta";
			$request = $this->select_all($sql);
			if(empty($request)){
				$query_insert = "INSERT INTO base(codigoruta,monto,observacion,datecreated) VALUES(?,?,?,?)";
				$arrData = array($this->intRuta,$this->intBase,$this->strObservacion,$this->fecha_actual);
				$request_insert = $this->insert($query_insert,$arrData);
				$return = $request_insert;
			}else{
				$return = "0";
			}
			return $return;
		}

		public function insertGasto(int $ruta, string $nombre,int $gasto, string $fecha)
		{
			$this->fecha_actual = $fecha;
			$this->intRuta = $ruta;
			$this->strNombre = $nombre;
			$this->intGasto = $gasto;

			$query_insert = "INSERT INTO gastos(codigoruta,nombre,monto,datecreated) VALUES(?,?,?,?)";
			$arrData = array($this->intRuta,$this->strNombre,$this->intGasto,$this->fecha_actual);
			$request_insert = $this->insert($query_insert,$arrData);
			return $request_insert;
		}

		public function insertResumen(int $idbase, int $idgasto, int $ruta, int $cobrado, int $ventas, int $total, string $fecha)
		{
			$this->fecha_actual = $fecha;
			$this->intBase = $idbase;
			$this->intGasto = $idgasto;
			$this->intMonto = $ventas;
			$this->intPago = $cobrado;
			$this->intTotal = $total;
			$this->intRuta = $ruta;

			$query_insert = "INSERT INTO resumen(baseid,gastoid,codigoruta,ventas,cobrado,total,datecreated) VALUES(?,?,?,?,?,?,?)";
			$arrData = array($this->intBase,$this->intGasto,$this->intRuta,$this->intMonto,$this->intPago,$this->intTotal,$this->fecha_actual);
			$request_insert = $this->insert($query_insert,$arrData);
			// if(!empty($request_insert))
			// {
			// 	$sql = "SELECT pe.idpersona, pe.codigoruta FROM prestamos pr INNER JOIN persona pe ON (pr.personaid = pe.idpersona) 
			// 		WHERE ((pr.status =  1 AND pe.codigoruta = $this->intRuta) OR (pr.status = 2 AND pr.datefinal = '{$this->fecha_actual}')) AND pe.codigoruta = $this->intRuta";
			// 	$requestRuta = $this->select_all($sql);

			// 	for ($i=0; $i < count($requestRuta); $i++) { 
			// 		$idPersona = $requestRuta[$i]['idpersona'];
			// 		if($requestRuta[$i]['codigoruta'] == $this->intRuta){
			// 			$query_update = "UPDATE prestamos SET pago = ?, datepago = ? WHERE personaid = $idPersona";
			// 			$arrDataUpdate = array(NULL,NULL);
			// 			$request = $this->update($query_update,$arrDataUpdate);
			// 		}
			// 	}

				return $request_insert;
			// }
		}

		public function deleteBase(int $baseId)
		{
			$this->intBase = $baseId;
			$sql = "DELETE FROM base WHERE idbase = $this->intBase";
			$request = $this->delete($sql);
			return $request;
		}

		public function deleteGasto(int $gastoId)
		{
			$this->intGasto = $gastoId;
			
			$sql = "DELETE FROM gastos WHERE idgasto = $this->intGasto";
			$request = $this->delete($sql);
			return $request;
		}

		public function deleteGastoF(int $gastoId, string $fecha = NULL)
		{
			$this->intGasto = $gastoId;
			$this->fecha_actual = $fecha;
			
			$sql = "DELETE FROM gastos WHERE idgasto = $this->intGasto AND datecreated = '{$this->fecha_actual}'";
			$request = $this->delete($sql);
			return $request;
		}

		public function deleteResumen(int $resumenId, string $fecha_actual)
		{
			$this->intIdResumen = $resumenId;

			$query_resumen = "SELECT * FROM resumen WHERE idresumen = $this->intIdResumen";
			$request_resumen = $this->select($query_resumen);
			$gastos = $request_resumen['gastoid'];
			
			$sqlGastos = "DELETE FROM gastos WHERE idgasto = $gastos AND monto = 0";
			$requestGastos = $this->delete($sqlGastos);
			
			$sql = "DELETE FROM resumen WHERE idresumen = $this->intIdResumen";
			$request = $this->delete($sql);
			if($request)
			{
				$ruta = $_SESSION['idRuta'];

				$query_gastos = "";

				$query_prestamos = "SELECT pa.idpago,pa.abono,pa.datecreated FROM pagos pa 
									INNER JOIN prestamos pr ON(pa.prestamoid = pr.idprestamo) 
									INNER JOIN persona pe ON pe.idpersona = pr.personaid WHERE pe.codigoruta = $ruta AND pa.datecreated = '{$fecha_actual}'";
				$request_prestamos = $this->select_all($query_prestamos);

				//dep($request_prestamos);exit;
				for ($i=0; $i < count($request_prestamos); $i++)
				{
					$id_pago = $request_prestamos[$i]['idpago'];
					$query_update = "UPDATE prestamos SET datepago = ?, pago = ? WHERE pagoid = $id_pago ";
					$arrData = array($request_prestamos[$i]['datecreated'], $request_prestamos[$i]['abono']);
					$requestU = $this->update($query_update, $arrData);					
				}

				return $request;
			}
		}

		public function selectPrestamos2(string $fecha = NULL, $ruta)
		{
			$this->fecha_actual = $fecha;
			$this->intRuta = $ruta;
			$sql = "SELECT 
						pr.idprestamo, 
						pe.nombres,
						pe.apellidos,
						pr.monto,
						pr.total,
						pr.datefinal,
						pr.datecreated,
						pr.status
					FROM prestamos pr 
					INNER JOIN persona pe 
					ON (pr.personaid = pe.idpersona)
					WHERE (pr.datecreated = '{$this->fecha_actual}') AND pr.status != 0 AND pe.codigoruta = $this->intRuta";
			$request = $this->select_all($sql);

			$sqlPa = "SELECT pe.nombres, 
							pe.apellidos, 
							pa.idpago as pagoid, 
							pa.abono as pago, 
							pa.datecreated as datepago, 
							pr.idprestamo, 
							pr.total, 
							pr.status, 
							pr.datefinal 
						FROM pagos pa INNER JOIN prestamos pr ON (pa.prestamoid = pr.idprestamo)
									 INNER JOIN persona pe ON(pe.idpersona = pr.personaid)
									 WHERE pa.datecreated = '{$this->fecha_actual}' AND pe.codigoruta = $ruta AND pr.status != 0 ORDER BY pa.datecreated DESC";
			$requestPa = $this->select_all($sqlPa);

			$arrData = array('prestamos' => $request, 'pagos' => $requestPa);
			//dep($requestPa);exit;
			return $arrData;
		}

		public function selectDatePagoPrestamo()
		{
			$ruta = $_SESSION['idRuta'];
			$fecha_actual = date("Y-m-d");

			$sqlR = "SELECT datecreated FROM resumen WHERE codigoruta = $ruta AND datecreated != '{$fecha_actual}' ORDER BY datecreated DESC";
			$requestR = $this->select($sqlR);

			//dep($requestR);exit;

			// $sql = "SELECT * FROM prestamos pr INNER JOIN persona pe ON(pr.personaid = pe.idpersona) 
			// 		WHERE (pr.pagoid != '' AND pr.datepago != '$fecha_actual') AND (pe.codigoruta = $ruta AND pr.status != 0)";
			$sql = "SELECT pa.datecreated as datepago FROM prestamos pr 
						INNER JOIN persona pe ON(pr.personaid = pe.idpersona) 
						INNER JOIN pagos pa ON(pr.idprestamo = pa.prestamoid)
						WHERE (pa.datecreated != '{$fecha_actual}') AND (pe.codigoruta = $ruta AND pr.status != 0)
						ORDER BY pa.datecreated desc";
			$request = $this->select($sql);

			//dep($request);exit;

			if(!empty($request) && ($request['datepago'] > $requestR['datecreated']))
			{
				return $request;
			}else{
				return 2;
			}
		}
	}
?>