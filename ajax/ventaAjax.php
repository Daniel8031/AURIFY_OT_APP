<?php
    $peticion_ajax=true;
    require_once "../config/APP.php";
	include "../vistas/inc/session_start.php";

	if(isset($_POST['modulo_venta'])){

		/*--------- Instancia al controlador ---------*/
		require_once "../controladores/ventaControlador.php";
        $ins_venta = new ventaControlador();
        
		/*--------- Agregar producto a carrito ---------*/
		if ($_POST['modulo_venta'] == "agregar_producto_personalizado") {
   
			echo $ins_venta->agregar_producto_personalizado();
		}
        
        /*--------- Eliminar producto de carrito ---------*/
		if($_POST['modulo_venta']=="eliminar_producto"){
			echo $ins_venta->eliminar_producto_carrito_controlador();
		}
		if($_POST['modulo_venta']=="registrar_venta"){
			echo $ins_venta->registrar_venta_controlador();
		}

		/*--------- Eliminar cliente de carrito ---------*/
		if($_POST['modulo_venta']=="eliminar_cliente"){
			echo $ins_venta->eliminar_cliente_venta_controlador();
		}

		/*--------- Buscar codigo ---------*/
		if($_POST['modulo_venta']=="buscar_codigo"){
			echo $ins_venta->buscar_codigo_venta_controlador();
		}

		/*--------- Eliminar venta---------*/
		if($_POST['modulo_venta']=="eliminar_venta"){
			echo $ins_venta->eliminar_venta_controlador();
		}
		
	}else{
		session_destroy();
		header("Location: ".SERVERURL."login/");
	}