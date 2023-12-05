<?php
$peticion_ajax = true;
$code = (isset($_GET['code'])) ? $_GET['code'] : 0;

/*---------- Incluyendo configuraciones ----------*/
require_once "../config/APP.php";

/*---------- Instancia al controlador venta ----------*/
require_once "../controladores/ventaControlador.php";
$ins_venta = new ventaControlador();

$datos_grupos = $ins_venta->datos_tabla(
    "Normal",
    "game_group",
    "*",
    0
);

if ($datos_grupos->rowCount() > 0) {
    /*---------- Datos del Juego ----------*/
   
    try {
        $fichero = new DOMDocument();
        $fichero->formatOutput = true;
        $fichero->xmlStandalone;

        // Crear el elemento principal para los grupos de juegos
        $etiquetaDepartamentos = $fichero->createElement('GameGroupDefinitions');
        $etiquetaDepartamentos = $fichero->appendChild($etiquetaDepartamentos);

        // Iterar sobre los grupos obtenidos de la base de datos
        while ($grupo = $datos_grupos->fetch()) {
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

            // Iterar sobre los detalles y agregar al XML
            while ($detalle = $detalle_grupo->fetch()) {
                $denome = $fichero->createElement('DenomGroup');
                $denome = $denom->appendChild($denome);
    
                $name = $fichero->createElement('name', $detalle['titulo_game']);
                $name = $denome->appendChild($name);
    
                $GameDefID = $fichero->createElement('GameDefID', $detalle['familia_game']);
                $GameDefID = $denome->appendChild($GameDefID);
    
                $PercentPayout = $fichero->createElement('PercentPayout', $detalle['payout_game']);
                $PercentPayout = $denome->appendChild($PercentPayout);
    
                $GameSkinDLL = $fichero->createElement('GameSkinDLL', $detalle['denominacion_game']);
                $GameSkinDLL = $denome->appendChild($GameSkinDLL);
    
                $Denominations = $fichero->createElement('Denominations', $detalle['codigo_grupo']);
                $Denominations = $denome->appendChild($Denominations);
                
            }
        }
        // Definir $fechaHoy antes de usarlo
        $fechaHoy = date('Ymd');

        // Para guardar el fichero en el sistema de archivos local
        $savePath = __DIR__ . "/tmp/" . $fechaHoy . $code . "gameGroup.xml";

        // Obtener el contenido XML como una cadena
        $xmlContent = $fichero->saveXML();

        // Eliminar manualmente la declaración XML de la cadena
        $xmlContent = str_replace('<?xml version="1.0"?>', '', $xmlContent);

        // Guardar la cadena XML modificada en el archivo
        file_put_contents($savePath, $xmlContent);

        // Utilizar SERVERURL si está definido
        $downloadURL = defined('SERVERURL') ? SERVERURL : '';
        $downloadURL .= "pdf/tmp/" . $fechaHoy . $code . "gameGroup.xml";
        header('Content-Type: text/xml');
        header("Content-Disposition: attachment; filename=" . $fechaHoy . $code . "gameGroup.xml");
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
