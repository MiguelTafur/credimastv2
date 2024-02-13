<?php 

	class VentasModel extends Mysql
	{
		PRIVATE $fecha_actual;
		PRIVATE $intIdCliente;
		PRIVATE $intFormato;
		PRIVATE $intPlazo;
		PRIVATE $intTaza;
		PRIVATE $intParcela;
		PRIVATE $intMonto;
		PRIVATE $intTotal;
		PRIVATE $strObservacion;
		PRIVATE $strVence;

		public function __construct()
		{
			parent::__construct();
		}	

		public function selectDatePagoPrestamo()
		{
			$ruta = $_SESSION['idRuta'];
			$fecha_actual = date("Y-m-d");

			$sqlR = "SELECT datecreated FROM resumen WHERE codigoruta = $ruta AND datecreated != '$fecha_actual' ORDER BY datecreated DESC";
			$requestR = $this->select($sqlR);

			$sqlPr = "SELECT pe.nombres, pe.apellidos, pr.monto, pr.datecreated FROM prestamos pr INNER JOIN persona pe ON(pr.personaid = pe.idpersona) 
					  WHERE pe.codigoruta = $ruta AND pr.datecreated != '{$fecha_actual}' ORDER BY datecreated DESC";
			$requestPr = $this->select($sqlPr);

			$fechaPrestamo = $requestPr['datecreated'];

			$sql = "SELECT pa.datecreated as datepago FROM prestamos pr 
						INNER JOIN persona pe ON(pr.personaid = pe.idpersona) 
						INNER JOIN pagos pa ON(pr.idprestamo = pa.prestamoid)
						WHERE (pa.datecreated != '$fecha_actual') AND (pe.codigoruta = $ruta AND pr.status != 0)
						ORDER BY pa.datecreated desc";
			$request = $this->select($sql);

			//dep($request);exit;

			if(!empty($request) && ($request['datepago'] > $requestR['datecreated']) /*|| $fechaPrestamo != $requestR['datecreated']*/)
			{
				if(!empty($request)) {
					$fechaPago = $request['datepago'];
					return $fechaPago;
				}/*else {
					return $fechaPrestamo;
				}*/
			}else{
				return 2;
			}
		}

		public function insertPrestamo(int $cliente,int $monto, int $total, int $taza, int $parcela, int $plazo, int $formato, string $observacion, string $fecha, string $vence)
		{
			$this->fecha_actual = $fecha;
			$this->intIdCliente = $cliente;
			$this->intMonto = $monto;
			$this->intTotal = $total;
			$this->intTaza = $taza;
			$this->intParcela = $parcela;
			$this->intPlazo = $plazo;
			$this->intFormato = $formato;
			$this->strObservacion = $observacion;
			$this->strVence = $vence;
			$ruta = $_SESSION['idRuta'];
			$return = 0;

			$sql = "SELECT idresumen FROM resumen WHERE codigoruta = $ruta AND datecreated = '{$this->fecha_actual}'";
			$requestR = $this->select($sql);
			if(empty($requestR))
			{
				$query_insert = "INSERT INTO prestamos(personaid,monto,total,parcela,formato,plazo,taza,observacion,datecreated,fechavence) VALUES(?,?,?,?,?,?,?,?,?,?)";
				$arrData = array($this->intIdCliente,
								$this->intMonto,
								$this->intTotal,
								$this->intParcela,
								$this->intFormato,
								$this->intPlazo,
								$this->intTaza,
								$this->strObservacion,
								$this->fecha_actual,
								$this->strVence);
				$request_insert = $this->insert($query_insert,$arrData);

				$return = $request_insert;
			}else{
				$return = "0";
			}
			return $return;
		}
	}
?>