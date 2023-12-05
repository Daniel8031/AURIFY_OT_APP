<?php
$peticion_ajax = true;
if (isset($_POST['id_juego'])) {
    $id_juego = $_POST['id_juego'];

    require_once "../config/APP.php";
    require_once "../controladores/ventaControlador.php";
    $ins_venta = new ventaControlador();

    // Obtén los detalles del grupo con la id_juego seleccionada
    $detalle_grupo = $ins_venta->datos_tabla("Normal", "titulo_game WHERE id_juego ='". $id_juego."'","*",0);

    // Inicializa la variable para almacenar las opciones de prioridad2
    $htmlOptions = '';

    // Genera las opciones para prioridad2
    while ($detalle = $detalle_grupo->fetch()) {
        $htmlOptions .= '<option value="' . $detalle['name_titulo'] . '">(' . $detalle['name_titulo'] . ')</option>';
    }

    // Verifica si se generaron opciones y devuelve el resultado
    if ($htmlOptions !== '') {
        echo $htmlOptions;
    } else {
        // Manejar la falta de id_juego en la solicitud (opcional)
        echo '<option value="">No se encontraron datos relacionados</option>';
    }
}




//Establece la conexión a la base de datos (ajusta los valores de conexión según tu configuración)
// $host = "localhost";
// $usuario = "root";
// $contrasena = "";
// $base_de_datos = "game";

// try {
//     $conexion = new PDO("mysql:host=$host;dbname=$base_de_datos", $usuario, $contrasena);
//     $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//     if (isset($_POST['id_juego'])) {
//         $id_juego = $_POST['id_juego'];

//         // Realiza una consulta en la base de datos para obtener opciones relacionadas con la id_juego
//         $sql = "SELECT name_titulo FROM titulo_game WHERE id_juego = :id_juego";
//         $stmt = $conexion->prepare($sql);
//         $stmt->bindParam(":id_juego", $id_juego);
//         $stmt->execute();

//         $opciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

//         // Genera las opciones en formato HTML
//         $htmlOptions = '';
//         foreach ($opciones as $opcion) {
//             $htmlOptions .= '<option value="' . $opcion['name_titulo'] . '">' . $opcion['name_titulo'] . '</option>';
//         }

//         echo $htmlOptions;
//     } else {
//         // Manejar la falta de id_juego en la solicitud (opcional)
//         echo '<option value="">Seleccione una opción</option>';
//     }
// } catch (PDOException $e) {
//     echo "Error de conexión a la base de datos: " . $e->getMessage();
// }

// Incluye el archivo que contiene la lógica de tu modelo (asumo que ahí se encuentra la función $lc->datos_tabla)


?>


