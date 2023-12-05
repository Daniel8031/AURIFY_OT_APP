<div class="full-box page-header">
    <h3 class="text-left text-uppercase">
		<i class="fas fa-gamepad fa-fw"></i> &nbsp; Agregar grupo de juegos
    </h3>
    <?php include "./vistas/desc/desc_venta.php"; ?>
</div>
<?php
                    if (isset($_SESSION['grupo_codigo']) && $_SESSION['grupo_codigo'] != "") {
                        ?>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <h4 class="alert-heading text-center">Grupo creado Exitosamente!</h4>
                            <p class="text-center">Los juegos fueron añadidos correctamente!. ¿Que desea hacer a continuación? </p>
                            <br>
                            <div class="container">
                                <div class="row">
                                    <div class="col-12 col-md-6 text-center">
                                        <button type="button" class="btn btn-primary"
                                            onclick="print_ticket('<?php echo SERVERURL . "pdf/ticket_" . THERMAL_PRINT_SIZE . "mm.php?code=" . $_SESSION['grupo_codigo']; ?>')">
                                            <i class="fas fa-users fa-4x"></i><br>
                                            Visualizar Grupos
                                            </buttona>
                                    </div>
                                    <div class="col-12 col-md-6 text-center">
                                        <button type="button" class="btn btn-primary"
                                            onclick="print_invoice('<?php echo SERVERURL . "pdf/xmlgenerator.php?code=" . $_SESSION['grupo_codigo']; ?>')">
                                            <i class="fas fa-file-excel fa-4x"></i><br>
                                            Generar XML del grupo creado
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                  
                        <?php
                        unset($_SESSION['grupo_codigo']);
                    }
                    ?>

<div class="container-fluid">
	<form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php" data-form="save" method="POST" autocomplete="off" >
        <input type="hidden" name="modulo" value="registrar">
		<div class="container-fluid">
			<div class="row justify-content-md-center">
				<div class="col-12 col-md-4">
					<div class="form-group">
						<label for="fecha_inicio" ><strong class="text-uppercase">Nombre del Grupo de Juego</strong></label>
						<input type="text" class="form-control" name="grupo_game" id="grupo_game" maxlength="30">
                        
					</div>
				</div>
				<div class="col-12 col-md-4">
					<div class="form-group">
						<label for="fecha_final" ><strong class="text-uppercase">Tema del grupo de juegos</strong></label>
						<!-- <input type="txt" class="form-control" name="fecha_final" id="fecha_final" maxlength="30"> -->
                        <select class="form-control" id="tema_game" name="tema_game" required>
                            <option value="">&nbsp;Seleccione skin de Juego</option>
                            <option value="Default">&nbsp;(Default)</option>
                            <option value="MultiGame2_GameMaster">&nbsp;(MultiGame2_GameMaster)</option>
                            <option value="MultiGame2_Quad">&nbsp;(MultiGame2_Quad)</option>
                            <option value="MultiGameScreen">&nbsp;(MultiGameScreen)</option>
                            <option value="MultiGame2_XTREME">&nbsp;(MultiGame2_XTREME)</option>
                            <option value="MultiGame2">&nbsp;(MultiGame2)</option>
                            <option value="MultiGame2_FlamingJackpots">&nbsp;(MultiGame2_FlamingJackpots)</option>
                            <option value="MultiGame2_FuGold">&nbsp;(MultiGame2_FuGold)</option>
                            <option value="MultiGame2_LuckyLink">&nbsp;(MultiGame2_LuckyLink)</option>
                            <option value="MultiGame2_SkillPack1">&nbsp;(MultiGame2_SkillPack1)</option>
                            <option value="MultiGame2_SkillPack2">&nbsp;(MultiGame2_SkillPack2)</option>
                            <option value="MultiGame2_SkillPack3">&nbsp;(MultiGame2_SkillPack3)</option>
                            <option value="MultiGame2_RedHotRespins">&nbsp;(MultiGame2_RedHotRespins)</option>
                            <option value="MultiGame_LinkUpdraft">&nbsp;(MultiGame_LinkUpdraft)</option>
                            <option value="MultiGame_HyperRespin">&nbsp;(MultiGame_HyperRespin)</option>
                        </select>
                        </select>
					</div>
				</div>
				<div class="col-12">
					<p class="text-center" style="margin-top: 40px;">
						<button type="submit" class="btn btn-raised btn-info"><i class="fas fa-plus-circle"></i> &nbsp; Crear</button>
					</p>
				</div>
			</div>
		</div>
	</form>
</div>     
    <div class="container-fluid">
    <?php
        require_once "./controladores/ventaControlador.php";
        $ins_venta = new ventaControlador();

        echo $ins_venta->paginador_controlador($pagina[1],15,$pagina[0],"Listado","","");
    ?>
</div>
</div>
 </div>
<?php
	include "./vistas/inc/print_invoice_script.php";
?>

<script>
function printSelectedGroups() {
    var selectedGroups = document.querySelectorAll('input[name="grupos[]"]:checked');

    if (selectedGroups.length > 0) {
        var selectedCodes = Array.from(selectedGroups).map(group => group.value);
        var printURL = '<?php echo SERVERURL . "pdf/xmlgenerator.php?codes="; ?>' + selectedCodes.join(',');

        print_invoice(printURL);
    } else {
        alert('No hay grupos seleccionados para imprimir.');
    }
}

function print_invoice(url) {
    window.location.href = url;
}
</script>
