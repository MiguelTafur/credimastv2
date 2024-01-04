<?php 

    class Rutas extends Controllers{
        public function __construct()
        {
            session_start();
            parent::__construct();
            if(empty($_SESSION['login'])){
                header('Location: '.base_url().'/login');
            }
            getPermisos(MRUTAS);
        }

        public function Rutas()
        {
            if(empty($_SESSION['permisosMod']['r'])){
                header("Location: ".base_url().'/prestamos');
            }
            $data['page_tag'] = "Rutas";
            $data['page_title'] = "RUTAS";
            $data['page_name'] = "rutas";
            //$data['pagamentos'] = $this->model->selectDatePagoPrestamo();
            $data['page_functions_js'] = "functions_rutas.js";
            $this->views->getView($this,"rutas",$data);
        }

        public function getRutas()
        {
            if($_SESSION['permisosMod']['r']){
                $arrData = $this->model->selectRutas();
                //dep($arrData);exit;
                for ($i=0; $i < count($arrData); $i++) {
                    
                    $btnEdit = '';
                    $btnDelete = '';

                    if($_SESSION['permisosMod']['u']){
                        $btnEdit = '<button class="btn btn-secondary btn-sm" onClick="fntEditInfo(this,'.$arrData[$i]['codigo'].')" title="Editar Ruta"><i class="fas fa-pencil-alt"></i></button>';
                    }
                    if($_SESSION['permisosMod']['d']){
                        $btnDelete = '<button class="btn btn-danger btn-sm" onClick="fntDelInfo('.$arrData[$i]['codigo'].')" title="Eliminar Ruta"><i class="far fa-trash-alt"></i></button>';
                    }

                    $arrData[$i]['options'] = '<div class="text-center">'.$btnEdit.' '.$btnDelete.'</div>';
                }
                echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
            }
            die();
        }

        public function setRutas()
        {
            if($_POST)
            {
                if(empty($_POST['txtNombre']))
                {
                    $arrResponse = array("status" => false, "msg" => "Ingresa un nombre.");
                }else{
                    $codigoRuta = intval($_POST['codigoRuta']);
                    $strNombre =  ucwords(strClean($_POST['txtNombre']));
                    $diaPagamento = date("d");
                    $request_user = "";

                    if($codigoRuta == 0)
                    {
                        $option = 1;
                        if($_SESSION['permisosMod']['w']){
                            $request_user = $this->model->insertRuta($strNombre,$diaPagamento);
                        }
                    }else{
                         $option = 2;
                         if($_SESSION['permisosMod']['u']){
                            $diaPagamento = strClean($_POST['txtDia']);
                            $request_user = $this->model->updateRuta($codigoRuta,$strNombre,$diaPagamento);
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
                        $arrResponse = array('status' => false, 'msg' => 'Atención! el código ya existe, ingresa otro.');
                    }else{
                        $arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
                    }
                }	
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
            die();
        }

        public function getRuta($idruta)
        {
            if($_SESSION['permisosMod']['r']){
                $idRuta = intval($idruta);
                if($idRuta > 0)
                {
                    $arrData = $this->model->selectRuta($idRuta);
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

        public function delRuta()
        {
            if($_POST)
            {
                if($_SESSION['permisosMod']['d']){
                    $intIdRuta = intval($_POST['idRuta']);
                    $requestDelete = $this->model->deleteRuta($intIdRuta);
                    if($requestDelete)
                    {
                        $arrResponse = array('status' => true, 'msg' => 'Se ha eliminado la Ruta.');
                    }else{
                        $arrResponse = array('status' => false, 'msg' => 'La ruta tiene algún usuario vinculado.');
                    }
                    echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);	
                }
            }
            die();
        }
    }

?>