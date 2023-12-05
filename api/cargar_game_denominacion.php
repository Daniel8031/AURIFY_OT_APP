<?php
$peticion_ajax = true;
if (isset($_POST['id_juego'])) {
    $id_juego = $_POST['id_juego'];

    require_once "../config/APP.php";
    require_once "../controladores/ventaControlador.php";
    $ins_venta = new ventaControlador();

    // Obtén los detalles del grupo con la id_juego seleccionada
    $detalle_grupo = $ins_venta->datos_tabla("Normal", "denominacion WHERE id_juego ='". $id_juego."'","*",0);

    // Inicializa la variable para almacenar las opciones de prioridad2
    $htmlOptions = '';

    // Genera las opciones para prioridad2
    while ($detalle = $detalle_grupo->fetch()) {
        $htmlOptions .= '<option value="' . $detalle['denominador'] . '">(' . $detalle['denominador'] . ')</option>';
    }

    // Verifica si se generaron opciones y devuelve el resultado
    if ($htmlOptions !== '') {
        echo $htmlOptions;
    } else {
        // Manejar la falta de id_juego en la solicitud (opcional)
        echo '<option value="">No se encontraron datos relacionados</option>';
    }
}
