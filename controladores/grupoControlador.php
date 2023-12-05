<?php

if($peticion_ajax){
		require_once "../modelos/mainModel.php";
	}else{
		require_once "./modelos/mainModel.php";
	}

	class grupoControlador extends mainModel{

        /*---------- Controlador agregar movimiento ----------*/
       
		public function agregar_modulo_game_controlador(){

			// $caja=mainModel::limpiar_cadena($_POST['movimiento_caja_reg']);
			$grupo_game=mainModel::limpiar_cadena($_POST['grupo_game']);
			$tema_game=mainModel::limpiar_cadena($_POST['tema_game']);
	
			 
			if($tema_game=="" || $grupo_game==""){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No has llenado todos los campos que son obligatorios",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
		
			if($_SESSION['cargo_svi']!="Administrador" && $_SESSION['cargo_svi']!="Usuario"){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No tienes los permisos necesarios para realizar esta operación en el sistema.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
	
	/*== Ajustando parametros del movimiento ==*/
	$correlativo=mainModel::ejecutar_consulta_simple("SELECT id_group FROM game_group");
	$correlativo=($correlativo->rowCount())+1;
	$codigo=mainModel::generar_codigo_aleatorio(8,$correlativo);
	
			$datos_movimiento_reg=[
				"group_codigo"=>[
					"campo_marcador"=>":Codigo",
					"campo_valor"=>$codigo
				],
				"group_name"=>[
					"campo_marcador"=>":Name",
					"campo_valor"=>$grupo_game
				],
				"group_theme"=>[
					"campo_marcador"=>":Theme",
					"campo_valor"=>$tema_game
				],
				"usuario_id"=>[
					"campo_marcador"=>":Usuario",
					"campo_valor"=>$_SESSION['id_svi']
				],
				"rol_id"=>[
					"campo_marcador"=>":Rol",
					"campo_valor"=>$_SESSION['caja_svi']
				],
	
			];
			
			$agregar_movimientos=mainModel::guardar_datos("game_group",$datos_movimiento_reg);
	
			// $_SESSION['factura']=$codigo;
			if($agregar_movimientos->rowCount()==1){
				
				$alerta=[
					"Alerta"=>"cambiar",
					"Titulo"=>"¡Grupo de juego registrado, redirigiendo..",
					"Tipo"=>"success",
					"URL"=>SERVERURL."sale-new/".$codigo."/"
					// "Alerta"=>"redireccionar",
					// "URL"=>SERVERURL."dashboard/"
	
				];
				
			}else{
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos podido registrar el Grupo de juego, por favor intente nuevamente",
					"Tipo"=>"error"
				];
			}
	
			echo json_encode($alerta);
				exit();
			
			$agregar_movimientos->closeCursor();
			$agregar_movimientos=mainModel::desconectar($agregar_movimientos);
	/*-- Fin controlador --*/
	}
	}
	