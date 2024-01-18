<!-- Modal detalle Resumen -->
<div class="modal fade" id="modalDetalleR" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="titleModal">Resumenes</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
          <div class="row">
            <div class="col-10">
            <input type="text" readonly class="form-control" id="fechaResumen" placeholder="Selecciona una fecha">
            </div>
            <div class="col-2">
              <button type="button" class="btn btn-warning mb-2" onclick="fntSearchResumenD()"><i class="fas fa-search" aria-hidden="true"></i></button>
            </div>
          </div>
        </form>
        <div id="divResumenD">
          <div class="table-responsive">
            <table class="table">
              <thead>
              <tr class="text-center">
                  <th>Fecha</th>
                  <th>Base</th>
                  <th>Cobrado</th>
                  <th>Ventas</th>
                  <th>Gastos</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody id="datosResumenD"></tbody>
            </table>
          </div>
          <br>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal detalle Préstamos Finalizados -->
<div class="modal fade" id="modalDetallePF" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="titleModal">Préstamos Finalizados</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
          <div class="row mb-3">
            <div class="col-7">
              <select class="form-control text-center listClientId" id="listClientId" name="listClientId" required></select>
            </div>
            <div class="col-5 text-left">
              <button type="button" class="btn btn-warning btn-sm" onclick="fntSearchPrestamosFD()"><i class="fas fa-search" aria-hidden="true"></i></button>
            </div>
          </div>
        </form>
        <div id="divPrestamosFD" class="d-none">
          <table class="table">
            <thead>
              <tr class="text-center">
                <th>Cierre</th>
                <th>Cliente</th>
                <th>Pagamentos</th>
                <th>Detalles</th>
              </tr>
            </thead>
            <tbody id="datosPrestamosFD"></tbody>
          </table>
          <br>
        </div>
      </div>
      <div class="modal-footer d-none" id="divJurosD" style="margin-top: -30px;">
          <b>TOTAL JUROS: <i><mark id="markJuros"></mark></i></b>
      </div>
      <div class="modal-footer justify-content-center d-none" id="sinDatosD" style="margin-top: -30px;">
          <b>No hay datos</b>
      </div>
    </div>
  </div>
</div>

<!-- Modal detalle Cobrado -->
<div class="modal fade" id="modalDetalleC" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="titleModal">Cobrado Detallado</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
          <div class="row">
            <div class="col-10">
            <input type="text" readonly class="form-control" id="fechaCobrado" placeholder="Selecciona una fecha">
            </div>
            <div class="col-2">
              <button type="button" class="btn btn-warning mb-2" onclick="fntSearchCobradoD()"><i class="fas fa-search" aria-hidden="true"></i></button>
            </div>
          </div>
        </form>
        <div id="divCobradoD">
          <table class="table">
            <thead>
            <tr class="text-center">
                <th>Fecha</th>
                <th>Valor</th>
                <th>Clientes</th>
              </tr>
            </thead>
            <tbody id="datosCobradoD"></tbody>
          </table>
          <br>
          <div class="tile-footer text-right">
            <b>TOTAL COBRADO: <i><mark id="markCobrado"></mark></i></b>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal detalle Ventas -->
<div class="modal fade" id="modalDetalleV" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="titleModal">Ventas Detalladas</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
          <div class="row">
            <div class="col-10">
            <input type="text" readonly class="form-control" id="fechaVentas" placeholder="Selecciona una fecha">
            </div>
            <div class="col-2">
              <button type="button" class="btn btn-warning mb-2" onclick="fntSearchVentasD()"><i class="fas fa-search" aria-hidden="true"></i></button>
            </div>
          </div>
        </form>
        <div id="divVentasD">
          <table class="table">
            <thead>
            <tr class="text-center">
                <th>Fecha</th>
                <th>Valor</th>
                <th>Clientes</th>
              </tr>
            </thead>
            <tbody id="datosVentasD"></tbody>
          </table>
          <br>
          <div class="tile-footer text-right" id="divVentasD">
            <b>TOTAL VENTAS: <i><mark id="markVentas"></mark></i></b>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal detalle Gastos -->
<div class="modal fade" id="modalDetalleG" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="titleModal">Gastos Detallados</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
          <div class="row">
            <div class="col-10">
            <input type="text" readonly class="form-control" id="fechaGastos" placeholder="Selecciona una fecha">
            </div>
            <div class="col-2">
              <button type="button" class="btn btn-warning mb-2" onclick="fntSearchGastosD()"><i class="fas fa-search" aria-hidden="true"></i></button>
            </div>
          </div>
        </form>
        <div id="divGastosD">
          <table class="table">
            <thead>
            <tr class="text-center">
                <th>Fecha</th>
                <th>Valor</th>
                <th>Gastos</th>
              </tr>
            </thead>
            <tbody id="datosGastosD"></tbody>
          </table>
          <br>
          <div class="tile-footer text-right" id="divGastosD">
            <b>TOTAL GASTOS: <i><mark id="markGastos"></mark></i></b>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>