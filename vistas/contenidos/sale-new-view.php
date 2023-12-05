<div class="full-box page-header">
    <h3 class="text-left text-uppercase">
        <i class="fas fa-gamepad fa-fw"></i> &nbsp; Agregar Juegos
    </h3>
    <?php include "./vistas/desc/desc_venta.php"; ?>
</div>


<div class="container-fluid">
    <?php
    $check_empresa = $lc->datos_tabla("Normal", "empresa LIMIT 1", "*", 0);

    if ($check_empresa->rowCount() == 1) {
        $datos_empresa = $check_empresa->fetch();

        $datos_caja = $lc->datos_tabla("Normal", "rol WHERE rol_id='" . $_SESSION['caja_svi'] . "'", "*", 0);
        $datos_caja = $datos_caja->fetch();
    }
    ?>
    <div class="form-neon">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-lg-9">
                    <div class="alert alert-light text-center" role="alert" style="font-size: 12px;">
                        <?php
                        if ($_SESSION['lector_codigo_svi'] == "Barras") {
                            $txt_codigo = "de barras";
                        } else {
                            $txt_codigo = "SKU";
                        }


                        if ($_SESSION['lector_estado_svi'] == "Deshabilitado") {
                            ?>
                            <p>Está utilizando la <strong class="text-uppercase">configuración manual</strong> con lectura
                                de <strong class="text-uppercase">códigos
                                    <?php echo $txt_codigo; ?>
                                </strong>, para agregar Juegos debe de digitar el código
                                <?php echo $txt_codigo; ?> en el campo "Código de producto" y luego presionar &nbsp; <strong
                                    class="text-uppercase"><i class="far fa-check-circle"></i> &nbsp; Agregar
                                    producto</strong>. También puede agregar el producto mediante la opción &nbsp; <strong
                                    class="text-uppercase"><i class="fas fa-search"></i> &nbsp; Agregar Juegos</strong>
                            </p>
                        <?php } else { ?>
                            <p>Está utilizando la <strong class="text-uppercase">configuración automática</strong> con
                                lectura de <strong class="text-uppercase">códigos
                                    <?php echo $txt_codigo; ?>
                                </strong>, debe de conectar un lector de código de barras a su computadora, luego
                                seleccionar el campo "Código de juego" <strong class="text-uppercase">enter</strong></p>
                        <?php } ?>
                        <hr>
                        <p class="mb-0">Puede cambiar esta configuración en los &nbsp; <a
                                href="<?php echo SERVERURL . "user-update/" . $lc->encryption($_SESSION['id_svi']) . "/"; ?>"><i
                                    class="fas fa-user-cog"></i>&nbsp; ajustes de su cuenta</a>.</p>
                    </div>

                    <div class="container-fluid">
                        <form class="row align-items-center" id="sale-barcode-form" autocomplete="off">
                            <input type="hidden" id="sale-barcode-input">
                            <div class="col-12 col-md-3">
                                <button type="button" class="btn btn-primary" id="btn_modal_buscar_codigo"><i
                                        class="fas fa-search"></i> &nbsp; Agregar juego</button>
                                <br>

                            </div>


                            <div class="col-12 col-md-3">

                            </div>

                            <div class="col-12 col-md-9">
                                <div class="form-group">


                                </div>
                            </div>

                        </form>
                    </div>

                    <div class="table-responsive">

                        <div class="container-fluid">
                            <?php
                            require_once "./controladores/ventaControlador.php";
                            $ins_venta = new ventaControlador();

                            echo $ins_venta->paginador_venta_controlador($pagina[1], 4, $pagina[0], "", "");
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3">
                    <h3 class="text-center text-uppercase">Datos del grupo</h3>
                    <hr>
                    <?php


                    $datos = $lc->datos_tabla("Normal", "game_group WHERE group_codigo='" . $pagina[1] . "'", "*", 0);

                    if ($datos->rowCount() == 1) {
                        $datos = $datos->fetch();

                        // Guardar los resultados en variables para usar fuera de la etiqueta PHP
                        $codigoGroup = $datos['group_codigo'];
                        $nombreGroup = $datos['group_name'];
                        $temajuego = $datos['group_theme'];
                        // ... otras variables que necesites
                    
                        // Puedes cerrar la etiqueta PHP y escribir HTML directamente
                        ?>


                        <div class="form-group">
                            <label for="">Código de Grupo</label>
                            <input type="text" class="form-control" value="<?php echo $codigoGroup; ?>" readonly>
                            <!-- ... otros elementos HTML con las variables que necesites -->
                        </div>

                        <div class="form-group">
                            <label for="">Nombre del Grupo</label>
                            <input type="text" class="form-control" value="<?php echo $nombreGroup; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="">Nombre del Tema de Juego</label>
                            <input type="text" class="form-control" value="<?php echo $temajuego; ?>" readonly>
                        </div>
                        <?php
                    } else {
                        // Manejar el caso en que no se obtengan datos
                        echo "No se encontraron resultados para el código de grupo: " . $pagina[1];
                    }
                    ?>
                    <div class="form-group">
                        <label for="venta_fecha">Fecha</label>
                        <input type="date" class="form-control" name="venta_fecha_reg" id="venta_fecha"
                            value="<?php echo date("Y-m-d"); ?>" readonly>
                    </div>


                    <!-- <div class="form-group">
                        <label for="venta_cliente">Cliente</label>
                        <?php
                        if (isset($_SESSION['datos_cliente_venta']) && count($_SESSION['datos_cliente_venta']) >= 1 && $_SESSION['datos_cliente_venta']['cliente_id'] != 1) {
                            ?>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-10 text-center">
                                    <input type="text" class="form-control" id="venta_cliente" value="<?php echo $_SESSION['datos_cliente_venta']['cliente_nombre'] . " " . $_SESSION['datos_cliente_venta']['cliente_apellido']; ?>" readonly>
                                </div>
                                <div class="col-2 text-center">
                                    <form class="FormularioAjax" action="<?php echo SERVERURL; ?>ajax/ventaAjax.php" method="POST" data-form="sale_cliente" autocomplete="off">
                                        <input type="hidden" name="cliente_id_del" value="<?php echo $_SESSION['datos_cliente_venta']['cliente_id']; ?>">
                                        <input type="hidden" name="modulo_venta" value="eliminar_cliente">
                                        <button type="submit" class="btn btn-danger" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Remover cliente">
                                            <i class="fas fa-user-times"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                   
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-10 text-center">
                                    <input type="text" class="form-control" id="venta_cliente" value="<?php echo $_SESSION['datos_cliente_venta']['cliente_nombre'] . " " . $_SESSION['datos_cliente_venta']['cliente_apellido']; ?>" readonly>
                                </div>
                                <div class="col-2 text-center">
                                    <button type="button" class="btn btn-info" data-toggle="popover" data-trigger="hover" id="btn_modal_cliente" data-placement="top" data-content="Agregar cliente">
                                        <i class="fas fa-user-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div> -->


                    <form class="FormularioAjax" action="<?php echo SERVERURL; ?>ajax/ventaAjax.php" method="POST"
                        data-form="save" autocomplete="off" name="formsale">
                        <input type="hidden" name="modulo_venta" value="registrar_venta">
                        <input type="hidden" name="codigo" value="<?php echo $pagina[1]; ?>">

                        <p class="text-center" style="margin-top: 40px;">
                            <button type="submit" class="btn btn-raised btn-info btn-sm"><i class="far fa-save"></i>
                                &nbsp; GUARDAR</button>
                        </p>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <?php
    // }else{
    //     echo '
    //         <div class="alert alert-danger text-center" role="alert">
    //             <p><i class="fas fa-exclamation-triangle fa-5x"></i></p>
    //             <h4 class="alert-heading">¡Ocurrió un error inesperado!</h4>
    //             <p class="mb-0">No hemos podio seleccionar algunos datos sobre los impuestos, por favor <a href="'.SERVERURL.'company/" >verifique aquí los datos de la empresa</a></p>
    //         </div>
    //     ';
    // }
    ?>
</div>

<!-- MODAL CLIENTE -->
<div class="modal fade" id="modal_cliente" tabindex="-1" role="dialog" aria-labelledby="modal_cliente"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_cliente">Agregar cliente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="form-group">
                        <label for="input_cliente" class="bmd-label-floating">Documento, Nombre, Apellido,
                            Teléfono</label>
                        <input type="text" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}" class="form-control"
                            name="input_cliente" id="input_cliente" maxlength="30">
                    </div>
                </div>
                <br>
                <div class="container-fluid" id="tabla_clientes"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="buscar_cliente()"><i
                        class="fas fa-search fa-fw"></i> &nbsp; Buscar</button>
                &nbsp; &nbsp;
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL BUSCAR CODIGO -->
<div class="modal fade" id="modal_buscar_codigo" tabindex="-1" role="dialog" aria-labelledby="modal_buscar_codigo"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_buscar_codigo">Añadir un Juego</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" role="alert">
                <form id="formulario_juego" class="form-neon FormularioAjax" method="POST" data-form="save"
                    autocomplete="off">
                    <input type="hidden" id="codigo" name="codigo" value="<?php echo $pagina[1]; ?>">

                    <input type="hidden" name="modulo_movimiento" value="registrar">

                    <div class="container-fluid">

                        <div class="form-group">
                            <label for="prioridad1" class="bmd-label-floating">Modelo de Máquina</label>
                            <select class="form-control label-floating" id="prioridad1" name="prioridad1" required>
                                <option value="">Seleccione modelo de máquina</option>
                                <?php
                                $datos_maquina = $lc->datos_tabla("Normal", "familia_game", "*", 0);

                                while ($campos_maquina = $datos_maquina->fetch()) {
                                    $id_juego = $campos_maquina['id_juego'];
                                    $familia_name = $campos_maquina['familia_name'];

                                    // Agrega el atributo data-id-juego a cada opción
                                    echo '<option value="' . $id_juego . '" data-id-juego="'.$id_juego.'" data-id-familia-name="'.$familia_name.'">(' . $id_juego . "_" . $familia_name . ')</option>';

                                }
                                ?>
                            </select>
                        </div>

                    </div>
                    <div class="container-fluid">
                        <div class="form-group">
                            <label for="prioridad2" class="bmd-label-floating">Skin del Juego</label>
                            <select class="form-control" id="prioridad2" name="prioridad2" required>
                                <option value="">Seleccione skin de Juego</option>
                            </select>
                        </div>
                    </div>
                    <div class="container-fluid">
                        <div class="form-group">
                            <label for="prioridad2" class="bmd-label-floating">Porcentaje de Payout</label>
                            <select class="form-control" id="prioridad3" name="prioridad3" required>
                                <option value="">Seleccione porcentaje Payout</option>
                            </select>
                        </div>
                    </div>
                    <div class="container-fluid">
                        <div class form-group>
                            <label for="prioridad2" class="bmd-label-floating">Denominaciones</label>
                            <select class="form-control" id="prioridad4" name="prioridad4" required>
                                <option value="">Seleccione la denominación(s)</option>
                            </select>
                        </div>
                    </div>
                </form>
                <br>
                <div class="container-fluid" id="tabla_productos"></div>
            </div>
            <div class="modal-footer">
                &nbsp; &nbsp;
                <button type="button" class="btn btn-primary" onclick="enviarFormulario()">
                    <i class="fas fa-plus-circle fa-fw"></i> &nbsp; Agregar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    $('#prioridad1').on('change', function() {
        var selectedId = $(this).val(); // Obtiene el valor seleccionado en prioridad1

        // Realiza una consulta a la base de datos para obtener las opciones de skin basadas en id_juego
        $.ajax({
            url: '<?php echo SERVERURL; ?>api/cargar_game_titulo.php', // Ruta al archivo PHP que recuperará las opciones
            method: 'POST',
            data: { id_juego: selectedId },
            success: function(data) {
                // Llena el selector prioridad2 con las opciones obtenidas de la respuesta del servidor
                $('#prioridad2').html(data);
            },
            error: function(error) {
                console.log('Error al cargar opciones: ' + error);
            }
        });
        $.ajax({
            url: '<?php echo SERVERURL; ?>api/cargar_game_payout.php', // Ruta al archivo PHP que recuperará las opciones
            method: 'POST',
            data: { id_juego: selectedId },
            success: function(data) {
                // Llena el selector prioridad2 con las opciones obtenidas de la respuesta del servidor
                $('#prioridad3').html(data);
            },
            error: function(error) {
                console.log('Error al cargar opciones: ' + error);
            }
        });
        $.ajax({
            url: '<?php echo SERVERURL; ?>api/cargar_game_denominacion.php', // Ruta al archivo PHP que recuperará las opciones
            method: 'POST',
            data: { id_juego: selectedId },
            success: function(data) {
                // Llena el selector prioridad2 con las opciones obtenidas de la respuesta del servidor
                $('#prioridad4').html(data);
            },
            error: function(error) {
                console.log('Error al cargar opciones: ' + error);
            }
        });
    });
});
</script>


<script>

    let sale_form_barcode = document.querySelector("#sale-barcode-form");
    sale_form_barcode.addEventListener('submit', function (event) {
        event.preventDefault();
        setTimeout('agregar_producto()', 100);
    });

    /* Configuracion automatica con lector de codigo de barras */
    <?php if ($_SESSION['lector_estado_svi'] == "Habilitado") { ?>
        let sale_input_barcode = document.querySelector("#sale-barcode-input");

        sale_input_barcode.addEventListener('paste', function () {
            setTimeout('agregar_producto()', 100);
        });
    <?php } ?>


    /* Actualizar cantidad de producto */
    function actualizar_cantidad(id, codigo) {
        let cantidad = document.querySelector(id).value;

        cantidad = cantidad.trim();
        codigo.trim();

        if (cantidad > 0) {
            Swal.fire({
                title: "¿Estás seguro?",
                text: "Desea actualizar la cantidad de productos",
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, actualizar',
                cancelButtonText: 'No, cancelar'
            }).then((result) => {
                if (result.value) {

                    let datos = new FormData();
                    datos.append("producto_codigo_up", codigo);
                    datos.append("producto_cantidad_up", cantidad);
                    datos.append("modulo_venta", "actualizar_producto");

                    fetch('<?php echo SERVERURL; ?>ajax/ventaAjax.php', {
                        method: 'POST',
                        body: datos
                    })
                        .then(respuesta => respuesta.json())
                        .then(respuesta => {
                            return alertas_ajax(respuesta);
                        });
                }
            });
        } else {
            Swal.fire({
                title: 'Ocurrió un error inesperado',
                text: 'Debes de introducir una cantidad mayor a 0',
                type: 'error',
                confirmButtonText: 'Aceptar'
            });
        }
    }



    /* Mostrar modal cliente y buscar codigo */
    $(document).ready(function () {
        $('#btn_modal_cliente').on('click', function () {
            $('#modal_cliente').modal('show');
        });

        $('#btn_modal_buscar_codigo').on('click', function () {
            $('#modal_buscar_codigo').modal('show');
        });
    });


    /*----------  Agregar descuento  ----------*/
    function aplicar_descuento() {
        let descuento = document.querySelector('#venta_descuento').value;
        descuento = descuento.trim();

        if (descuento > 0) {
            Swal.fire({
                title: "¿Estás seguro?",
                text: "Desea aplicar el descuento seleccionado",
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, aplicar',
                cancelButtonText: 'No, cancelar'
            }).then((result) => {
                if (result.value) {

                    let datos = new FormData();
                    datos.append("venta_descuento_add", descuento);
                    datos.append("modulo_venta", "aplicar_descuento");

                    fetch('<?php echo SERVERURL; ?>ajax/ventaAjax.php', {
                        method: 'POST',
                        body: datos
                    })
                        .then(respuesta => respuesta.json())
                        .then(respuesta => {
                            return alertas_ajax(respuesta);
                        });
                }
            });
        } else {
            Swal.fire({
                title: 'Ocurrió un error inesperado',
                text: 'Debes de introducir un descuento mayor a 0%',
                type: 'error',
                confirmButtonText: 'Aceptar'
            });
        }
    }


    /*----------  Remover descuento  ----------*/
    function remover_descuento(descuento) {

        if (descuento > 0) {
            Swal.fire({
                title: "¿Estás seguro?",
                text: "Desea remover el descuento aplicado",
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, remover',
                cancelButtonText: 'No, cancelar'
            }).then((result) => {
                if (result.value) {

                    let datos = new FormData();
                    datos.append("venta_descuento_del", descuento);
                    datos.append("modulo_venta", "remover_descuento");

                    fetch('<?php echo SERVERURL; ?>ajax/ventaAjax.php', {
                        method: 'POST',
                        body: datos
                    })
                        .then(respuesta => respuesta.json())
                        .then(respuesta => {
                            return alertas_ajax(respuesta);
                        });
                }
            });
        } else {
            Swal.fire({
                title: 'Ocurrió un error inesperado',
                text: 'Ha ocurrido un error no podemos procesar su petición',
                type: 'error',
                confirmButtonText: 'Aceptar'
            });
        }
    }





    /*----------  Resetear total abonado  ----------*/
    function resetear_total(opcion) {

        let tipo_pago = document.formsale.venta_tipo_venta_reg.value;

        if (tipo_pago != opcion) {
            document.querySelector('#venta_abono').value = "0.00";
            document.querySelector('#venta_cambio').value = "0.00";
        }

    }


    /*----------  enviar formulario  ----------*/
    function enviarFormulario() {
        // Captura los valores del formulario
        let prioridad1 = $("#prioridad1").val();
        let prioridad2 = $("#prioridad2").val();
        let prioridad3 = $("#prioridad3").val();
        let prioridad4 = $("#prioridad4").val();
        let codigo = $("#codigo").val();

        // Recupera el valor de id_juego y familia_name del elemento seleccionado
        let id_juego = $("#prioridad1 option:selected").data("id-juego");
let familia_name = $("#prioridad1 option:selected").data("id-familia-name");

        // Verifica si el campo input_codigo no está vacío
        if (prioridad1 !== "") {
            // Agrega el valor de id_juego y familia_name a tu objeto de datos
            let datos = {
                prioridad1: id_juego,
                prioridad2: prioridad2,
                prioridad3: prioridad3,
                prioridad4: prioridad4,
                codigo: codigo,
                familia_name: familia_name,
                modulo_venta: "agregar_producto_personalizado"
            };

            // Agrega mensajes de depuración
            // console.log("URL de la solicitud AJAX:", '<?php echo SERVERURL; ?>ajax/ventaAjax.php');
            var audio = new Audio('<?php echo SERVERURL; ?>vistas/song/soft-notice.mp3');
            audio.play();

            // Realiza la solicitud AJAX para enviar los datos al servidor
            $.ajax({
                type: "POST",
                url: '<?php echo SERVERURL; ?>ajax/ventaAjax.php',
                data: datos,
                success: function (response) {
                    // Maneja la respuesta del servidor aquí
                    console.log("Respuesta del servidor:", response);
                    try {
                        let parsedResponse = JSON.parse(response);
                        // Procesa la respuesta aquí
                        alertas_ajax(parsedResponse);

                        // Cierra el modal si es necesario
                        $("#modal_formulario").modal("hide");
                    } catch (error) {
                        console.error(error);
                        // Muestra la alerta de error al usuario
                        Swal.fire({
                            title: 'Ocurrió un error inesperado',
                            text: 'Datos no se han enviado a la base de datos',
                            type: 'error',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                },
                error: function (error) {
                    // Maneja los errores aquí
                    console.error(error);
                    // Muestra la alerta de error al usuario
                    Swal.fire({
                        title: 'Ocurrió un error inesperado',
                        text: 'No se pudo completar la solicitud',
                        type: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        } else {
            Swal.fire({
                title: 'Ocurrió un error inesperado',
                text: 'Debes de introducir el Nombre, Marca o Modelo del producto',
                type: 'error',
                confirmButtonText: 'Aceptar'
            });
        }
    }




    /*----------  Agregar codigo  ----------*/
    function agregar_codigo($codigo) {
        $('#modal_buscar_codigo').modal('hide');
        document.querySelector('#sale-barcode-input').value = $codigo;
        setTimeout('agregar_producto()', 100);
    }
</script>




<?php
include "./vistas/inc/print_invoice_script.php";
?>