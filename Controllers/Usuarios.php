<?php 

class Usuarios extends Controllers{
	public function __construct()
	{
		//sessionStart();
		session_start();
		parent::__construct();
		//session_regenerate_id(true);
		if(empty($_SESSION['login'])){
			header('Location: '.base_url().'/login');
		}
		getPermisos(MUSUARIOS);
	}

	public function Usuarios()
	{
		if(empty($_SESSION['permisosMod']['r'])){
			header("Location: ".base_url().'/prestamos');
		}
		$data['page_tag'] = "Usuarios";
		$data['page_title'] = "USUARIOS";
		$data['page_name'] = "usuarios";
		$data['pagamentos'] = $this->model->selectDatePagoPrestamo();
		$data['page_functions_js'] = "functions_usuarios.js";
		$this->views->getView($this,"usuarios",$data);
	}

	public function setUsuario()
	{
		if($_POST)
		{
			if(empty($_POST['txtIdentificacion']) || empty($_POST['txtNombre']) || empty($_POST['txtApellido']) || empty($_POST['txtTelefono']) || empty($_POST['txtEmail']) || empty($_POST['listRolid']) || empty($_POST['listStatus']) || empty($_POST['listRuta']))
			{
				$arrRespose = array("status" => false, "msg" => "Datos incorrectos.");
			}else{
				$idUsuario = intval($_POST['idUsuario']);
				$strIdentificacion = strClean($_POST['txtIdentificacion']);
				$strNombre =  ucwords(strClean($_POST['txtNombre']));
				$strApellido =  ucwords(strClean($_POST['txtApellido']));
				$intTelefono = intval(strClean($_POST['txtTelefono']));
				$strEmail = strtolower(strClean($_POST['txtEmail']));
				$intTipoId = intval($_POST['listRolid']);
				$intStatus = intval($_POST['listStatus']);
				$intRuta = intval($_POST['listRuta']);
				$request_user = "";

				if($idUsuario == 0)
				{
					$option = 1;
					$strPassword = empty($_POST['txtPassword']) ? hash("SHA256", passGenerator()) : hash("SHA256", $_POST['txtPassword']);
					
					if($_SESSION['permisosMod']['w']){
						$request_user = $this->model->insertUsuario($strIdentificacion,$strNombre,$strApellido,$intTelefono,$strEmail,$strPassword,$intTipoId,$intStatus,$intRuta);
					}
				}else{
					$option = 2;
					$strPassword = empty($_POST['txtPassword']) ? "" : hash("SHA256", $_POST['txtPassword']);
					if($_SESSION['permisosMod']['u']){
						$request_user = $this->model->updateUsuario($idUsuario,$strIdentificacion,$strNombre,$strApellido,$intTelefono,$strEmail,$strPassword,$intTipoId,$intStatus);
					}
				}

				if($request_user > 0)
				{
					if($option == 1){
						$arrResponse = array('status' => true, 'msg' => 'Datos guardados correctamente.');
					}else{
						$arrResponse = array('status' => true, 'msg' => 'Datos actualizados correctamente.');
					}
				}else if($request_user == '0'){
					$arrResponse = array('status' => false, 'msg' => 'Atencion! el email o la identificación ya existe, ingrese otro.');
				}else{
					$arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
				}
			}	
			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		}
		die();
	}

	public function getUsuarios()
	{
		if($_SESSION['permisosMod']['r']){
			$arrData = $this->model->selectUsuarios();

			for ($i=0; $i < count($arrData); $i++) {

				$btnView = '';
				$btnEdit = '';
				$btnDelete = '';

				if($arrData[$i]['status'] == 1)
				{
					$arrData[$i]['status'] = '<span class="badge badge-success">Activo</span>';
				}else{
					$arrData[$i]['status'] = '<span class="badge badge-danger">Inactivo</span>';
				}

				if($_SESSION['permisosMod']['r']){
					$btnView = '<button class="btn btn-info btn-sm btnViewUsuario" onclick="fntViewUsuario('.$arrData[$i]['idpersona'].')" title="Ver usuario"><i class="far fa-eye"></i></button>';
				}
				if($_SESSION['permisosMod']['u']){
					if($_SESSION['idUser'] == 1 && $_SESSION['userData']['idrol'] == 1 ||
					  ($_SESSION['userData']['idrol'] == 1 && $arrData[$i]['idrol'] != 1)){
						$btnEdit = '<button class="btn btn-primary btn-sm btnEditUsuario" onclick="fntEditUsuario(this,'.$arrData[$i]['idpersona'].')" title="Editar usuario"><i class="fas fa-pencil-alt"></i></button>';
					}else{
						$btnEdit = '<button class="btn btn-secondary btn-sm" disabled><i class="fas fa-pencil-alt"></i></button>';
					}
				}
				if($_SESSION['permisosMod']['d']){
					if($_SESSION['idUser'] == 1 && $_SESSION['userData']['idrol'] == 1 ||
					  ($_SESSION['userData']['idrol'] == 1 && $arrData[$i]['idrol'] != 1) and
					  ($_SESSION['userData']['idpersona'] != $arrData[$i]['idpersona'])){

						$btnDelete = '<button class="btn btn-danger btn-sm btnDelUsuario" onclick="fntDelUsuario('.$arrData[$i]['idpersona'].')" title="Eliminar usuario"><i class="far fa-trash-alt"></i></button>';
					}else{
						$btnDelete = '<button class="btn btn-secondary btn-sm" disabled><i class="far fa-trash-alt"></i></button>';
					}
				}

				$arrData[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
			}
			echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		}
		die();
	}

	public function getUsuario($idpersona)
	{
		if($_SESSION['permisosMod']['r']){
			$idusuario = intval($idpersona);
			if($idusuario > 0)
			{
				$arrData = $this->model->selectUsuario($idusuario);
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

	public function getRutas()
	{
		if($_SESSION['permisosMod']['r']){
			$htmlOptions = "";
			$arrData = $this->model->selectRutas();
			if(count($arrData) > 0){
				for ($i=0; $i < count($arrData); $i++) { 
					$htmlOptions .= '<option value="'.$arrData[$i]['idruta'].'">'.$arrData[$i]['nombre'].'</option>';
				}
			}
			echo $htmlOptions;
			die();

			
			if(empty($arrData))
				{
					$arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
				}else{
					$arrResponse = array('status' => true, 'data' => $arrData);
				}
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		}	

		die();
	}

	public function delUsuario()
	{
		if($_POST)
		{
			if($_SESSION['permisosMod']['d']){
				$intIdpersona = intval($_POST['idUsuario']);
				$requestDelete = $this->model->deleteUsuario($intIdpersona);
				if($requestDelete)
				{
					$arrResponse = array('status' => true, 'msg' => 'Se ha eliminado el usuario.');
				}else{
					$arrResponse = array('status' => false, 'msg' => 'Error al eliminar el usuario.');
				}
				echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);	
			}
		}
		die();
	}

	public function perfil()
	{
		$data['page_tag'] = "Perfil";
		$data['page_title'] = "Perfil de usuario";
		$data['page_name'] = "perfil";
		$data['page_functions_js'] = "functions_usuarios.js";
		$this->views->getView($this,"perfil",$data);
	}

	public function putPerfil()
	{
		if($_POST){
			if(empty($_POST['txtIdentificacion']) || empty($_POST['txtNombre']) || empty($_POST['txtApellido']) || empty($_POST['txtTelefono'])){
				$arrResponse = array("status" => false, "msg" => 'Datos incorrectos.');
			}else{
				$idUsuario = $_SESSION['idUser'];
				$strIdentificacion = strClean($_POST['txtIdentificacion']);
				$strNombre = strClean($_POST['txtNombre']);
				$strApellido = strClean($_POST['txtApellido']);
				$intTelefono = intval(strClean($_POST['txtTelefono']));
				$strPassword = "";

				$request_user = $this->model->updatePerfil($idUsuario,$strIdentificacion,$strNombre,$strApellido,$intTelefono);

				if($request_user){
					sessionUser($idUsuario);
					$arrResponse = array("status" => true, "msg" => 'Datos actualizados correctamente.');		
				}else if($request_user == '0'){
					$arrResponse = array('status' => false, 'msg' => 'Atencion! La identificación ya existe, por favor,  ingrese otra.');
				}else{
					$arrResponse = array("status" => false, "msg" => 'No es posible actualizar los datos.');
				}
			}
			echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		}
		die();
	}

	public function putDFiscal()
	{
		if($_POST){
			if(empty($_POST['txtNit']) || empty($_POST['txtNombreFiscal']) || empty($_POST['txtDirFiscal'])){
				$arrResponse = array("status" => false, "msg" => 'Datos incorrectos.');
			}else{
				$idUsuario = $_SESSION['idUser'];
				$strNit = strClean($_POST['txtNit']);
				$strNomFiscal = strClean($_POST['txtNombreFiscal']);
				$strDirFiscal = strClean($_POST['txtDirFiscal']);
				$request_datafiscal = $this->model->updateDataFiscal($idUsuario,$strNit,$strNomFiscal,$strDirFiscal);

				if($request_datafiscal){
					sessionUser($_SESSION['idUser']);
					$arrResponse = array("status" => true, "msg" => 'Datos actualizados correctamente.');	
				}else{
					$arrResponse = array("status" => false, "msg" => 'No es posible actualizar los datos.');
				}
			}
			echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		}
		die();
	}
}
 ?>