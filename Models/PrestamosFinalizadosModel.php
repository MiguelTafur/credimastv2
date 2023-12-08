<?php 

	class PrestamosFinalizadosModel extends Mysql
	{
		public function __construct()
		{
			parent::__construct();
		}	

		public function selectPrestamosFinalizados()
		{
			$ruta = $_SESSION['idRuta'];
			$sql = "SELECT 
						pr.idprestamo, 
						pr.personaid,
						pe.nombres,
						pe.apellidos,
						pr.monto,
						pr.formato,
						pr.taza,
						pr.parcela,
						pr.plazo,
						DATE_FORMAT(pr.datecreated, '%d/%m/%Y') as datecreated,
						DATE_FORMAT(pr.datefinal, '%d/%m/%Y') as datefinal,
						pr.orden
					FROM prestamos pr 
					INNER JOIN persona pe 
					ON (pr.personaid = pe.idpersona)
					WHERE pr.status = 2 AND pe.codigoruta = $ruta";
			$request = $this->select_all($sql);
			return $request;
		}

		public function selectPrestamoFinalizado(int $idprestamo)
		{
			$this->intIdPrestamo = $idprestamo;
			$ruta = $_SESSION['idRuta'];
			$sql = "SELECT pr.idprestamo,pe.nombres,pe.apellidos,pr.total,pr.monto,pr.formato,pr.taza,pr.parcela,pr.plazo,pr.pagado,pr.observacion, DATE_FORMAT(pr.datecreated, '%d/%m/%Y') as datecreated FROM prestamos pr INNER JOIN persona pe ON pr.personaid = pe.idpersona WHERE pr.idprestamo = $this->intIdPrestamo AND pr.status = 2 AND pe.codigoruta = $ruta";
			$request = $this->select($sql);
			return $request;
		}

		public function selectPagos($idprestamo)
		{
			$this->intIdPrestamo = $idprestamo;
			$sql = "SELECT idpago,abono,datecreated FROM pagos WHERE prestamoid = $this->intIdPrestamo ORDER BY datecreated DESC";
			$request = $this->select_all($sql);
			return $request;
		}

		public function selectTotalPrestamo2(int $pagoid)
		{
			$fecha_actual = date("Y-m-d");
			$this->intIdPago = $pagoid;
			$sql = "SELECT total,pagado,pago FROM prestamos WHERE pagoid = $this->intIdPago";
			$request = $this->select($sql);
			return $request;
		}

		public function deletePago(int $pagoid, int $nuevoTotal, int $pagado)
		{
			$this->intIdPago = $pagoid;
			$this->intTotal = $nuevoTotal;
			$this->intPagado = $pagado;
			$fecha_actual = date("Y-m-d");

			$sqlR = "SELECT * FROM resumen WHERE datecreated = '{$fecha_actual}'";
			$request = $this->select_all($sqlR);
			if(empty($request))
			{
				$sql = "DELETE FROM pagos WHERE idpago = $this->intIdPago";
				$request = $this->delete($sql);
				if(!empty($request)){
					$sqlU = "UPDATE prestamos SET total = ?, pagado = ?, pagoid = ?, pago = ?, datepago = ?, status = ? WHERE pagoid = $this->intIdPago";
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
 ?>