<?php

	class ListaResumenes extends Controllers
	{
		public function __construct()
		{
			parent::__construct();
			session_start();
			if(empty($_SESSION['login'])){
				header('Location: '.base_url().'/login');
			}
			getPermisos(MRESUMEN);
		}

		public function ListaResumenes()
		{
			if(empty($_SESSION['permisosMod']['r'])){
				header("Location: ".base_url().'/prestamos');
			}
			$data['page_tag'] = "Lista de resumenes";
			$data['page_title'] = "LISTA DE RESUMENES";
			$data['page_name'] = "resumenes";
			$data['pagamentos'] = $this->model->selectDatePagoPrestamo();
			$data['page_functions_js'] = "functions_listaResumenes.js";
			$this->views->getView($this,"listaResumenes",$data);
		}

		public function getResumenes()
		{
			if($_SESSION['permisosMod']['r'])
			{
				$arrData = $this->model->selectResumenes();
				$fecha = "";

				for ($i=0; $i < count($arrData); $i++) {

					$fecha = '<small>'.date("d-m-Y", strtotime($arrData[$i]['datecreated'])).'</small>';

					// COBRADO
					if($arrData[$i]['cobrado'] > 0)
					{
						$arrData[$i]['cobrado'] = $arrData[$i]['cobrado'].' '.'&nbsp;
						<button type="button" id="" class="btn btn-secondary btn-sm pagosResumen" data-content="'.forClientesPagos($arrData[$i]['datecreated']).'" title="PAGAMENTOS&nbsp; '.$fecha.'">
							<i class="fas fa-info-circle fa-sm" aria-hidden="true"></i>
						</button>
							<script type="text/javascript">
							  $(function(){
						        $(".pagosResumen").popover({
						        	container: "body",
						        	trigger: "focus",
						        	html: true
						        	});
							    });
							</script>';
					}

					// VENTAS
					if($arrData[$i]['ventas'] > 0)
					{
						$arrData[$i]['ventas'] = $arrData[$i]['ventas'].' '.'&nbsp;
						<button type="button" class="btn btn-secondary btn-sm ventasResumen" data-content="'.forVentasResumen($arrData[$i]['datecreated']).'" title="CREDITOS&nbsp; '.$fecha.'">
							<i class="fas fa-info-circle fa-sm" aria-hidden="true"></i>
						</button>
							<script type="text/javascript">
							  $(function(){
						        $(".ventasResumen").popover({
						        	container: "body",
						        	trigger: "focus",
						        	html: true
						        	});
							    });
							</script>';
					}

					// GASTOS
					if($arrData[$i]['gasto'] > 0)
					{
						$arrData[$i]['gasto'] = $arrData[$i]['gasto'].' '.'&nbsp;
						<button type="button" class="btn btn-secondary btn-sm gastosResumen" data-content="'.forGastoResumen($arrData[$i]['datecreated']).'" title="GASTOS&nbsp; '.$fecha.'">
							<i class="fas fa-info-circle fa-sm" aria-hidden="true"></i>
						</button>
							<script type="text/javascript">
							  $(function(){
						        $(".gastosResumen").popover({
						        	container: "body",
						        	trigger: "focus",
						        	html: true
						        	});
							    });
							</script>';
					}
				}

				echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
			}
			die();
		}

	}
?>