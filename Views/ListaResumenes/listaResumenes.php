<?php 
  headerAdmin($data);
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
              <table class="table table-bordered" id="tableResumenes">
                <thead>
                  <tr>
                    <th>Fecha</th>
                    <th>Base</th>
                    <th>Cobrado</th>
                    <th>Ventas</th>
                    <th>Gastos</th>
                    <th>Total</th>
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