<?php
    $peticion_ajax=true;
    require_once "../config/APP.php";
	include "../vistas/inc/session_start.php";

	if(isset($_POST['modulo_rol'])){

		/*--------- Instancia al controlador ---------*/
		require_once "../controladores/rolControlador.php";
        $ins_caja = new cajaControlador();
        
        /*--------- Agregar caja ---------*/
        if($_POST['modulo_rol']=="registrar"){
            echo $ins_caja->agregar_caja_controlador();
		}
		
		/*--------- Actualizar caja ---------*/
		if($_POST['modulo_rol']=="actualizar"){
			echo $ins_caja->actualizar_caja_controlador();
		}

		/*--------- Eliminar caja ---------*/
		if($_POST['modulo_rol']=="eliminar"){
			echo $ins_caja->eliminar_caja_controlador();
		}

	}else{
		session_destroy();
		header("Location: ".SERVERURL."login/");
	}