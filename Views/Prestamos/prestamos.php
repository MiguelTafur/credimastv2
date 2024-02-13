<?php
  headerAdmin($data);
  getModal('modalPrestamos',$data);
  $fecha_actual = date("Y-m-d");
?>
<main class="app-content">

  <?php
    if($data['pagamentos'] == 2){
  ?>

    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top-2" style="border-radius: 50px;">
      <div class="container-fluid">
        <ul class="navbar-nav mt-2">
          <li class="nav-item mr-5">
            <p class="h5 ml-3">Cobrado: <span class="badge text-success"><i id="iCobrado">0</i></span></p>
          </li>
          <li class="nav-item">
            <p class="h5 ml-3">Ventas: <span class="badge text-info"><i id="iVentas">0</i></span></p>
          </li>
        </ul>
        <!--<p class="pt-3 h5">Cobrado: </p>-->
        <div class="ml-auto">
          <button id="btnPayAll" class="btn btn-success my-2 my-sm-0 text-right" style="border-radius: 50%; padding: 10px;" onclick="fntPayAll();" title="Agregar varios pagos">
            <i class="fas fa-hand-holding-usd fa-lg" aria-hidden="true"></i>
          </button>
        </div>
      </div>
    </nav>
  <?php } ?>
  <br>
  <div class="d-flex justify-content-center pl-3">
    <a href="<?= base_url() ?>/resumen" class="font-italic font-weight-bold text-dark"><u><i class="fa fa-share" aria-hidden="true"></i>&nbsp;&nbsp;Ir al Resumen</u></a>
  </div>
  <br>
  <div class="container-fuid">
    <div class="row justify-content-center">
      <div class="col-lg-12">
        <div class="tile" style="border-radius: 10px;">
          <div class="tile-body">
            <!--TABLA PRESTAMOS MOVIL Y ESCRITORIO-->
            <?php
              if($data['pagamentos'] == 2)
              {
            ?>
            <div class="table-responsive">
              <div id="divTablasPrestamos">
                <div id="TP" class="d-none">
                  <table class="table table-borderless " id="tablePrestamos">
                    <thead>
                      <tr class="text-center">
                        <!--<th>ID</th>-->
                        <th>CRÉDITO</th>
                        <th>FORMATO</th>
                        <th>SALDO</th>
                        <th>CLIENTE</th>
                        <th class="text-center">ABONO</th>
                        <th class="text-center">DETALLES</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div id="divPrestamos">
                <div id="TPM">
                  <table class="table table-borderless" id="tablePrestamosMovil">
                    <thead>
                      <tr>
                        <th>CLIENTE</th>
                        <th>SALDO</th>
                        <th class="text-center">ABONO</th>
                        <th class="text-center">DETALLES</th>
                        <th>FORMATO</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <?php }else{ ?>

              <!-- SECCIÓN PRESTAMOS ANTERIOR -->
              <div class="alert alert-danger" role="alert">
                <strong>Error Resumen - </strong> Debes cerrar el resumen de la fecha: &nbsp;&nbsp;<strong><i><?= date("d-m-Y", strtotime($data['pagamentos'])) ?></i></strong>
              </div>

              <div id="resumenPendiente">
                <?php
                  $totalNav = 0;
                  foreach ($data['prestamos']['prestamos'] as $prestamo)
                  {
                    if(($prestamo['datecreated'] == $prestamo['datepago'] AND $prestamo['status'] != 0) || (empty($prestamo['datepago']) AND $prestamo['status'] != 0))
                    {
                      $totalNav += $prestamo['monto'];
                    }
                  }
                  if($totalNav > 1){
                ?>
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item font-weight-bold font-italic" aria-current="page"><h5>VENTAS</h5></li>
                  </ol>
                </nav>
                <!-- Ventas Pendientes -->
                <table class="table table-borderless" id="divTablaPrestamosPendiente">
                  <thead>
                    <tr class="text-center">
                      <th class="text-left">CLIENTE</th>
                      <th>FECHA</th>
                      <th>ABONO</th>
                      <th>ELIMINAR</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      foreach ($data['prestamos']['prestamos'] as $prestamo)
                      {
                        if($data['pagamentos'] == $prestamo['datecreated'] AND $prestamo['status'] != 0){

                    ?>
                    <tr class="text-center">
                      <td class="text-left"><?= strtok($prestamo['nombres'], " ").' - '.$prestamo['apellidos'] ?></td>
                      <td class="text-danger"><?= date("d-m-Y", strtotime($prestamo['datecreated'])) ?></td>
                      <td><?= /*SMONEY.*/' '.$prestamo['monto'] ?></td>
                      <td>
                        <button class="btn btn-danger btn-sm" onclick="fntDelPrestamoAnterior(<?= $prestamo['idprestamo']; ?>)" title="Eliminar Prestamo">
                          <i class="far fa-trash-alt"></i>
                        </button>
                      </td>
                    </tr>
                    <?php } ?>
                    <?php } ?>
                  </tbody>
                </table>
                <hr>
                <?php } ?>
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb justify-content-center">
                  <li class="breadcrumb-item font-weight-bold font-italic" aria-current="page"><h5>PAGAMENTOS</h5></li>
                  </ol>
                </nav>
                <div class="table-responsive">
                  <!-- Prestamos pendientes -->
                  <table class="table " id="divTablaCobradoPendiente">
                    <thead>
                      <tr class="text-center">
                        <th class="text-left">CLIENTE</th>
                        <th>SALDO</th>
                        <th>ABONO</th>
                        <th>DETALLES</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        foreach ($data['prestamos']['prestamos'] as $prestamo)
                        {
                          if($prestamo['status'] == 1 || ($prestamo['status'] == 2 && $prestamo['datefinal'] == $prestamo['datepago']))
                          {
                      ?>
                      <tr class="text-center">
                        <!-- NOMBRES -->
                        <td class="text-left"><?= strtok($prestamo['nombres'], " ").' - '.$prestamo['apellidos'] ?></td>
                        <!-- SALDO -->
                        <td>
                          <h5><span class="badge badge-danger badge-pill"><?= /*SMONEY.*/' '.$prestamo['total'] ?></span></h5>
                        </td>
                        <!-- ABONOS -->
                        <td>
                          <?php
                            if($prestamo['datepago'] == $data['pagamentos'] && $prestamo['total'] == 0){
                          ?>
                              <button class="btn btn-success btn-sm" onclick="fntRenovarPrestamo(<?= $prestamo['idprestamo'] ?>, '<?= $prestamo['datefinal'] ?>')">RENOVAR</button> &nbsp;&nbsp;
                              <button class="btn btn-danger btn-sm" onclick="fntDelPagoFinalizado(<?= $prestamo['pagoid'] ?>)" title="Eliminar pago">
                                  <?= /*SMONEY.*/' '.$prestamo['pago'] ?>&nbsp;
                              </button>
                          <?php
                            }else if($prestamo['pagoid'] != 0 AND $prestamo['datepago'] == $data['pagamentos']){
                          ?>
                            <button class="btn btn-success btn-sm" onclick="fntDelPagoFinalizado(<?= $prestamo['pagoid'] ?>)" title="Eliminar pago">
                              <?= /*SMONEY.*/' '.$prestamo['pago'] ?>&nbsp;
                            </button>

                          <?php
                            }else{
                          ?>
                            <div class="text-center divPagoPrestamo">
                              <input type="hidden" name="fechaAnterior" id="fechaAnterior" value="<?= $data['pagamentos'] ?>">
                              <input type="number" class="inpPago <?= $prestamo['idprestamo']; ?>" id="<?= $prestamo['idprestamo']; ?>" style="width: 65px; height: 35px; padding: 5px" onkeypress="return controlTag(event)">
                              <button id="" class="btn btn-secondary btn-sm pagoPrestamo P-5" title="Agregar Pago" onclick="fntPagoPrestamo(<?= $prestamo['idprestamo']; ?>)"><i class="fas fa-hand-holding-usd"></i> Pagar
                              </button>
                            </div>
                          <?php
                           }
                          ?>
                        </td><!-- FIN TD ABONOS -->
                        <td>
                          <button class="btn btn-info " onclick="fntViewPrestamo(<?= $prestamo['idprestamo'] ?>)" title="Ver Prestamo"><i class="far fa-eye"></i></button>
                        </td>
                      </tr>
                      <?php }} ?>
                    </tbody>
                  </table>
                </div>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<?php footerAdmin($data); ?>
