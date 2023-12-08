<?php 
  headerAdmin($data);
  getModal('modalPrestamos',$data);
?>

<main class="app-content">
  <div class="app-title">
    <div>
        <h1>
          <i class="fas fa-user-tag"></i> <?= $data['page_title'] ?>
        </h1>
    </div>
  </div>

  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
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
            <div class="table-responsive">
              <table class="table table-striped" id="tablePrestamosFinalizados">
                <thead>
                  <tr>
                    <th>Inicio</th>
                    <th>Cierre</th>
                    <th>Cliente</th>
                    <th class="text-center">Abonos</th>
                    <th class="text-center">Detalles</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<?php footerAdmin($data); ?>