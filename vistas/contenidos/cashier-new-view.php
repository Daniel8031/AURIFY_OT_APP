<?php
    include "./vistas/inc/admin_security.php";
?>
<div class="full-box page-header">
    <h3 class="text-left text-uppercase">
        <i class="fas fa-plus-circle fa-fw"></i> &nbsp; Nuevo Rol
    </h3>
    <p class="text-justify">
    <strong><?php echo $_SESSION['nombre_svi']." ".$_SESSION['apellido_svi']; ?></strong> Te encuentras en el apartado de Rooles donde podras visualizar y crear nuevos rooles, para asi llevar un buen control de las actividades de tus empleados.
</p>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs text-uppercase">
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>cashier-new/">
                <i class="fas fa-plus-circle fa-fw"></i> &nbsp; Nuevo Rol
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>cashier-list/">
                <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Lista de rooles
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>cashier-search/">
                <i class="fas fa-search fa-fw"></i> &nbsp; Buscar rol
            </a>
        </li>
    </ul>	
</div>

<div class="container-fluid">
    <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/rolAjax.php" method="POST" data-form="save" autocomplete="off">
        <input type="hidden" name="modulo_rol" value="registrar">
        <fieldset>
            <legend><i class="far fa-address-card"></i> &nbsp; Información del Rol</legend>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="caja_numero" class="bmd-label-floating">Numero de rol <?php echo CAMPO_OBLIGATORIO; ?></label>
                            <input type="text" pattern="[0-9]{1,5}" class="form-control" name="caja_numero_reg" id="caja_numero" maxlength="5">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="caja_nombre" class="bmd-label-floating">Nombre o código del rol <?php echo CAMPO_OBLIGATORIO; ?></label>
                            <input type="text" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ:# ]{3,70}" class="form-control" name="caja_nombre_reg" id="caja_nombre" maxlength="70">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="caja_estado" class="bmd-label-floating">Estado del rol</label>
                            <select class="form-control" name="caja_estado_reg" id="caja_estado">
                                <option value="Habilitada" selected="" >Habilitada</option>
                                <option value="Deshabilitada">Deshabilitada</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
        <p class="text-center" style="margin-top: 40px;">
            <button type="reset" class="btn btn-raised btn-secondary btn-sm"><i class="fas fa-paint-roller"></i> &nbsp; LIMPIAR</button>
            &nbsp; &nbsp;
            <button type="submit" class="btn btn-raised btn-info btn-sm"><i class="far fa-save"></i> &nbsp; GUARDAR</button>
        </p>
        <p class="text-center">
            <small>Los campos marcados con <?php echo CAMPO_OBLIGATORIO; ?> son obligatorios</small>
        </p>
    </form>
</div>