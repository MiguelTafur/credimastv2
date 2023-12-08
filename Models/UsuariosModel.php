<?php 

	class UsuariosModel extends Mysql
	{
		PRIVATE $intIdUsuario;
		PRIVATE $strIdentificacion;
		PRIVATE $strNombre;
		PRIVATE $strApellido;
		PRIVATE $intTelefono;
		PRIVATE $strEmail;
		PRIVATE $strPassword;
		PRIVATE $strToken;
		PRIVATE $intTipoId;
		PRIVATE $intStatus;
		PRIVATE $intRuta;
		PRIVATE $strNit;
		PRIVATE $strNomFiscal;
		PRIVATE $strDirFiscal;

		public function __construct()
		{
			parent::__construct();
		}	

		public function insertUsuario(string $idenficacion, string $nombre, string $apellido, int $telefono, string $email, string $password, int $tipoid, int $status, int $ruta)
		{
			$this->strIdentificacion = $idenficacion;
			$this->strNombre = $nombre;
			$this->strApellido = $apellido;
			$this->intTelefono = $telefono;
			$this->strEmail = $email;
			$this->strPassword = $password;
			$this->intTipoId = $tipoid;
			$this->intStatus = $status;
			$this->intRuta = $ruta;
			$return = 0;

			$sql = "SELECT * FROM persona WHERE codigoRuta = $this->intRuta AND (email_user = '{$this->strEmail}' OR identificacion = '{$this->strIdentificacion}')";
			$request = $this->select_all($sql);

			if(empty($request))
			{
				/*$usuarioId = $_SESSION['idUser'];
				$sql2 = "SELECT * FROM persona pe INNER JOIN ruta ru ON(pe.codigoruta = ru.idruta) WHERE pe.idpersona = $usuarioId";
				$request = $this->select($sql2);
				$codigoRuta = $request['idruta'];*/
				$query_insert = "INSERT INTO persona(identificacion,nombres,apellidos,telefono,email_user,password,rolid,codigoruta,status)  VALUES(?,?,?,?,?,?,?,?,?)";
				$arrData = array($this->strIdentificacion,$this->strNombre,$this->strApellido,$this->intTelefono,$this->strEmail,$this->strPassword,$this->intTipoId,$this->intRuta,$this->intStatus);
				$request_insert = $this->insert($query_insert, $arrData);
				$return = $request_insert;
			}else{
				$return = "0";
			}
			return $return;
		}

		public function selectUsuarios()
		{
			$whereAdmin = "";
			if($_SESSION['idUser'] != 1){
				$ruta = $_SESSION['idRuta'];
				$whereAdmin = " and p.idpersona != 1 and p.codigoruta = $ruta";
			}
			$sql = "SELECT p.idpersona, p.identificacion, p.nombres, p.codigoruta, p.telefono, p.email_user, p.codigoruta, p.status, r.idrol, r.nombrerol FROM persona p INNER JOIN rol r ON p.rolid = r.idrol WHERE p.status != 0 and (p.rolid = 1 || p.rolid = 3) ".$whereAdmin;
			$request = $this->select_all($sql);
			return $request;
		}

		public function selectUsuario(int $idpersona)
		{
			$this->intIdUsuario = $idpersona;
			$sql = "SELECT p.idpersona, p.identificacion, p.nombres, p.apellidos, p.telefono, p.email_user, r.idrol, r.nombrerol, p.status, DATE_FORMAT(p.datecreated, '%d-%m-%Y') as fechaRegistro FROM persona p INNER JOIN rol r ON p.rolid = r.idrol WHERE p.idpersona = $this->intIdUsuario";
			$request = $this->select($sql);
			return $request;
		}

		public function selectRutas()
		{
			$sql = "SELECT * from ruta";
			$request = $this->select_all($sql);
			return $request;
		}

		public function updateUsuario(int $idUsuario, string $idenficacion, string $nombre, string $apellido, int $telefono, string $email, string $password, int $tipoid, int $status)
		{
			$this->intIdUsuario = $idUsuario;
			$this->strIdentificacion = $idenficacion;
			$this->strNombre = $nombre;
			$this->strApellido = $apellido;
			$this->intTelefono = $telefono;
			$this->strEmail = $email;
			$this->strPassword = $password;
			$this->intTipoId = $tipoid;
			$this->intStatus = $status;

			$sql = "SELECT * FROM persona WHERE (email_user = '{$this->strEmail}' AND idpersona != $this->intIdUsuario) OR (identificacion = '{$this->strIdentificacion}' AND idpersona != $this->intIdUsuario)";
			$request = $this->select_all($sql);

			if(empty($request))
			{
				$sql = "UPDATE persona SET identificacion = ?, nombres = ?, apellidos = ?, telefono = ?, email_user = ?, password = ?, rolid = ?, status = ? WHERE idpersona = $this->intIdUsuario";
				$arrData = array($this->strIdentificacion,$this->strNombre,$this->strApellido,$this->intTelefono,$this->strEmail,$this->strPassword,$this->intTipoId,$this->intStatus);
				$request = $this->update($sql, $arrData);
			}else{
				$request = "0";
			}
			return $request;
		}

		public function deleteUsuario(int $idtipousuario)
		{
			$usuarioId = $_SESSION['idUser'];
			$this->intIdUsuario = $idtipousuario;
			$sql2 = "SELECT * FROM persona pe INNER JOIN ruta ru ON(pe.codigoruta = ru.idruta) WHERE pe.idpersona = $usuarioId";
			$request = $this->select($sql2);
			$codigoRuta = $request['idruta'];
			$sql = "UPDATE persona SET status = ? WHERE idpersona = $this->intIdUsuario AND codigoruta = $codigoRuta";
			$arrData = array(0);
			$request = $this->update($sql, $arrData);
			return $request;
		}

		public function updatePerfil(int $idUsuario, string $idenficacion, string $nombre, string $apellido, int $telefono)
		{
			$this->intIdUsuario = $idUsuario;
			$this->strIdentificacion = $idenficacion;
			$this->strNombre = $nombre;
			$this->strApellido = $apellido;
			$this->intTelefono = $telefono;

			$sql = "SELECT * FROM persona WHERE (identificacion = '{$this->strIdentificacion}' AND idpersona != $this->intIdUsuario)";
			$request = $this->select_all($sql);

			if(empty($request)){
					$sql = "UPDATE persona SET identificacion = ?, nombres = ?, apellidos = ?, telefono = ? WHERE idpersona = $this->intIdUsuario";
					$arrData = array($this->strIdentificacion,$this->strNombre,$this->strApellido,$this->intTelefono);
				$request = $this->update($sql,$arrData);
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