<?php 

	class ListaResumenesModel extends Mysql
	{
		public function __construct()
		{
			parent::__construct();
		}	

		public function selectResumenes()
		{
			$ruta = $_SESSION['idRuta'];
			$sql = "SELECT re.idresumen,ba.monto as base,
							ga.nombre,ga.monto as gasto,
							re.ventas,re.cobrado,
							re.total,re.datecreated
					FROM resumen re INNER JOIN base ba ON re.baseid = ba.idbase 
									INNER JOIN gastos ga ON re.gastoid = ga.idgasto 
									WHERE re.codigoruta = $ruta";
			$request = $this->select_all($sql);
			return $request;
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