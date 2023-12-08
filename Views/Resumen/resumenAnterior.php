<?php 
  headerAdmin($data);
  getModal('modalPrestamos',$data);
  $fecha_actual = date("Y-m-d");
  
?>

<main class="app-content">
  <div class="app-title">
    <div>
        <h1>
          <i class="fas fa-user-tag"></i> <?= $data['page_title'] ?>
        </h1>
    </div>
  </div>

  <div class="tile">
    <div class="tile-body">
      <div class="alert alert-danger" role="alert">
        <strong>Error Resumen - </strong> Debes cerrar el resumen anterior!
      </div>
    </div>

    <div id="cardAnterior" class="card">
      <div class="card-body">
        <form id="formResumenAnterior" name="formResumenAnterior" class="form-horizontal">
          <input type="hidden" name="fechaResumen" id="fechaResumen" value="<?= $data['pagamentos']['datepago']; ?>">
          <input type="hidden" id="idRuta" value="<?= $_SESSION['idRuta']; ?>">
          <input type="hidden" name="idResumen" id="idResumen" value="">
          

          <div class="row">
            <div class="col-md-6">
              <table class="table table-borderless">
                <tbody>
                  <tr>
                    <td><h5 class="card-title">BASE:</h5></td>
                    <?php 
                      if(!empty($data['prestamos']))
                      {
                    ?>
                      <td id="baseAnterior">
                        <?php 
                          if(!empty($data['resumenAnterior']))
                          {
                            $baseAnterior = $data['resumenAnterior']['total'];
                          }else{
                            $baseAnterior = 0;
                          }
                          
                          if(!empty($data['base']))
                          {
                            $baseActualizada = $data['base']['monto'];
                            echo /*SMONEY.' '.*/$baseActualizada;
                          }else{
                            echo /*SMONEY.' '.*/$baseAnterior;
                          }
                        ?>
                      </td>
                      <?php if(!empty($data['base'])){ ?>
                        <td id="celDelBaseAnterior">
                          <button type="button" class="btn btn-danger btn-sm" onclick="fntDelBase()"><i class="fas fa-trash fa-sm" aria-hidden="true"></i>
                          </button>
                        </td>
                      <?php }else{ ?>
                        <td id="celAddBaseAnterior">
                          <button type="button" class="btn btn-primary btn-sm" onclick="modalBase()" title="Editar Base"><i class="fas fa-pencil-alt fa-sm" aria-hidden="true"></i>
                          </button>
                        </td>
                      <?php } ?>
                    <?php }else{ ?>
                      <td id="celBase"></td>
                      <td id="celInfoBase" class="d-none">
                        <a tabindex="0" role="button" class="btn btn-info btn-sm infoBase">
                          <i class="fas fa-info-circle fa-sm" aria-hidden="true"></i>
                        </a>
                      </td>
                      <td id="celDelBase" class="d-none">
                        <button type="button" class="btn btn-danger btn-sm" onclick="fntDelBase()"><i class="fas fa-trash fa-sm" aria-hidden="true"></i>
                        </button>
                      </td>
                      <?php if($_SESSION['permisosMod']['d']){ ?>
                        <td id="celAddBase" class="d-none">
                          <button type="button" class="btn btn-primary btn-sm" onclick="modalBase()" title="Editar Base"><i class="fas fa-pencil-alt fa-sm" aria-hidden="true"></i>
                          </button>
                        </td>
                      <?php } ?>
                    <?php } ?>
                    <input type="hidden" name="idBase" id="idBase" value="<?php if(!empty($data['base'])){echo $data['base']['idbase'];}else{echo 0;} ?>">
                    <input type="hidden" name="baseAnteriorRA" id="baseAnteriorRA" value="">
                  <tr>
                    <td><h5 class="card-title">COBRADO:</h5></td>
                    <?php 
                      if(!empty($data['prestamos'])){
                    ?>
                    <td>
                      <?php 
                        if($data['pagamentos'] != 2){
                          $totalC = 0;
                          foreach ($data['prestamos']['pagos'] as $prestamo)
                          {
                            if($prestamo['datepago'] != $fecha_actual)
                            {
                              $totalC += $prestamo['pago'];
                            } 
                          }
                          
                          echo /*SMONEY.' '.*/$totalC;
                        }
                      ?>
                    </td>
                    <?php }else{ ?>
                    <td id="celCobrado"></td>
                    <td id="celInfoCobrado" class="d-none">
                      <a tabindex="0" role="button" class="btn btn-info btn-sm infoCobrado">
                        <i class="fas fa-info-circle fa-sm" aria-hidden="true"></i>
                      </a>
                    </td>
                    <?php } ?>
                    <input type="hidden" name="cobradoAnterior" id="cobrado" value="<?php if(!empty($totalC) && $totalC > 0) {echo $totalC;}else{echo 0;} ?>">
                  </tr>
                  <tr>
                    <td><h5 class="card-title">VENTAS:</h5></td>
                    <td id="celVentas">
                      <?php 
                        if($data['pagamentos'] != 2)
                        {
                          $totalV = 0;
                          foreach ($data['prestamos']['prestamos'] as $prestamo)
                          {
                            if($prestamo['status'] == 1 AND $data['pagamentos']['datepago'] == $prestamo['datecreated'])
                            {
                              $totalV += $prestamo['monto'];
                            }
                          }
                          if($data['pagamentos'] != 2 AND $totalV == 0)
                          {
                            echo /*SMONEY.' '.*/"0";
                          }else{
                            echo /*SMONEY.' '.*/$totalV; 
                          }
                        }else{
                          echo /*SMONEY.' '.*/'0';
                        }
                      ?>
                    </td>
                    <td id="celInfoVentas" class="d-none">
                      <a tabindex="0" role="button" class="btn btn-info btn-sm infoVentas">
                        <i class="fas fa-info-circle fa-sm" aria-hidden="true"></i>
                      </a>
                    </td>
                    <input type="hidden" name="ventasAnterior" id="ventasAnterior" value="<?= $totalV ?>">
                  </tr>
                  <tr>
                        <td><h5 class="card-title">GASTOS:</h5></td>
                        <?php 
                          if(!empty($data['prestamos'])){
                        ?>
                        <td>
                          <?php 
                            $totalG = 0;
                            foreach ($data['gastos'] as $gasto)
                            {
                              if($gasto['datecreated'] == $data['pagamentos']['datepago'])
                              {
                                $totalG += $gasto['monto'];
                                $gastoID = $gasto['idgasto'];
                              }
                            }
                            if($totalG > 0){
                              echo /*SMONEY.' '.*/$totalG;
                            }else{
                              echo /*SMONEY.' '.*/'0';
                            }
                          ?>
                        </td>
                        <td>
                          <button type="button" id="btnGasto del-gasto" class="btn btn-success btn-sm" onclick="modalGasto()"><i class="fas fa-plus-circle fa-sm" aria-hidden="true"></i></button>
                        </td>
                        <?php if($totalG > 0){ ?>
                          <td id="celDelGastosAnterior">
                            <div class="btn-group justify-content-right" role="group">
                              <button id="btnGroupDrop1" type="button" class="btn btn-danger btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-trash fa-sm" aria-hidden="true"></i>
                              </button>
                              <div id="d-delGastosAnterior" class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                <?php 
                                  $totalGA = 0;
                                  foreach ($data['gastos'] as $gasto)
                                  {
                                ?>
                                <button type="button" class="dropdown-item" onclick="fntDelGasto(<?= $gasto['idgasto']; ?>)">
                                  <i class="fa fa-times-circle fa-sm"></i> <?= ucwords($gasto['nombre']).' - './*SMONEY.' '.*/$gasto['monto']; ?>
                                </button>
                                <?php } ?>
                              </div>
                            </div>
                          </td> 
                        <?php } ?>
                        <?php }else{ ?>
                        <td id="celGastos"></td>
                        <td id="celAddGastosAnterior">
                          <button type="button" id="btnGasto" class="btn btn-success btn-sm" onclick="modalGasto()"><i class="fas fa-plus-circle fa-sm" aria-hidden="true"></i></button>
                        </td>
                        <?php } ?>
                        <td id="celDelGastos" class="d-none">
                          <div class="btn-group justify-content-right" role="group">
                            <button id="btnGroupDrop1" type="button" class="btn btn-danger btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              <i class="fas fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                            <div id="d-delGastos" class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                              
                            </div>
                          </div>
                        </td>
                        <input type="hidden" name="gastos" id="gastos" value="<?php if($totalG > 0){echo $totalG;}else{echo 0;} ?>">
                        <input type="hidden" name="idGasto" id="idGasto" value="<?php if($totalG > 0) {echo $gastoID;}else{echo 0;} ?>">
                      </tr>
                      <tr>
                        <td><h5 class="card-title">TOTAL:</h5></td>
                        <?php 
                          if(!empty($data['prestamos']))
                          {
                            $total = 0;
                            foreach ($data['prestamos']['pagos'] as $prestamo)
                            {
                              if($prestamo['datepago'] != $fecha_actual)
                              {
                                $total += $prestamo['pago'];
                              }
                            }



                            //if($total > 0)
                            //{
                              if(empty($data['base']))
                              {
                                if(!empty($data['resumenAnterior'])){
                                  $base = $data['resumenAnterior']['total'];
                                }else{
                                  $base = 0;
                                }
                              }else{
                               $base = $data['base']['monto'];
                              }
                              $totalResumen = ($base + $totalC) - ($totalG + $totalV);
                          //}
                        ?>
                        <td><?= /*SMONEY.' '.*/$totalResumen ?></td>
                        <?php }else{ ?>
                          <td id="celTotal"></td>
                        <?php } ?>
                        <input type="hidden" name="total" id="total" value="<?= $totalResumen; ?>">
                      </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <?php 
                if(!empty($data['prestamos'])){
              ?>
              <button id="btnActionFormRAnterior" type="submit" class="btn btn-warning">
                <i class="fas fa-check-circle" aria-hidden="true"></i>&nbsp;&nbsp;Guardar resumen anterior
              </button>
              <?php } ?>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</main>

<?php footerAdmin($data); ?>