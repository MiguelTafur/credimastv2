<?php 
  headerAdmin($data);
  getModal('modalClientes',$data);
?>

<main class="app-content">
  <div class="app-title">
    <div>
        <h1>
          <i class="fas fa-user-tag"></i> <?= $data['page_title'] ?>
        </h1>
    </div>
  </div>

  <div class="container">
    <div class="tile">
      <div class="tile-body">
        <?php 
          if($data['pagamentos'] != 2)
          {
        ?>
        <div class="alert alert-danger" role="alert">
          <strong>Error Resumen - </strong> Debes cerrar el resumen anterior!
        </div>
        <?php } ?>
        <form id="formPrestamos" name="formPrestamos" class="form-horizontal">
            <?php if($data['pagamentos'] != 2){ ?>
              <input type="hidden" name="fechaAnterior" id="fechaAnterior" value="<?= $data['pagamentos']['datepago']; ?>">
            <?php } ?>
            <div id="div-addPrestamo">
              <p class="text-primary">Los campos con asterisco (<span class="required">*</span>) son obligatorios.</p>
              <div class="form-row justify-content-center shadow-sm p-3 bg-white rounded">
                <div class="form-group col-md-3 text-center" id="listAddClients">
                  <i class="app-menu__icon fa fa-user" aria-hidden="true"></i>
                  <label for="listClientId">
                    <b>Cliente</b> 
                    <span class="required">*</span>&nbsp;&nbsp;
                    <button class="btn btn-sm btn-success" type="button" onclick="openModal();" title="Agregar Cliente"><i class="fas fa-plus-circle"></i></button>
                  </label>
                  <select class="form-control listClientId" id="listClientId" name="listClientId" required></select>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-3">
                  <label for="txtMonto">Monto <?= SMONEY; ?> <span class="required">*</span></label>
                  <input type="tel" class="form-control valid validNumber" id="txtMonto" name="txtMonto" required="">
                </div>  
                <div class="form-group col-md-3">
                  <label for="txtTaza">Tasa(%) <span class="required">*</span></label>
                  <input type="tel" class="form-control valid validNumber" id="txtTaza" name="txtTaza" required="">
                </div>
                <div class="form-group col-md-3">
                  <label for="listFormato">Formato <span class="required">*</span></label>
                  <select class="form-control" id="listFormato" name="listFormato" required>
                    <option value="1">Diario</option>
                    <option value="2">Semanal</option>
                    <option value="3">Mensual</option>
                  </select>
                </div>
                <div class="form-group col-md-3">
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
              <div class="form-row justify-content-right d-none" id="div-valorParcela">
                <div class="form-group col-md-12 text-right">
                  <h5 class="text-secondary"><b>Valor Parcela: <i>30</i></b></h5>
                </div>
              </div>
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
    </div>
  </div>
  
</main>



<?php footerAdmin($data); ?>