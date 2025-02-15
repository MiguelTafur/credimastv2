<?php 

	class Login extends Controllers{
		public function __construct()
		{
			session_start();
			if(isset($_SESSION['login'])){
				header('Location: '.base_url().'/prestamos');
			}
			parent::__construct();
		}

		public function login()
		{
			$data['page_tag'] = "Login - CREDIMAST";
			$data['page_title'] = "Credimast";
			$data['page_name'] = "login";
			$data['page_functions_js'] = "functions_login.js";
			$this->views->getView($this,"login",$data);
		}

		public function loginUser()
		{
			//dep($_POST);exit;
			if($_POST){
				if(empty($_POST['txtEmail']) || empty($_POST['txtCodigo']) || empty($_POST['txtRuta'])){
					$arrResponse = array('status' => false, 'msg' => 'Error de datos.');
				}else{
					$strRuta = strtolower(strClean($_POST['txtRuta']));
					$intCodigo = intval($_POST['txtCodigo']);
					$strUsuario = strtolower(strClean($_POST['txtEmail']));
					$requestUser = $this->model->loginUser($strRuta, $intCodigo, $strUsuario);

					//dep($requestUser);exit;					

					if(empty($requestUser)){
						$arrResponse = array('status' => false, 'msg' => 'Los datos proporcionados no coinciden.');
					}else{
						//dep($requestUser);exit;
						$arrData = $requestUser;
						if($arrData['status'] == 1){
							$_SESSION['idUser'] = $arrData['idpersona'];
							$_SESSION['idRol'] = $arrData['rolid'];
							$_SESSION['ruta'] = $arrData['nombre'];
							$_SESSION['idRuta'] = $arrData['idruta'];
							$_SESSION['login'] = true;
							$_SESSION['timeout'] = true;
							$_SESSION['inicio'] = time();

							$arrData = $this->model->sessionLogin($_SESSION['idUser']);
							sessionUser($_SESSION['idUser']);
							$arrResponse = array('status' => true, 'msg' => 'ok.');
						}else{
							$arrResponse = array('status' => false, 'msg' => 'Usuario inactivo.');
						}
					}
				}
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		public function resetPass()
		{
			if($_POST){
				error_reporting(0);
				if(empty($_POST['txtEmailReset']))
				{
					$arrResponse = array('status' => false, 'msg' => 'Error de datos');
				}else{
					$token = token();
					$strEmail = strtolower(strClean($_POST['txtEmailReset']));
					$arrData = $this->model->getUserEmail($strEmail);

					if(empty($arrData)){
						$arrResponse = array('status' => false, 'msg' => 'Usuario no existente.');
					}else{
						$idpersona = $arrData['idpersona'];
						$nombreUsuario = $arrData['nombres'].' '.$arrData['apellidos'];

						$url_recovery = base_url().'/login/confirmUser/'.$strEmail.'/'.$token;
						$requestUpdate = $this->model->setTokenUser($idpersona,$token);

						$dataUsuario = array('nombreUsuario' => $nombreUsuario,
											 'email' => $strEmail,
											 'asunto' => 'Recuperar cuenta - '.NOMBRE_REMITENTE,
											 'url_recovery' => $url_recovery);


						if($requestUpdate){
							$sendEmail = sendEmail($dataUsuario,'email_cambioPassword');
							if($sendEmail){
								$arrResponse = array('status' => true, 'msg' => 'Se ha enviado el email a tu cuenta para cambiar tu contraseña.');
							}else{
								$arrResponse = array('status' => false, 'msg' => 'No es posible enviar el correo, intenta mas tarde.');	
							}
						}else{
							$arrResponse = array('status' => false, 'msg' => 'No es posible realizar el proceso, intenta mas tarde.');
						}
					}
				}
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		public function confirmUser(string $params)
		{
			if(empty($params)){
				header('Location: '.base_url());
			}else{
				$arrParams = explode(',', $params);
				$strEmail = strClean($arrParams[0]);
				$strToken = strClean($arrParams[1]);

				$arrResponse = $this->model->getUsuario($strEmail,$strToken);

				if(empty($arrResponse)){
					header('Location: '.base_url());
				}else{
					$data['page_tag'] = "Cambiar contraseña";
					$data['page_title'] = "Cambiar contraseña";
					$data['page_name'] = "Cambiar contraseña";
					$data['email'] = $strEmail;
					$data['token'] = $strToken;
					$data['idpersona'] = $arrResponse['idpersona'];
					$data['page_functions_js'] = "functions_login.js";
					$arrResponse = $this->views->getView($this,"cambiar_password",$data);
				}
			}
			die();
		}

		public function setPassword()
		{
			if(empty($_POST['idUsuario']) || empty($_POST['txtEmail']) || empty($_POST['txtToken']) || empty($_POST['txtPassword']) || empty($_POST['txtPasswordConfirm'])){
				$arrResponse = array('status' => false, 'msg' => 'Error de datos.');
			}else{
				$intIdpersona = intval($_POST['idUsuario']);
				$strPassword = $_POST['txtPassword'];
				$strPasswordConfirm = $_POST['txtPasswordConfirm'];
				$strEmail = strClean($_POST['txtEmail']);
				$strToken = strClean($_POST['txtToken']);

				if($strPassword != $strPasswordConfirm){
					$arrResponse = array('status' => false, 'msg' => 'Las contraseñas no coinciden.');	
				}else{
					$arrResponseUser = $this->model->getUsuario($strEmail,$strToken);
					if(empty($arrResponseUser)){
						$arrResponse = array('status' => false, 'msg' => 'Error de datos.');
					}else{
						$strPassword = hash("SHA256", $strPassword);
						$requestPass = $this->model->insertPassword($intIdpersona,$strPassword);

						if($requestPass){
							$arrResponse = array('status' => true, 'msg' => 'contraseña actualizada con exito.');	
						}else{
							$arrResponse = array('status' => false, 'msg' => 'No es posible realizar el proceso.');
						}
					}
				}
			}
			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			die();
		}
	}
 ?>