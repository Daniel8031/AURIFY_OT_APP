<?php
    include "./vistas/inc/admin_security.php";
?>
<div class="full-box page-header">
    <h3 class="text-left text-uppercase">
        <i class="fas fa-sync fa-fw"></i> &nbsp; Actualizar Rol
    </h3>
    <p class="text-justify">
    <strong><?php echo $_SESSION['nombre_svi']." ".$_SESSION['apellido_svi']; ?></strong> Te encuentras en el apartado de Rooles donde podras visualizar y crear nuevos rooles, para asi llevar un buen control de las actividades de tus empleados.
</p>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs text-uppercase">
        <li>
            <a href="<?php echo SERVERURL; ?>cashier-new/">
                <i class="fas fa-plus-circle fa-fw"></i> &nbsp; Nuevo rol
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
    <?php
        include "./vistas/inc/btn_go_back.php";
        
        $datos_caja=$lc->datos_tabla("Unico","rol","rol_id",$pagina[1]);

        if($datos_caja->rowCount()==1){
            $campos=$datos_caja->fetch();
    ?>
    <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/rolAjax.php" method="POST" data-form="update" autocomplete="off">
        <input type="hidden" name="caja_id_up" value="<?php echo $pagina[1]; ?>" >
        <input type="hidden" name="modulo_rol" value="actualizar">
        <fieldset>
            <legend><i class="far fa-address-card"></i> &nbsp; Información del rol</legend>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="caja_numero" class="bmd-label-floating">Numero de rol <?php echo CAMPO_OBLIGATORIO; ?></label>
                            <input type="text" pattern="[0-9]{1,5}" class="form-control" name="caja_numero_up" value="<?php echo $campos['rol_numero']; ?>" id="rol_numero" maxlength="5" disabled>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="caja_nombre" class="bmd-label-floating">Nombre o código del rol <?php echo CAMPO_OBLIGATORIO; ?></label>
                            <input type="text" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ:# ]{3,70}" class="form-control" name="caja_nombre_up" value="<?php echo $campos['rol_nombre']; ?>" id="rol_nombre" maxlength="70">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="caja_estado" class="bmd-label-floating">Estado del rol</label>
                            <select class="form-control" name="caja_estado_up" id="caja_estado">
                                <?php
                                    $array_estado=["Habilitada","Deshabilitada"];
                                    echo $lc->generar_select($array_estado,$campos['rol_estado']);
                                ?>
                            </select>
                        </div>
                    </div>
                    
                </div>
            </div>
        </fieldset>
        <p class="text-center" style="margin-top: 40px;">
            <button type="submit" class="btn btn-raised btn-success btn-sm"><i class="fas fa-sync"></i> &nbsp; ACTUALIZAR</button>
        </p>
        <p class="text-center">
            <small>Los campos marcados con <?php echo CAMPO_OBLIGATORIO; ?> son obligatorios</small>
        </p>
    </form>
    <?php 
        }else{
            include "./vistas/inc/error_alert.php";
        } 
    ?>
</div>