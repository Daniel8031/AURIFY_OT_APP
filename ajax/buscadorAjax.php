<?php
    $peticion_ajax=true;
    require_once "../config/APP.php";
	include "../vistas/inc/session_start.php";

	if(isset($_POST['modulo'])){

		/*--------- Instancia al controlador ---------*/
		require_once "../controladores/grupoControlador.php";
        $ins_movimiento = new grupoControlador();
        
        /*--------- Agregar movimiento ---------*/
        if($_POST['modulo']=="registrar"){
            echo $ins_movimiento->agregar_modulo_game_controlador();
		}

	}else{
		session_destroy();
		header("Location: ".SERVERURL."login/");
	}