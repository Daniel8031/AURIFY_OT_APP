<?php

	if($peticion_ajax){
		require_once "../modelos/loginModelo.php";
	}else{
		require_once "./modelos/loginModelo.php";
	}

	class loginControlador extends loginModelo{

		/*----------  Controlador iniciar sesion  ----------*/
		public function iniciar_sesion_controlador(){

			$usuario=mainModel::limpiar_cadena($_POST['usuario_log']);
			$clave=mainModel::limpiar_cadena($_POST['clave_log']);

			/*== Comprobando campos vacios ==*/
			if($usuario=="" || $clave==""){
				echo'<script>
					Swal.fire({
					  title: "Ocurrió un error inesperado",
					  text: "No has llenado todos los campos que son requeridos.",
					  type: "error",
					  confirmButtonText: "Aceptar"
					});
				</script>';
				exit();
			}


			/*== Verificando integridad datos ==*/
			if(mainModel::verificar_datos("[a-zA-Z0-9]{4,35}",$usuario)){
				echo'<script>
					Swal.fire({
					  title: "Ocurrió un error inesperado",
					  text: "El nombre de usuario no coincide con el formato solicitado.",
					  type: "error",
					  confirmButtonText: "Aceptar"
					});
				</script>';
				exit();
			}
			if(mainModel::verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$clave)){
				echo'<script>
					Swal.fire({
					  title: "Ocurrió un error inesperado",
					  text: "La contraseña no coincide con el formato solicitado.",
					  type: "error",
					  confirmButtonText: "Aceptar"
					});
				</script>';
				exit();
			}

			$clave=mainModel::encryption($clave);

			$datos_login=[
				"Usuario"=>$usuario,
				"Clave"=>$clave
			];

			$datos_cuenta=loginModelo::iniciar_sesion_modelo($datos_login);

			if($datos_cuenta->rowCount()==1){

				$row=$datos_cuenta->fetch();

				$datos_cuenta->closeCursor();
			    $datos_cuenta=mainModel::desconectar($datos_cuenta);

				$_SESSION['id_svi']=$row['usuario_id'];
				$_SESSION['nombre_svi']=$row['usuario_nombre'];
				$_SESSION['apellido_svi']=$row['usuario_apellido'];
				$_SESSION['genero_svi']=$row['usuario_genero'];
				$_SESSION['usuario_svi']=$row['usuario_usuario'];
				$_SESSION['cargo_svi']=$row['usuario_cargo'];
				$_SESSION['foto_svi']=$row['usuario_foto'];
				$_SESSION['lector_estado_svi']=$row['usuario_lector'];
				$_SESSION['lector_codigo_svi']=$row['usuario_tipo_codigo'];
				$_SESSION['caja_svi']=$row['rol_id'];
				$_SESSION['token_svi']=mainModel::encryption(uniqid(mt_rand(), true));

				if(headers_sent()){
					echo "<script> window.location.href='".SERVERURL."dashboard/'; </script>";
				}else{
					return header("Location: ".SERVERURL."dashboard/");
				}

			}else{
				echo'<script>
					Swal.fire({
					  title: "Ocurrió un error inesperado",
					  text: "El nombre de usuario o contraseña no son correctos.",
					  type: "error",
					  confirmButtonText: "Aceptar"
					});
				</script>';
			}
		} /*-- Fin controlador --*/


		/*----------  Controlador forzar cierre de sesion  ----------*/
		public function forzar_cierre_sesion_controlador(){

			unset($_SESSION['id_svi']);
			unset($_SESSION['nombre_svi']);
			unset($_SESSION['apellido_svi']);
			unset($_SESSION['genero_svi']);
			unset($_SESSION['usuario_svi']);
			unset($_SESSION['cargo_svi']);
			unset($_SESSION['foto_svi']);
			unset($_SESSION['lector_estado_svi']);
			unset($_SESSION['lector_codigo_svi']);
			// unset($_SESSION['caja_svi']);
			unset($_SESSION['token_svi']);

			session_destroy();
			
			if(headers_sent()){
				echo "<script> window.location.href='".SERVERURL."login/'; </script>";
			}else{
				return header("Location: ".SERVERURL."login/");
			}
		} /*-- Fin controlador --*/


		/*----------  Controlador cierre de sesion  ----------*/
		public function cerrar_sesion_controlador(){

			$token=mainModel::decryption($_POST['token']);
			$usuario=mainModel::decryption($_POST['usuario']);

			if($token==$_SESSION['token_svi'] && $usuario==$_SESSION['usuario_svi']){

				unset($_SESSION['id_svi']);
				unset($_SESSION['nombre_svi']);
				unset($_SESSION['apellido_svi']);
				unset($_SESSION['genero_svi']);
				unset($_SESSION['usuario_svi']);
				unset($_SESSION['cargo_svi']);
				unset($_SESSION['foto_svi']);
				unset($_SESSION['lector_estado_svi']);
				unset($_SESSION['lector_codigo_svi']);
				// unset($_SESSION['caja_svi']);
				unset($_SESSION['token_svi']);

				session_destroy();

				$alerta=[
					"Alerta"=>"redireccionar",
					"URL"=>SERVERURL."login/"
				];
			}else{
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No se pudo cerrar la sesión.",
					"Tipo"=>"error"
				];
			}
			echo json_encode($alerta);
		} /*-- Fin controlador --*/
	}