<?php
$peticion_ajax = true;
$codes = (isset($_GET['codes'])) ? explode(',', $_GET['codes']) : array();

/*---------- Incluyendo configuraciones ----------*/
require_once "../config/APP.php";

/*---------- Instancia al controlador venta ----------*/
require_once "../controladores/ventaControlador.php";
$ins_venta = new ventaControlador();

$datos_grupos = $ins_venta->datos_tabla("Normal", "game_group", "*", 0);

if ($datos_grupos->rowCount() > 0) {
    try {
        $fichero = new DOMDocument();
        $fichero->formatOutput = true;
        $fichero->xmlStandalone;

        // Crear el elemento principal para los grupos de juegos
        $etiquetaDepartamentos = $fichero->createElement('GameGroupDefinitions');
        $etiquetaDepartamentos = $fichero->appendChild($etiquetaDepartamentos);

        while ($grupo = $datos_grupos->fetch()) {
            // Verificar si el ID del grupo está en la lista seleccionada
            if (in_array($grupo['group_codigo'], $codes)) {
                $etiquetaGroups = $fichero->createElement('GameGroups');
                $etiquetaGroups = $etiquetaDepartamentos->appendChild($etiquetaGroups);

                $etiquetaDepartamento = $fichero->createElement('GameGroup');
                $etiquetaDepartamento = $etiquetaGroups->appendChild($etiquetaDepartamento);

                // Agregar datos del grupo principal (game_group)
                $names = $fichero->createElement('name', $grupo['group_name']);
                $names = $etiquetaDepartamento->appendChild($names);
                $ThemeName = $fichero->createElement('ThemeName', $grupo['group_theme']);
                $ThemeName = $etiquetaDepartamento->appendChild($ThemeName);

                // Obtener detalles relacionados con el grupo desde la tabla catalogo
                $detalle_grupo = $ins_venta->datos_tabla(
                    "Normal",
                    "catalogo WHERE codigo_grupo = '" . $grupo['group_codigo'] . "'",
                    "*",
                    0
                );
                $denom = $fichero->createElement('DenomGroupDefinitions');
                $denom = $etiquetaDepartamento->appendChild($denom);

                while ($detalle = $detalle_grupo->fetch()) {
                    $denome = $fichero->createElement('DenomGroup');
                    $denome = $denom->appendChild($denome);
    
                    $name = $fichero->createElement('name', $detalle['payout_game'].'_'.$detalle['id_juego']."_".$detalle['titulo_game']);
                    $name = $denome->appendChild($name);
    
                    $GameDefID = $fichero->createElement('GameDefID', $detalle['id_juego']);
                    $GameDefID = $denome->appendChild($GameDefID);
    
                    $PercentPayout = $fichero->createElement('PercentPayout', $detalle['payout_game']);
                    $PercentPayout = $denome->appendChild($PercentPayout);
    
                    $GameSkinDLL = $fichero->createElement('GameSkinDLL', $detalle['familia_game']);
                    $GameSkinDLL = $denome->appendChild($GameSkinDLL);
    
                    $Denominations = $fichero->createElement('Denominations', $detalle['denominacion_game']);
                    $Denominations = $denome->appendChild($Denominations);
                }
            }
        }

        $fechaHoy = date('Ymd');
        $savePath = __DIR__ . "/tmp/" . $fechaHoy . implode('_', $codes) . "gameGroup.xml";

        $xmlContent = $fichero->saveXML();
        $xmlContent = str_replace('<?xml version="1.0"?>', '', $xmlContent);

        file_put_contents($savePath, $xmlContent);

        $downloadURL = defined('SERVERURL') ? SERVERURL : '';
        $downloadURL .= "pdf/tmp/" . $fechaHoy . implode('_', $codes) . "gameGroup.xml";
        header('Content-Type: text/xml');
        header("Content-Disposition: attachment; filename=" . $fechaHoy . implode('_', $codes) . "gameGroup.xml");
        readfile($downloadURL);
    } catch (PDOException $error) {
        echo "<p>Error " . $error->getMessage() . "</p>";
    }
} else {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <title><?php echo COMPANY; ?></title>
        <?php include '../vistas/inc/Head.php'; ?>
    </head>
    <body>
        <div class="full-box container-404">
            <div>
                <p class="text-center"><i class="fas fa-rocket fa-10x"></i></p>
                <h1 class="text-center">¡Ocurrió un error!</h1>
                <p class="lead text-center">No hemos encontrado datos de <?php echo $code ?></p>
            </div>
        </div>
        <?php include '../vistas/inc/Script.php'; ?>
    </body>
    </html>
<?php } ?>
