<?php
    $peticion_ajax=true;
    require_once "../config/APP.php";
	include "../vistas/inc/session_start.php";

	if(isset($_POST['modulo_producto'])){

		/*--------- Instancia al controlador ---------*/
		require_once "../controladores/logoControlador.php";
		$ins_producto = new productoControlador();
		
		/*--------- Actualizar imagen de producto ---------*/
        if($_POST['modulo_producto']=="img_actualizar"){
			echo $ins_producto->actualizar_imagen_producto_controlador();
		}

		/*--------- Eliminar imagen de producto ---------*/
        if($_POST['modulo_producto']=="img_eliminar"){
			echo $ins_producto->eliminar_imagen_producto_controlador();
		}

	}else{
		session_destroy();
		header("Location: ".SERVERURL."login/");
	}