<?php 

class Clientes extends Controllers{
	public function __construct()
	{
		//sessionStart();
		session_start();
		parent::__construct();
		//session_regenerate_id(true);
		if(empty($_SESSION['login'])){
			header('Location: '.base_url().'/login');
		}
		getPermisos(MCLIENTES);
	}

	public function Clientes()
	{
		if(empty($_SESSION['permisosMod']['r'])){
			header("Location: ".base_url().'/prestamos');
		}
		$data['page_tag'] = "Clientes";
		$data['page_title'] = "CLIENTES";
		$data['page_name'] = "clientes";
		$data['pagamentos'] = $this->model->selectDatePagoPrestamo();
		$data['page_functions_js'] = "functions_clientes.js";
		$this->views->getView($this,"clientes",$data);
	}
	
	public function setCliente()
	{
		if($_POST)
		{
			if(empty($_POST['txtIdentificacion']) || empty($_POST['txtNombre']) || empty($_POST['txtApellido']) || empty($_POST['txtTelefono']) || empty($_POST['txtDireccion']))
			{
				$arrRespose = array("status" => false, "msg" => "Datos incorrectos.");
			}else{
				$idUsuario = intval($_POST['idUsuario']);
				$strIdentificacion = strClean($_POST['txtIdentificacion']);
				$strNombre =  ucwords(strClean($_POST['txtNombre']));
				$strApellido =  ucwords(strClean($_POST['txtApellido']));
				$intTelefono = intval(strClean($_POST['txtTelefono']));
				$strDireccion =  strClean($_POST['txtDireccion']);
				$intTipoId = 7;
				$request_user = "";
				$intIdRuta = $_SESSION['idRuta'];

				if($idUsuario == 0)
				{
					$option = 1;
					if($_SESSION['permisosMod']['w']){
						$request_user = $this->model->insertCliente($strIdentificacion,
																	$strNombre,
																	$strApellido,
																	$intTelefono,
																	$strDireccion,
																	$intTipoId,
																	$intIdRuta);
					}
				}else{
					$option = 2;
					if($_SESSION['permisosMod']['u']){
						$request_user = $this->model->updateCliente($idUsuario,
																	$strIdentificacion,
																	$strNombre,
																	$strApellido,
																	$intTelefono,
																	$strDireccion,
																	$intTipoId);
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
					$arrResponse = array('status' => false, 'msg' => 'Atención! el email o la identificación ya existe, ingresa otro.');
				}else{
					$arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
				}
			}	
			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		}
		die();
	}

	public function getClientes()
	{
		if($_SESSION['permisosMod']['r']){
			$arrData = $this->model->selectClientes();
			for ($i=0; $i < count($arrData); $i++) {
				
				$btnView = '';
				$btnEdit = '';
				$btnDelete = '';
				if($_SESSION['permisosMod']['r']){
					$btnView = '<button class="btn btn-info btn-sm mr-1" onClick="fntViewInfo('.$arrData[$i]['idpersona'].')" title="Ver cliente"><i class="far fa-eye"></i></button>';
				}
				if($_SESSION['permisosMod']['u']){
					$btnEdit = '<button class="btn btn-primary btn-sm mr-1" onClick="fntEditInfo(this,'.$arrData[$i]['idpersona'].')" title="Editar cliente"><i class="fas fa-pencil-alt"></i></button>';
				}
				if($_SESSION['permisosMod']['d']){
					$btnDelete = '<button class="btn btn-danger btn-sm" onClick="fntDelInfo('.$arrData[$i]['idpersona'].')" title="Eliminar cliente"><i class="far fa-trash-alt"></i></button>';
				}

				$arrData[$i]['options'] = '<div class="text-center d-flex justify-content-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
			}
			echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		}
		die();
	}

	public function getSelectClientes()
	{
		$htmlOptions = "";
		$arrData = $this->model->selectClientes();

		if(count($arrData) > 0){
			for ($i=0; $i < count($arrData); $i++) { 
				if($arrData[$i]['status'] == 1){
					$htmlOptions .= '<option></option>';
					$htmlOptions .= '<option value="'.$arrData[$i]['idpersona'].'">'.strtoupper($arrData[$i]['nombres']).' - '.$arrData[$i]['apellidos'].'</option>';
				}
			}
		}
		echo $htmlOptions;
		die();
	}

	public function getCliente($idpersona)
	{
		if($_SESSION['permisosMod']['r']){
			$idusuario = intval($idpersona);
			if($idusuario > 0)
			{
				$arrData = $this->model->selectCliente($idusuario);
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

	public function delCliente()
	{
		if($_POST)
		{
			if($_SESSION['permisosMod']['d']){
				$intIdpersona = intval($_POST['idUsuario']);
				$requestDelete = $this->model->deleteCliente($intIdpersona);
				if($requestDelete)
				{
					$arrResponse = array('status' => true, 'msg' => 'Se ha eliminado el usuario.');
				}else{
					$arrResponse = array('status' => false, 'msg' => 'El cliente tiene préstamos vinculados.');
				}
				echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);	
			}
		}
		die();
	}
}

?>