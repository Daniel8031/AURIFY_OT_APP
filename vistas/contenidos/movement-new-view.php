<div class="full-box page-header">
    <h3 class="text-left text-uppercase">
        <i class="fas fa-table fa-fw"></i> &nbsp; Nuevo Reporte
    </h3>
    <?php include "./vistas/desc/desc_movimiento.php"; ?>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs text-uppercase">
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>movement-new/">
                <i class="fas fa-table fa-fw"></i> &nbsp; Nuevo Reporte
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>movement-list/">
                <i class="fas fa-file-pdf fa-fw"></i> &nbsp; Reportes realizados
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>movement-search/">
                <i class="fas fa-search-dollar fa-fw"></i> &nbsp; Buscar reportes
            </a>
        </li>
    </ul>	
</div>

<div class="container-fluid">
<?php 


                        if(isset($_SESSION['factura']) && $_SESSION['factura']!=""){
                    ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <h4 class="alert-heading text-center">¡Reporte Generado!</h4>
                        <p class="text-center">El reporte se realizó con éxito. ¿Que desea hacer a continuación? </p>
                        <br>
                        <div class="container">
                            <div class="row">
                                <div class="col-12 col-md-6 text-center">
                                    <button type="button" class="btn btn-primary" onclick="print_ticket('<?php echo SERVERURL."pdf/ticket_".THERMAL_PRINT_SIZE."mm.php?code=".$_SESSION['factura']; ?>')" >
                                    <i class="fas fa-file-excel fa-4x"></i><br>
                                        Generar XML
                                    </buttona>
                                </div>
                                <div class="col-12 col-md-6 text-center">
                                    <button type="button" class="btn btn-primary" onclick="print_invoice('<?php echo SERVERURL."/pdf/invoice.php?code=".$_SESSION['factura']; ?>')" >
                                        <i class="fas fa-file-pdf fa-4x"></i><br>
                                        Imprimir reporte
                                    </button>
                                </div>
                                
                            </div>
                        </div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php
                            unset($_SESSION['factura']);
                        }
                    ?>

    <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/movimientoAjax.php" method="POST" data-form="save" autocomplete="off">
        <input type="hidden" name="modulo_movimiento" value="registrar">
        <fieldset>
            <legend><i class="fas fa-info-circle"></i> &nbsp; Generar reporte OT</legend>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label for="movimiento_tipo" class="bmd-label-floating">Tipo de reporte <?php echo CAMPO_OBLIGATORIO; ?></label>
                            <select class="form-control" name="movimiento_reg">
                                <option value="" selected="" >Seleccione una opción</option>
                                <?php
                                    $datos_caja=$lc->datos_tabla("Normal","tipo_detalle","*",0);

                                    while($campos_caja=$datos_caja->fetch()){
                                        echo '<option value="'.$campos_caja['nombre'].'">('.$campos_caja['id_tipo'].' - '.$campos_caja['nombre'].')</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label for="movimiento_cantidad" class="bmd-label-floating">Folio <?php echo CAMPO_OBLIGATORIO; ?></label>
                            <input type="text" class="form-control" name="folio" value="00000"  maxlength="25">
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label for="movimiento_cantidad" class="bmd-label-floating">Sala <?php echo CAMPO_OBLIGATORIO; ?></label>
                            <input type="text" class="form-control" name="sala" value="Casino"  maxlength="25">
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label for="movimiento_cantidad" class="bmd-label-floating">Hora de Entrada <?php echo CAMPO_OBLIGATORIO; ?></label>
                            <input type="time" class="form-control" name="hora_entrada"   maxlength="25">
                        </div>
                    </div>
                    
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label for="movimiento_cantidad" class="bmd-label-floating">Hora de Salida <?php echo CAMPO_OBLIGATORIO; ?></label>
                            <input type="time" class="form-control" name="hora_salida"   maxlength="25">
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label for="movimiento_cantidad" class="bmd-label-floating">Fecha <?php echo CAMPO_OBLIGATORIO; ?></label>
                            <input type="date" class="form-control" name="fecha"  id="movimiento_cantidad" maxlength="25">
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label for="movimiento_cantidad" class="bmd-label-floating">Ciudad <?php echo CAMPO_OBLIGATORIO; ?></label>
                            <input type="text" class="form-control" name="city" value="Mexico"  maxlength="25">
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label for="movimiento_tipo" class="bmd-label-floating">Tipo de atenciòn <?php echo CAMPO_OBLIGATORIO; ?></label>
                            <select class="form-control" name="movimiento_tipo_reg" id="movimiento_tipo">
                            <option value="" selected="" >Seleccione una opción</option>
                                <option value="Programada">PROGRAMADA</option>
                                <option value="Incidencia">INCIDENCIA</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label for="movimiento_cantidad" class="bmd-label-floating">Ingeniero <?php echo CAMPO_OBLIGATORIO; ?></label>
                            <input type="text" class="form-control" name="ingeniero" value="<?php echo $_SESSION['nombre_svi']." ".$_SESSION['apellido_svi']; ?>" id="movimiento_cantidad" maxlength="25">
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label for="movimiento_cantidad" class="bmd-label-floating">Responsable de la sala <?php echo CAMPO_OBLIGATORIO; ?></label>
                            <input type="text" class="form-control" name="respon" value="alguien@example"  maxlength="25">
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label for="movimiento_cantidad" class="bmd-label-floating">Terminales inoperativas <?php echo CAMPO_OBLIGATORIO; ?></label>
                            <input type="number" class="form-control" name="termi" value="0"  maxlength="25">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="movimiento_motivo" class="bmd-label-floating">Descripcion de actividades realizadas <?php echo CAMPO_OBLIGATORIO; ?></label>
                            <input type="text" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{5,70}" class="form-control" name="descripcion"  maxlength="70">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="movimiento_motivo" class="bmd-label-floating">Pieza solicitada y numero de pedido (opcional)</label>
                            <input type="text" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{5,70}" class="form-control" name="pieza"  maxlength="70">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="movimiento_motivo" class="bmd-label-floating">Comentarios deadicionales (opcional)</label>
                            <input type="text" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{5,70}" class="form-control" name="comentarios"  maxlength="70">
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


<?php
	include "./vistas/inc/print_invoice_script.php";
?>