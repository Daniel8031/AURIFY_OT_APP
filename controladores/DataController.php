<?php
if ($peticion_ajax) {
    // Requiere el archivo de conexión o usa tu lógica de conexión a la base de datos
    require_once "../modelos/mainModel.php";
} else {
    // Ajusta la ruta del archivo de conexión o usa tu lógica de conexión a la base de datos
    require_once "./modelos/mainModel.php";
}

class DataController extends mainModel
{
    private $peticion_ajax;

    public function __construct($peticion_ajax)
    {
        $this->peticion_ajax = $peticion_ajax;
    }

    public function cargar_skins_controlador($id_juego)
    {
        if ($this->peticion_ajax) {
            // Define el array de parámetros
            $parametros = [":id_juego" => $id_juego];
    
            // Realiza la consulta SQL para obtener los skins del juego con el ID $id_juego
            $consulta = "SELECT id_titulo, name_titulo FROM titulo_game WHERE id_juego = :id_juego";
            $datos = mainModel::ejecutar_consulta_simple($consulta, $parametros);
    
            if ($datos->rowCount() > 0) {
                $skins = $datos->fetchAll(PDO::FETCH_ASSOC);
    
                // Formatea los datos como opciones para el select
                $options = "<option value=''>Seleccione una opción</option>";
                foreach ($skins as $skin) {
                    $options .= "<option value='" . $skin['id_titulo'] . "'>" . $skin['name_titulo'] . "</option>";
                }
    
                // Devuelve las opciones al script JavaScript en formato JSON
                $respuesta = [
                    "success" => true,
                    "options" => $options
                ];
            } else {
                $respuesta = [
                    "success" => false,
                    "message" => "No se encontraron skins para este juego."
                ];
            }
    
            echo json_encode($respuesta);
        } else {
            // Manejar la solicitud no AJAX según sea necesario
        }
    }
}    