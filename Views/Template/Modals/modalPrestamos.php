<!-- Modal ver prestamo-->
<div class="modal fade" id="modalViewPrestamo" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header header-primary">
        <h5 class="modal-title" id="titleModal">Datos de Préstamo</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="idPrestamoP" name="idPrestamoP" value="">
        <input type="hidden" id="clientePagos" name="clientePagos" value="">
        <table id="tableViewPrestamo" class="table table-striped">
          <tbody>
            <tr>
              <td>Fecha Inicio:</td>
              <td id="celFecha"></td>
            </tr>
            <tr>
              <td>Fecha Vencimiento:</td>
              <td id="celVence"></td>
            </tr>
            <tr id="trDiaPagamento" class="d-none">
              <td>Dia Pagamento:</td>
              <td  class="text-uppercase"><p id="tdDiaPagamento" class="font-weight-bold m-0"></p></td>
            </tr>
            <tr>
              <td>Nombre:</td>
              <td id="celNombres"></td>
            </tr>
            <tr>
              <td>Negocio:</td>
              <td id="celNegocio"></td>
            </tr>
            <tr>
              <td>Valor del crédito:</td>
              <td id="celMonto"></td>
            </tr>
            <tr>
              <td>Formato:</td>
              <td id="celFormato"></td>
            </tr>
            <tr>
              <td>Tasa:</td>
              <td id="celTaza">a</td>
            </tr>
            <tr>
              <td>Plazo:</td>
              <td id="celPlazo"></td>
            </tr>
            <tr>
              <td>Valor Parcela:</td>
              <td id="celParcela"></td>
            </tr>
            <tr>
              <td>Saldo:</td>
              <td id="celSaldo"></td>
            </tr>
            <tr>
              <td>Pagado:</td>
              <td id="celPagado">a</td>
            </tr>
            <tr>
              <td>Parcelas Pendientes:</td>
              <td id="celPendiente"></td>
            </tr>
            <tr>
              <td>Parcelas Canceladas:</td>
              <td id="celCancelado"></td>
            </tr>
            <tr>
              <td>Pagos realizados:</td>
              <td id="celPagado"><button type="button" class="btn btn-info btn-sm" onclick="listPagos()"><i class="far fa-eye"></i> Ver pagos</button></td>
            </tr>
            <tr id="trObservacion">
              <td>Observación:</td>
              <td id="celObservacion"></td>
            </tr>
          </tbody>
        </table>
        <div id="containerPagos">
          <div class="row">
            <div class="col-3">
              <button class="btn btn-secondary btn-sm" onclick="backFntViewPrestamo()"><i class="fas fa-arrow-left" aria-hidden="true"></i></button>
            </div>
            <div class="col-9 text-right">
              CLIENTE: <mark id="clientePago"></mark>
            </div>
          </div>
          <hr>
          <div class="row justify-content-center">
            <div class="col-md-9">
              <div class="tile">
                <div class="tile-body">
                  <div class="table-responsive">
                    <table class="table table-striped" id="tablePagos">
                      <thead>
                        <tr>
                          <!--<th>ID</th>-->
                          <th class="text-center">Fecha abono</th>
                          <th class="text-center">Abono</th>
                        </tr>
                      </thead>
                      <tbody id="listaPagos">
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          Cerrar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal agregar Base -->
<div class="modal fade myModal" id="modalFormBase" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="titleModal">Nueva base</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="formBase" name="formBase" class="form-horizontal">
          <?php 
            if($data['pagamentos'] != 2)
            {
          ?>
          <input type="hidden" name="fechaAnterior" value="<?= $data['pagamentos']; ?>">
          <?php } ?>
          <div class="row">
            <div class="form-group">
              <div class="col-12">
                <label for="txtBase" class="col-form-label bmd-label-static"><?= SMONEY; ?> Base:</label>
                <input type="number" name="txtBase" id="txtBase" class="form-control form-control-sm" required="">
              </div>
            </div>
          </div>
          <!-- <div class="row">
            <div class="col-md-12">
              <label for="txtObservacion" class="col-form-label bmd-label-static">Observación</label>
              <textarea class="form-control" id="txtObservacion" name="txtObservacion"></textarea>
            </div>
          </div> -->
          <hr>
          <div class="tile-footer">
            <button id="btnActionForm" class="btn btn-primary btn-sm" type="submit"><i class="fa fa-check-circle"></i><span id="btnText">Guardar</span></button>&nbsp;&nbsp;&nbsp;
            <button class="btn btn-danger btn-sm" type="button" data-dismiss="modal"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cerrar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal agregar Gasto -->
<div class="modal fade myModal" id="modalFormGasto" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="titleModal">Nuevo Gasto</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="formGasto" name="formGasto" class="form-horizontal">
          <?php 
            if($data['pagamentos'] != 2)
            {
          ?>
          <input type="hidden" name="fechaAnterior" id="fechaAnterior" value="<?= $data['pagamentos']; ?>">
          <?php } ?>
          <div class="row">
            <div class="form-group">
              <div class="col">
                <label for="txtNombre" class="col-form-label bmd-label-static">Nombre</label>
                <input type="text" name="txtNombre" id="txtNombre" class="form-control form-control-sm valid" required="">
              </div>
            </div>
            <div class="form-group">
              <div class="col">
                <label for="txtGasto" class="col-form-label bmd-label-static"><?= SMONEY; ?> Gasto:</label>
                <input type="number" name="txtGasto" id="txtGasto" class="form-control form-control-sm valid validNumber" required="">
              </div>
            </div>
          </div>
          <hr>
          <div class="tile-footer">
            <button id="btnActionForm" class="btn btn-warning btn-sm" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i><span id="btnText">Guardar</span></button>&nbsp;&nbsp;&nbsp;
            <button class="btn btn-danger btn-sm" type="button" data-dismiss="modal"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cerrar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal enrutar cliente -->
<div class="modal fade" id="modalFormEnrutar" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="titleModal">Enrutar clientes</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <ul class="list-group text-center" id="sortable" class="sortable">
          
        </ul>
        <hr>
        <div class="tile-footer text-right">
          <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal pagos y préstamos finalizados -->
<div class="modal fade myModal" id="modalFormPagosFinalizados" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title AF" id="titleModal">Abonos finalizados</h5>
        <h5 class="modal-title PF d-none" id="titleModal">Préstamo finalizado</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="tableViewPagosFinalizados" class="row justify-content-center">
          <!--<div class="col-12 text-right">
            <input type="hidden" id="clientePagosFinalizados" name="clientePagosFinalizados" value="">
            CLIENTE: <mark id="clientePagoFinalizado"></mark>
          </div>-->
        </div>
        <div id="idPagosFinalizados" class="row justify-content-center">
          <div class="col-md-9">
            <div class="tile">
              <div class="tile-body">
                <div class="table-responsive">
                  <table class="table table-striped" id="tablePagosFinalizados">
                    <thead>
                      <tr>
                        <th class="text-center">Fecha</th>
                        <th class="text-center">Abono</th>
                      </tr>
                    </thead>
                    <tbody id="listaPagosFinalizados">
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <table id="tableViewPrestamoFinalizado" class="table table-striped d-none">
          <tbody>
            <tr>
              <td>Nombre:</td>
              <td id="celNombresFinalizado">Miguel</td>
            </tr>
            <tr>
              <td>Negocio:</td>
              <td id="celNegocioFinalizado">Tafur</td>
            </tr>
            <tr>
              <td>Valor del crédito:</td>
              <td id="celMontoFinalizado">31999508894</td>
            </tr>
            <tr>
              <td>Formato:</td>
              <td id="celFormatoFinalizado">Laka...</td>
            </tr>
            <tr>
              <td>Tasa:</td>
              <td id="celTazaFinalizado">aaa</td>
            </tr>
            <tr>
              <td>Plazo:</td>
              <td id="celPlazoFinalizado">aaa</td>
            </tr>
            <tr>
              <td>Parcela:</td>
              <td id="celParcelaFinalizado">aaa</td>
            </tr>
            <tr>
              <td>Pagado:</td>
              <td id="celPagadoFinalizado">aaa</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal renovar prestamo-->
<div class="modal fade" id="modalRenovarPrestamo" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header header-primary">
        <h5 class="modal-title" id="titleModal">Renovar Préstamo</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="formRenovarPrestamo" name="formRenovarPrestamo" class="form-horizontal">
          <?php if($data['pagamentos'] != 2){ ?>
            <input type="hidden" name="fechaAnterior" id="fechaAnterior" value="<?= $data['pagamentos']; ?>">
          <?php } ?>
          <input type="hidden" name="inputClienteRenovar" id="inputClienteRenovar">
          <div id="div-addPrestamo">
            <p class="text-primary">Los campos con asterisco (<span class="required">*</span>) son obligatorios.</p>
            <div class="form-row justify-content-center shadow-sm p-3 mb-5 bg-white rounded">
              <div class="form-group mb-0 col-md-9 text-center" id="listAddClients">
                <p><i class="app-menu__icon fa fa-user" aria-hidden="true"></i><b>Cliente</b></p>
                <p class="" id="clienteRenovar"></p>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="txtMonto">Monto <?= SMONEY; ?> <span class="required">*</span></label>
                <input type="tel" class="form-control valid validNumber" id="txtMonto" name="txtMonto" required="">
              </div>  
              <div class="form-group col-md-6">
                <label for="txtTaza">Tasa(%) <span class="required">*</span></label>
                <input type="tel" class="form-control valid validNumber" id="txtTaza" name="txtTaza" required="">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="listFormato">Formato <span class="required">*</span></label>
                <select class="form-control" id="listFormato" name="listFormato" required>
                  <option value="1">Diario</option>
                  <option value="2">Semanal</option>
                  <option value="3">Mensual</option>
                </select>
              </div>
              <div class="form-group col-md-6">
                <label for="txtPlazo">Plazo(Días) <span class="required">*</span></label>
                <input type="tel" class="form-control valid validNumber" id="txtPlazo" name="txtPlazo" required="">
              </div>
            </div>
            <div class="form-row">
              <div class="col-md-12">
                <label for="txtObservacion">Observación</label>
                <textarea class="form-control" id="txtObservacion" name="txtObservacion"></textarea>
              </div>
            </div>
            <br>
            <div class="form-row">
              <div class="col-md-12" style="align-items: center">
                <input type="checkbox" name="pagamentoSabado" id="pagamentoSabado" style="margin-right: 7px;">
                <label for="pagamentoSabado">Paga 5 días semanales</label>
              </div>
            </div>
          </div>
          <br>
          <div class="tile-footer">
            <button id="btnActionForm" class="btn btn-warning" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i><span id="btnText">Guardar</span></button>&nbsp;&nbsp;&nbsp;
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          Cerrar
        </button>
      </div>
    </div>
  </div>
</div>



