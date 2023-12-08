<!-- Modal agregar y editar usuario -->
<div class="modal fade" id="modalFormUsuario" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header headerRegister">
        <h5 class="modal-title" id="titleModal">Nuevo Usuario</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <form id="formUsuario" name="formUsuario" class="form-horizontal">
          <input type="hidden" id="idUsuario" name="idUsuario" value="">
          <p class="text-primary">Todos los campos son obligatorios.</p>
          <div class="form-row">
            <div class="form-group col-md-12">
              <label for="txtIdentificacion">Identificación</label>
              <input type="text" class="form-control" id="txtIdentificacion" name="txtIdentificacion" required="">
            </div>  
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="txtNombre">Nombre</label>
              <input type="text" class="form-control valid validText" id="txtNombre" name="txtNombre" required="">
            </div>  
            <div class="form-group col-md-6">
              <label for="txtApellido">Apellido</label>
              <input type="text" class="form-control valid validText" id="txtApellido" name="txtApellido" required="">
            </div>  
          </div>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label for="txtTelefono">Teléfono</label>
              <input type="text" class="form-control valid validNumber" id="txtTelefono" name="txtTelefono" required="" onkeypress="return controlTag(event)">
            </div>  
            <div class="form-group col-md-4">
              <label for="txtEmail">Email</label>
              <input type="email" class="form-control valid validEmail" id="txtEmail" name="txtEmail" required="" autocomplete="username">
            </div>
            <div class="form-group col-md-4">
              <label for="txtPassword">Password</label>
              <input type="password" class="form-control valid" id="txtPassword" name="txtPassword" autocomplete="current-password">
            </div>  
          </div>
          <div class="form-row justify-content-center">
            <div class="form-group col-md-4">
              <label for="listRolid">Tipo Usuario</label>
              <select class="form-control" data-live-search="true" id="listRolid" name="listRolid" required></select>
            </div>  
            <div class="form-group col-md-4">
              <label for="listStatus">Status</label>
              <select class="form-control selectpicker" id="listStatus" name="listStatus" required>
                <option value="1">Activo</option>
                <option value="2">Inactivo</option>
              </select>
            </div> 
            <div class="form-group col-md-4" id="divListRuta">
              <label for="listRuta">Ruta</label>
              <select class="form-control" data-live-search="true" id="listRuta" name="listRuta" required></select>
            </div> 
          </div>
          <hr>
          <div class="tile-footer">
            <button id="btnActionForm" class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i><span id="btnText">Guardar</span></button>&nbsp;&nbsp;&nbsp;
            <button class="btn btn-danger" type="button" data-dismiss="modal"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cerrar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal ver usuario-->
<div class="modal fade" id="modalViewUser" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header header-primary">
        <h5 class="modal-title" id="titleModal">Datos de Usuario</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
          <tbody>
            <tr>
              <td>Identificación:</td>
              <td id="celIdentificacion">03061998</td>
            </tr>
            <tr>
              <td>Nombres:</td>
              <td id="celNombres">Miguel</td>
            </tr>
            <tr>
              <td>Apellidos:</td>
              <td id="celApellidos">Tafur</td>
            </tr>
            <tr>
              <td>Teléfono:</td>
              <td id="celTelefono">31999508894</td>
            </tr>
            <tr>
              <td>Email (Usuario)</td>
              <td id="celEmail">Laka...</td>
            </tr>
            <tr>
              <td>Tipo usuario</td>
              <td id="celTipoUsuario">aaa</td>
            </tr>
            <tr>
              <td>Estado:</td>
              <td id="celEstado">aaa</td>
            </tr>
            <tr>
              <td>Fecha registro:</td>
              <td id="celFechaRegistro">aaa</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          Cerrar
        </button>
      </div>
    </div>
  </div>
</div>



