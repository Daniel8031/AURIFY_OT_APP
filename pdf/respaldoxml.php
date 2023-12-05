<?php
$peticion_ajax = true;
$code = (isset($_GET['code'])) ? $_GET['code'] : 0;

/*---------- Incluyendo configuraciones ----------*/
require_once "../config/APP.php";

/*---------- Instancia al controlador venta ----------*/
require_once "../controladores/ventaControlador.php";
$ins_venta = new ventaControlador();

$datos_venta = $ins_venta->datos_tabla("Normal", "catalogo INNER JOIN usuario ON catalogo.usuario_id=usuario.usuario_id INNER JOIN rol ON catalogo.rol_id=catalogo.catalogo_id WHERE codigo_grupo='$code'", "*", 0);

if ($datos_venta->rowCount() == 0) {
    /*---------- Datos del Juego ----------*/
    $datos_venta = $datos_venta->fetch();

    try {
        // Crear un objeto del DOM 
        $fichero = new DOMDocument();
        $fichero->formatOutput = true; // Le damos formato al fichero 
        $fichero->xmlStandalone;

        // CREACION DEL FICHERO
        $etiquetaDepartamentos = $fichero->createElement('GameGroupDefinitions');
        $etiquetaDepartamentos = $fichero->appendChild($etiquetaDepartamentos);

        $etiquetaGroups = $fichero->createElement('GameGroups');
        $etiquetaGroups = $etiquetaDepartamentos->appendChild($etiquetaGroups);

        $etiquetaDepartamento = $fichero->createElement('GameGroup');
        $etiquetaDepartamento = $etiquetaDepartamentos = $etiquetaGroups->appendChild($etiquetaDepartamento);

        $datos_empresa = $ins_venta->datos_tabla("Normal", "game_group WHERE group_codigo='" . $code . "'", "*", 0);
        $datos_empresa = $datos_empresa->fetch();
        if($datos_empresa) {

        $names = $fichero->createElement('name', $datos_empresa['group_name']);
        $names = $etiquetaDepartamento->appendChild($names);
        $ThemeName = $fichero->createElement('ThemeName', $datos_empresa['group_theme']);
        $ThemeName = $etiquetaDepartamento->appendChild($ThemeName);
        }

        $denom = $fichero->createElement('DenomGroupDefinitions');
        $denom = $etiquetaDepartamento->appendChild($denom);

        // Ejecutar la consulta SQL
        $venta_detalle = $ins_venta->datos_tabla("Normal", "catalogo WHERE codigo_grupo='" . $code . "'", "*", 0);
        $venta_detalle = $venta_detalle->fetchAll();

        foreach ($venta_detalle as $registro) {
            $denome = $fichero->createElement('DenomGroup');
            $denome = $denom->appendChild($denome);

            $name = $fichero->createElement('name', $registro['titulo_game']);
            $name = $denome->appendChild($name);

            $GameDefID = $fichero->createElement('GameDefID', $registro['familia_game']);
            $GameDefID = $denome->appendChild($GameDefID);

            $PercentPayout = $fichero->createElement('PercentPayout', $registro['payout_game']);
            $PercentPayout = $denome->appendChild($PercentPayout);

            $GameSkinDLL = $fichero->createElement('GameSkinDLL', $registro['denominacion_game']);
            $GameSkinDLL = $denome->appendChild($GameSkinDLL);

            $Denominations = $fichero->createElement('Denominations', $registro['codigo_grupo']);
            $Denominations = $denome->appendChild($Denominations);
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
