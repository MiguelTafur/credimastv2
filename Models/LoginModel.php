<?php 

	class LoginModel extends Mysql
	{
		private $strRuta;
		private $intCodigo;
		private $intIdUsuario;
		private $strUsuario;
		private $strPassword;
		private $strToken;

		public function __construct()
		{
			parent::__construct();
		}	

		public function loginUser(string $ruta, int $codigo, string $usuario)
		{
			$this->strRuta = $ruta;
			$this->intCodigo = $codigo;
			$this->strUsuario = $usuario;
			$sql = "SELECT pe.idpersona, pe.rolid ,pe.status, ru.idruta, ru.nombre 
					FROM persona pe INNER JOIN ruta ru ON(pe.codigoruta = ru.idruta) 
					WHERE ru.nombre = '$this->strRuta' AND pe.email_user = '$this->strUsuario' AND pe.codigoruta = $this->intCodigo AND pe.status != 0";
			$request = $this->select($sql);
			return $request;
		}

		public function sessionLogin(int $iduser)
		{
			$this->intIdUsuario = $iduser;
			//BUSCAR ROLE
			$sql = "SELECT p.idpersona,
						  p.identificacion,
						  p.nombres,
						  p.apellidos,
						  p.telefono,
						  p.email_user,
						  r.idrol,r.nombrerol,
						  p.status
					FROM persona p 
					INNER JOIN rol r
					ON p.rolid = r.idrol
					WHERE p.idpersona = $this->intIdUsuario";
			$request = $this->select($sql);
			$_SESSION['userData'] = $request;
			return $request;
		}

		public function getUserEmail(string $strEmail)
		{
			$this->strUsuario = $strEmail;
			$sql = "SELECT idpersona,nombres,apellidos,status FROM persona WHERE email_user = '$this->strUsuario' AND status = 1";
			$request = $this->select($sql);
			return $request;
		}

		public function setTokenUser(int $idpersona, string $token)
		{
			$this->intIdUsuario = $idpersona;
			$this->strToken = $token;
			$sql = "UPDATE persona SET token = ? WHERE idpersona = $this->intIdUsuario";
			$arrData = array($this->strToken);
			$request = $this->update($sql,$arrData);
			return $request;
		}

		public function getUsuario(string $email, string $token)
		{
			$this->strUsuario = $email;
			$this->strToken = $token;

			$sql = "SELECT idpersona FROM persona WHERE email_user = '$this->strUsuario' AND token = '$this->strToken' AND status = 1";
			$request = $this->select($sql);
			return $request;
		}

		public function insertPassword(int $idPersona, string $password)
		{
			$this->intIdUsuario = $idPersona;
			$this->strPassword = $password;
			$sql = "UPDATE persona SET password = ?, token = ? WHERE idpersona = $this->intIdUsuario";
			$arrData = array($this->strPassword,"");
			$request = $this->update($sql,$arrData);
			return $request;
		}
	}
 ?>