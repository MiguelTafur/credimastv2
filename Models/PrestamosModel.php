<?php 

	class PrestamosModel extends Mysql
	{
		PRIVATE $fecha_actual;
		PRIVATE $intIdPrestamo;
		PRIVATE $intIdCliente;
		PRIVATE $intIdResumen;
		PRIVATE $intMonto;
		PRIVATE $intTotal;
		PRIVATE $intFormato;
		PRIVATE $intPlazo;
		PRIVATE $intTaza;
		PRIVATE $intParcela;
		PRIVATE $intPago;
		PRIVATE $intPagado;
		PRIVATE $intIdPago;
		PRIVATE $strObservacion;
		PRIVATE $intStatus;
		PRIVATE $intBase;
		PRIVATE $intGasto;
		PRIVATE $strNombre;
		PRIVATE $intPosicion;

		public function __construct()
		{
			parent::__construct();
		}

		public function insertPago(int $idprestamo, int $monto, string $fecha)
		{
			$this->fecha_actual = $fecha;
			$this->intIdPrestamo = $idprestamo;
			$this->intMonto = $monto;
			$return = 0;

			$sql = "SELECT * FROM pagos WHERE prestamoid = '{$this->intIdPrestamo}' AND datecreated = '{$this->fecha_actual}'";
			$request = $this->select_all($sql);

			if(empty($request))
			{
				$query_insert = "INSERT INTO pagos(prestamoid,abono,datecreated) VALUES(?,?,?)";
				$arrData = array($this->intIdPrestamo,$this->intMonto,$this->fecha_actual);
				$request_insert = $this->insert($query_insert,$arrData);
				$return = $request_insert;
			}else
			{
				$return = "0";
			}
			return $return;
		}

		public function selectPrestamos()
		{
			$fecha_actual = date('Y-m-d');		
			$ruta = $_SESSION['idRuta'];
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
						pr.fechavence,
						pr.datefinal,
						pr.pagoid,
						pr.pago,
						pr.datepago,
						pr.status,
						pr.orden
					FROM prestamos pr 
					INNER JOIN persona pe 
					ON (pr.personaid = pe.idpersona)
					WHERE (pe.codigoruta = $ruta and pr.status = 1) or (pe.codigoruta = $ruta AND pr.status = 2 and pr.datefinal = '{$fecha_actual}') ORDER BY orden";
			$request = $this->select_all($sql);
			return $request;
		}

		public function selectVentas()
		{
			$fecha_actual = date('Y-m-d');		
			$ruta = $_SESSION['idRuta'];
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
						pr.fechavence,
						pr.datefinal,
						pr.pagoid,
						pr.pago,
						pr.datepago,
						pr.status,
						pr.orden
					FROM prestamos pr 
					INNER JOIN persona pe 
					ON (pr.personaid = pe.idpersona)
					WHERE pe.codigoruta = $ruta and pr.status != 0 ORDER BY pr.datecreated desc";
			$request = $this->select_all($sql);
			return $request;
		}

		public function selectPrestamo(int $idprestamo)
		{
			$fecha_actual = date('Y-m-d');
			$this->intIdPrestamo = $idprestamo;
			$sql = "SELECT pr.idprestamo,
						   pe.nombres,
						   pe.apellidos,
						   pr.total,
						   pr.monto,
						   pr.formato,
						   pr.taza,
						   pr.plazo,
						   pr.parcela,
						   pr.pagado,
						   pr.observacion,
						   pr.datecreated,
						   pr.fechavence
						FROM prestamos pr 
						INNER JOIN persona pe 
						ON pr.personaid = pe.idpersona 
						WHERE (pr.idprestamo = $this->intIdPrestamo AND pr.status = 1) or (pr.datefinal = '{$fecha_actual}' and pr.status = 2 and pr.idprestamo = $this->intIdPrestamo)";
			$request = $this->select($sql);
			return $request;
		}

		public function selectCodigoPrestamo(int $idprestamo)
		{
			$this->intIdPrestamo = $idprestamo;
			$sql = "SELECT idprestamo,pago FROM prestamos WHERE idprestamo = $this->intIdPrestamo";
			$request = $this->select($sql);
			return $request;
		}

		public function selectTotalPrestamo(int $idprestamo)
		{
			$this->intIdPrestamo = $idprestamo;
			$sql = "SELECT total,pagado,pago,datepago FROM prestamos WHERE idprestamo = $this->intIdPrestamo";
			$request = $this->select($sql);
			return $request;
		}

		public function selectTotalPrestamo2(int $pagoid)
		{
			$this->intIdPago = $pagoid;
			$sql = "SELECT total,pagado,pago,datecreated FROM prestamos WHERE pagoid = $this->intIdPago";
			$request = $this->select($sql);
			return $request;
		}

		public function selectPago(int $idprestamo)
		{
			$fecha_actual = date("Y-m-d");
			$this->intIdPrestamo = $idprestamo;
			$sql = "SELECT prestamoid,abono,datecreated FROM pagos WHERE prestamoid = $this->intIdPrestamo AND datecreated = '{$fecha_actual}'";
			$request = $this->select($sql);	
			return $request;
		}

		public function selectPagos(int $idprestamo)
		{
			$this->intIdPrestamo = $idprestamo;
			$sql = "SELECT idpago,abono,datecreated FROM pagos WHERE prestamoid = $this->intIdPrestamo ORDER BY datecreated DESC";
			$request = $this->select_all($sql);
			return $request;
		}

		public function selectPagos2()
		{
			$fecha_actual = date("Y-m-d");
			$ruta = $_SESSION['idRuta'];
			$sql = "SELECT pa.abono FROM pagos pa 
					INNER JOIN prestamos pr ON(pa.prestamoid = pr.idprestamo) 
					INNER JOIN persona pe ON pe.idpersona = pr.personaid 
					WHERE pe.codigoruta = $ruta AND pa.datecreated = '{$fecha_actual}'";
			$request = $this->select_all($sql);
			return $request;
		}

		public function selectPagoAnterior(int $id, string $fecha)
		{
			$this->fecha_actual = $fecha;
			$this->intIdPago = $id;

			$sql = "SELECT * FROM pagos WHERE idpago = $this->intIdPago";
			$request = $this->select_all($sql);
			if($request)
			{
				return true;
			}else{
				return false;
			}
		}

		public function selectGastos(string $fecha = NULL)
		{
			$this->fecha_actual = $fecha;
			$ruta = $_SESSION['idRuta'];
			$sql = "SELECT idgasto,nombre,monto,datecreated FROM gastos WHERE codigoruta = $ruta AND datecreated = '{$this->fecha_actual}'";
			$request = $this->select_all($sql);
			return $request;
		}

		public function selectResumen()
		{
			$fecha_actual = date("Y-m-d");
			$ruta = $_SESSION['idRuta'];
			$sql = "SELECT idresumen,baseid,gastoid,datecreated FROM resumen WHERE codigoruta = $ruta AND datecreated = '{$fecha_actual}'";
			$request = $this->select($sql);
			return $request;
		}

		public function selectClientesPagos(string $fecha)
		{
			$this->fecha_actual = $fecha;
			$ruta = $_SESSION['idRuta'];
			$sql = "SELECT pa.abono,pe.nombres FROM prestamos pr INNER JOIN pagos pa ON pr.idprestamo = pa.prestamoid
																 INNER JOIN persona pe ON pr.personaid = pe.idpersona 
																 WHERE pe.codigoruta = $ruta AND pa.datecreated = '{$this->fecha_actual}' ORDER BY pr.orden";
			$request = $this->select_all($sql);
			return $request;
		}

		public function selectClientesVentas(string $fecha)
		{
			$this->fecha_actual = $fecha;
			$ruta = $_SESSION['idRuta'];
			$sql = "SELECT pe.nombres, pr.monto FROM prestamos pr INNER JOIN persona pe ON pr.personaid = pe.idpersona 
															 WHERE pe.codigoruta = $ruta AND pr.datecreated = '{$this->fecha_actual}' AND pr.status != 0 ORDER BY pr.orden";
			$request = $this->select_all($sql);
			return $request;
		}

		public function updateTotalPrestamo(int $idprestamo, int $idpago, int $total, int $pagado, int $pago, string $datepago)
		{
			$this->intIdPrestamo = $idprestamo;
			$this->intPagado = $pagado;
			$this->intIdPago = $idpago;
			$this->intTotal = $total;
			$this->intPago = $pago;
			$this->fecha_actual = $datepago;
			$return = 0;

			$sql = "SELECT idprestamo FROM prestamos WHERE idprestamo = '{$this->intIdPrestamo}' AND status = 1";
			$request = $this->select_all($sql);

			if(!empty($request))
			{
				$query_update = "UPDATE prestamos SET total = ?, pagado = ?, pagoid = ?, pago = ?, datepago = ? WHERE idprestamo = '{$this->intIdPrestamo}'";
				$arrData = array($this->intTotal,$this->intPagado,$this->intIdPago,$this->intPago,$this->fecha_actual);
				$request = $this->update($query_update,$arrData);
				$return = $request;
			}else{
				$return = "0";
			}
			return $return;
		}

		public function updateStatusPrestamo(int $idprestamo, string $fechaFinal, int $status)
		{
			$this->intIdPrestamo = $idprestamo;
			$this->intStatus = $status;
			$this->fecha_actual = $fechaFinal;
			$return = 0;

			if($this->intStatus == 2)
			{
				$query_update = "UPDATE prestamos SET datefinal = ?, status = ? WHERE idprestamo = $this->intIdPrestamo";
				$arrData = array($this->fecha_actual,$this->intStatus);
				$request = $this->update($query_update,$arrData);
				$return = $request;
			}else{
				$return = "0";
			}
			return $return;
		}

		public function updateListPrestamo(int $pos, int $idprestamo)
		{
			$this->intPosicion = $pos;
			$this->intIdPrestamo = $idprestamo;

			$query_update = "UPDATE prestamos SET orden = ? WHERE idprestamo = $this->intIdPrestamo";
			$arrData = array($this->intPosicion);
			$request = $this->update($query_update,$arrData);
			return $request;
		}

		public function deletePrestamo(int $idprestamo, string $fecha = NULL)
		{
			$this->fecha_actual = $fecha;
			$this->intIdPrestamo = $idprestamo;

			$sql = "UPDATE prestamos SET status = ? WHERE idprestamo = $this->intIdPrestamo";
			$arrData = array(0);
			$request = $this->update($sql, $arrData);

			$sqlP = "SELECT * FROM pagos WHERE prestamoid = $this->intIdPrestamo";
			$requestP = $this->select($sqlP);
			if(!empty($requestP))
			{
				$sqlPD = "DELETE FROM pagos where prestamoid = $this->intIdPrestamo";
				$requestPD = $this->delete($sqlPD);
			}else{
				$requestPD = true;
			}
			
			$return = $requestPD;

			return $return;
		}

		public function deletePago(int $pagoid, int $nuevoTotal, int $pagado)
		{
			$this->intIdPago = $pagoid;
			$this->intTotal = $nuevoTotal;
			$this->intPagado = $pagado;
			$ruta = $_SESSION['idRuta'];
			$fecha_actual = date("Y-m-d");

			$sqlR = "SELECT * FROM resumen WHERE codigoruta = $ruta AND datecreated = '{$fecha_actual}'";
			$request = $this->select_all($sqlR);
			if(empty($request))
			{
				$sql = "DELETE FROM pagos WHERE idpago = $this->intIdPago";
				$request = $this->delete($sql);
				if(!empty($request)){
					$sqlU = "UPDATE prestamos SET total = ?, pagado = ?, pagoid = ?, datefinal = ?, status = ? WHERE pagoid = $this->intIdPago";
					$arrData = array($this->intTotal,$this->intPagado,NULL,NULL,1);
					$requestU = $this->update($sqlU, $arrData);
					$return = $requestU;
				}else{
					$return = "0";
				}
			}else{
				$return = "0";
			}
			
			return $return;
		}

		public function deletePagoAnterior(int $pagoid, int $nuevoTotal, int $pagado)
		{
			$this->intIdPago = $pagoid;
			$this->intTotal = $nuevoTotal;
			$this->intPagado = $pagado;
			$ruta = $_SESSION['idRuta'];
			$fecha_actual = date("Y-m-d");

			$sqlR = "SELECT * FROM resumen WHERE codigoruta = $ruta AND datecreated = '{$fecha_actual}'";
			$request = $this->select_all($sqlR);
			if(empty($request))
			{
				$sql = "DELETE FROM pagos WHERE idpago = $this->intIdPago";
				$request = $this->delete($sql);
				if(!empty($request)){
					$sqlU = "UPDATE prestamos SET total = ?, pagado = ?, pagoid = ?, pago = ?, datefinal = ?, status = ? WHERE pagoid = $this->intIdPago";
					$arrData = array($this->intTotal,$this->intPagado,NULL,NULL,NULL,1);
					$requestU = $this->update($sqlU, $arrData);
					$return = $requestU;
				}else{
					$return = "0";
				}
			}else{
				$return = "0";
			}
			
			return $return;
		}

		public function selectPrestamos2(string $fecha = NULL)
		{
			$fechaPendiente = $this->selectDatePagoPrestamo()['datepago'];
			$ruta = $_SESSION['idRuta'];
			$sql = "SELECT 
						pr.idprestamo, 
						pr.personaid,
						pe.nombres,
						pe.apellidos,
						pr.monto,
						pr.formato,
						pr.taza,
						pr.plazo,
						pr.total,
						pr.datecreated,
						pr.fechavence,
						pr.datefinal,
						pr.pagoid,
						pr.pago,
						pr.datepago,
						pr.status,
						pr.orden
					FROM prestamos pr 
					INNER JOIN persona pe ON (pr.personaid = pe.idpersona)
					WHERE (pe.codigoruta = $ruta and pr.status = 1) or (pe.codigoruta = $ruta AND pr.status = 2 and pr.datefinal = '{$fechaPendiente}') ORDER BY orden";
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
									 WHERE pe.codigoruta = $ruta AND pr.status != 0";
			$requestPa = $this->select_all($sqlPa);
			$arrData = array('prestamos' => $request, 'pagos' => $requestPa);
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
			$sql = "SELECT pa.datecreated as datepago FROM prestamos pr 
						INNER JOIN persona pe ON(pr.personaid = pe.idpersona) 
						INNER JOIN pagos pa ON(pr.idprestamo = pa.prestamoid)
						WHERE (pa.datecreated != '$fecha_actual') AND (pe.codigoruta = $ruta AND pr.status != 0)
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