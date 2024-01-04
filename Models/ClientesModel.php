<?php 

class ClientesModel extends Mysql
{
	PRIVATE $intIdUsuario;
	PRIVATE $strIdentificacion;
	PRIVATE $strNombre;
	PRIVATE $strApellido;
	PRIVATE $intTelefono;
	PRIVATE $strDireccion;
	PRIVATE $intTipoId;
	PRIVATE $intStatus;
	PRIVATE $intIdRuta;

	public function __construct()
	{
		parent::__construct();
	}	

	public function insertCliente(string $identificacion, string $nombre, string $apellido, int $telefono, string $direccion, int $tipoid, int $ruta)
	{
		$this->strIdentificacion = $identificacion;
		$this->strNombre = $nombre;
		$this->strApellido = $apellido;
		$this->intTelefono = $telefono;
		$this->strDireccion = $direccion;
		$this->intTipoId = $tipoid;
		$this->intIdRuta = $ruta;
		$return = 0;

		$sql = "SELECT * FROM persona WHERE identificacion = '{$this->strIdentificacion}'";
		$request = $this->select_all($sql);

		if(empty($request))
		{
			$query_insert = "INSERT INTO persona(identificacion,nombres,apellidos,telefono,direccion,rolid,codigoruta)  VALUES(?,?,?,?,?,?,?)";
			$arrData = array($this->strIdentificacion,$this->strNombre,$this->strApellido,$this->intTelefono,$this->strDireccion,$this->intTipoId,$this->intIdRuta);
			$request_insert = $this->insert($query_insert, $arrData);
			$return = $request_insert;
		}else{
			$return = "0";
		}
		return $return;
	}

	public function selectClientes()
	{
		$ruta = $_SESSION['idRuta'];
		$sql = "SELECT idpersona, identificacion, nombres, apellidos, telefono, direccion, status FROM persona WHERE rolid = 7 AND codigoruta = $ruta  AND status != 0 ORDER BY nombres ASC";
		$request = $this->select_all($sql);
		return $request;
	}

	public function selectCliente(int $idpersona)
	{
		//$ruta = $_SESSION['idRuta'];
		$this->intIdUsuario = $idpersona;
		$sql = "SELECT pe.idpersona, pe.identificacion, pe.nombres, pe.apellidos, pe.telefono, pe.direccion, pe.status, 
				DATE_FORMAT(pe.datecreated, '%d-%m-%Y') as fechaRegistro, COUNT(pr.personaid) as prestamos
				FROM persona pe INNER JOIN prestamos pr ON(pe.idpersona = pr.personaid)
				WHERE pe.idpersona = $this->intIdUsuario AND pr.status != 0 AND rolid = ".RCLIENTES;
		$request = $this->select($sql);
		return $request;
	}

	public function updateCliente(int $idUsuario, string $identificacion, string $nombre, string $apellido, int $telefono, string $direccion)
	{
		$this->intIdUsuario = $idUsuario;
		$this->strIdentificacion = $identificacion;
		$this->strNombre = $nombre;
		$this->strApellido = $apellido;
		$this->intTelefono = $telefono;
		$this->strDireccion = $direccion;

		$sql = "SELECT * FROM persona WHERE (identificacion = '{$this->strIdentificacion}' AND idpersona != $this->intIdUsuario)";
		$request = $this->select_all($sql);

		if(empty($request))
		{

			$sql = "UPDATE persona SET identificacion = ?, nombres = ?, apellidos = ?, telefono = ?, direccion = ?  WHERE idpersona = $this->intIdUsuario";
			$arrData = array($this->strIdentificacion,$this->strNombre,$this->strApellido,$this->intTelefono,$this->strDireccion);
			$request = $this->update($sql, $arrData);
		}else{
			$request = "0";
		}
		return $request;
	}

	public function deleteCliente(int $idtipousuario)
	{
		$this->intIdUsuario = $idtipousuario;
		$ruta = $_SESSION['idRuta'];

		$sqlPr = "SELECT * FROM prestamos pr INNER JOIN persona pe ON(pr.personaid = pe.idpersona) WHERE pe.codigoruta = $ruta AND pr.personaid = $this->intIdUsuario AND pr.status = 1";
		$requestPr = $this->select_all($sqlPr);

		if(empty($requestPr)){
			$sql = "UPDATE persona SET status = ? WHERE idpersona = $this->intIdUsuario";
			$arrData = array(0);
			$request = $this->update($sql, $arrData);
		}else{
			$request = "0";	
		}

		
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