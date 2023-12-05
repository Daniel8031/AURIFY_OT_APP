<?php

	if($peticion_ajax){
		require_once "../modelos/mainModel.php";
	}else{
		require_once "./modelos/mainModel.php";
	}

	class proveedorControlador extends mainModel{

		/*---------- Controlador agregar proveedor ----------*/
        public function agregar_proveedor_controlador(){

			$tipo_documento=mainModel::limpiar_cadena($_POST['proveedor_tipo_documento_reg']);
			$numero_documento=mainModel::limpiar_cadena($_POST['proveedor_numero_documento_reg']);
			$nombre=mainModel::limpiar_cadena($_POST['proveedor_nombre_reg']);
			$direccion=mainModel::limpiar_cadena($_POST['proveedor_direccion_reg']);
			$estado=mainModel::limpiar_cadena($_POST['proveedor_estado_reg']);

			$encargado=mainModel::limpiar_cadena($_POST['proveedor_encargado_reg']);
			$telefono=mainModel::limpiar_cadena($_POST['proveedor_telefono_reg']);
			$email=mainModel::limpiar_cadena($_POST['proveedor_email_reg']);
			
			/*== comprobar campos vacios ==*/
            if($numero_documento=="" || $nombre==""){
                $alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No has llenado todos los campos que son obligatorios",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
			
			/*== Verificando integridad de los datos ==*/
			if(mainModel::verificar_datos("[a-zA-Z0-9-]{7,30}",$numero_documento)){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El número de documento no coincide con el formato solicitado",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
			
            if(mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ., ]{4,75}",$nombre)){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El nombre o código de caja no coincide con el formato solicitado",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}

			if($direccion!=""){
				if(mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{4,97}",$direccion)){
					$alerta=[
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrió un error inesperado",
						"Texto"=>"La dirección no coincide con el formato solicitado",
						"Tipo"=>"error"
					];
					echo json_encode($alerta);
					exit();
				}
			}

			if($encargado!=""){
				if(mainModel::verificar_datos("[a-zA-Z ]{4,70}",$encargado)){
					$alerta=[
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrió un error inesperado",
						"Texto"=>"La dirección no coincide con el formato solicitado",
						"Tipo"=>"error"
					];
					echo json_encode($alerta);
					exit();
				}
			}

			if($telefono!=""){
				if(mainModel::verificar_datos("[0-9()+]{8,20}",$telefono)){
					$alerta=[
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrió un error inesperado",
						"Texto"=>"El teléfono no coincide con el formato solicitado",
						"Tipo"=>"error"
					];
					echo json_encode($alerta);
					exit();
				}
			}

			/*== Comprobando estado del proveedor ==*/
			if($estado!="Habilitado" && $estado!="Deshabilitado"){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El estado del proveedor no es correcto.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}

			/*== Comprobando tipo de documento ==*/
			if(!in_array($tipo_documento, DOCUMENTOS_PROVEEDORES)){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El tipo de documento no es correcto.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
            }

			/*== Comprobando documento ==*/
            $check_documento=mainModel::ejecutar_consulta_simple("SELECT proveedor_id FROM proveedor WHERE proveedor_tipo_documento='$tipo_documento' AND proveedor_numero_documento='$numero_documento'");
			if($check_documento->rowCount()>0){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El número y tipo de documento ingresado ya se encuentra registrado en el sistema",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
			$check_documento->closeCursor();
			$check_documento=mainModel::desconectar($check_documento);
			
			/*== Comprobando email ==*/
			if($email!=""){
				if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
					$alerta=[
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrió un error inesperado",
						"Texto"=>"Ha ingresado un correo electrónico no valido.",
						"Tipo"=>"error"
					];
					echo json_encode($alerta);
					exit();
				}
			}
			
			/*== Comprobando privilegios ==*/
			if($_SESSION['cargo_svi']!="Administrador"){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No tienes los permisos necesarios para realizar esta operación en el sistema.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
			
			/*== Preparando datos para enviarlos al modelo ==*/
			$datos_proveedor_reg=[
				"proveedor_tipo_documento"=>[
					"campo_marcador"=>":Tipo",
					"campo_valor"=>$tipo_documento
				],
				"proveedor_numero_documento"=>[
					"campo_marcador"=>":Numero",
					"campo_valor"=>$numero_documento
				],
				"proveedor_nombre"=>[
					"campo_marcador"=>":Nombre",
					"campo_valor"=>$nombre
				],
				"proveedor_direccion"=>[
					"campo_marcador"=>":Direccion",
					"campo_valor"=>$direccion
				],
				"proveedor_telefono"=>[
					"campo_marcador"=>":Telefono",
					"campo_valor"=>$telefono
				],
				"proveedor_email"=>[
					"campo_marcador"=>":Email",
					"campo_valor"=>$email
				],
				"proveedor_contacto"=>[
					"campo_marcador"=>":Contacto",
					"campo_valor"=>$encargado
				],
				"proveedor_estado"=>[
					"campo_marcador"=>":Estado",
					"campo_valor"=>$estado
				]
			];

			$agregar_proveedor=mainModel::guardar_datos("proveedor",$datos_proveedor_reg);

			if($agregar_proveedor->rowCount()==1){
				$alerta=[
					"Alerta"=>"limpiar",
					"Titulo"=>"¡Proveedor registrado!",
					"Texto"=>"Los datos del proveedor se registraron con éxito en el sistema",
					"Tipo"=>"success"
				];
			}else{
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos podido registrar el proveedor, por favor intente nuevamente",
					"Tipo"=>"error"
				];
			}

			$agregar_proveedor->closeCursor();
			$agregar_proveedor=mainModel::desconectar($agregar_proveedor);

			echo json_encode($alerta);
		} /*-- Fin controlador --*/


		/*---------- Controlador paginador proveedor ----------*/
		public function paginador_proveedor_controlador($pagina,$registros,$url,$busqueda){

			$pagina=mainModel::limpiar_cadena($pagina);
			$registros=mainModel::limpiar_cadena($registros);

			$url=mainModel::limpiar_cadena($url);
			$url=SERVERURL.$url."/";

			$busqueda=mainModel::limpiar_cadena($busqueda);
			$tabla="";

			$pagina = (isset($pagina) && $pagina>0) ? (int) $pagina : 1;
			$inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;

			if(isset($busqueda) && $busqueda!=""){
				$consulta="SELECT SQL_CALC_FOUND_ROWS * FROM proveedor WHERE proveedor_tipo_documento LIKE '%$busqueda%' OR proveedor_numero_documento LIKE '%$busqueda%' OR proveedor_nombre LIKE '%$busqueda%' OR proveedor_telefono LIKE '%$busqueda%' OR proveedor_email LIKE '%$busqueda%' OR proveedor_estado LIKE '%$busqueda%' ORDER BY proveedor_nombre ASC LIMIT $inicio,$registros";
			}else{
				$consulta="SELECT SQL_CALC_FOUND_ROWS * FROM proveedor ORDER BY proveedor_nombre ASC LIMIT $inicio,$registros";
			}

			$conexion = mainModel::conectar();

			$datos = $conexion->query($consulta);

			$datos = $datos->fetchAll();

			$total = $conexion->query("SELECT FOUND_ROWS()");
			$total = (int) $total->fetchColumn();

			$Npaginas =ceil($total/$registros);

			### Cuerpo de la tabla ###
			$tabla.='
				<div class="table-responsive">
				<table class="table table-dark table-sm">
					<thead>
						<tr class="text-center roboto-medium">
							<th>#</th>
							<th>DOCUMENTO</th>
                            <th>NOMBRE</th>
                            <th>TELEFONO</th>
							<th>EMAIL</th>
							<th>ESTADO</th>
							<th>ACTUALIZAR</th>
                            <th>ELIMINAR</th>
                        </tr>
					</thead>
					<tbody>
			';

			if($total>=1 && $pagina<=$Npaginas){
				$contador=$inicio+1;
				$pag_inicio=$inicio+1;
				foreach($datos as $rows){
					$tabla.='
						<tr class="text-center" >
							<td>'.$contador.'</td>
                            <td>'.$rows['proveedor_tipo_documento'].': '.$rows['proveedor_numero_documento'].'</td>
                            <td>'.$rows['proveedor_nombre'].'</td>
							<td>'.$rows['proveedor_telefono'].'</td>
							<td>'.$rows['proveedor_email'].'</td>
							<td>'.$rows['proveedor_estado'].'</td>
							<td>
								<a class="btn btn-success" href="'.SERVERURL.'provider-update/'.mainModel::encryption($rows['proveedor_id']).'/" >
									<i class="fas fa-sync fa-fw"></i>
								</a>
							</td>
                            <td>
								<form class="FormularioAjax" action="'.SERVERURL.'ajax/proveedorAjax.php" method="POST" data-form="delete" autocomplete="off" >
									<input type="hidden" name="proveedor_id_del" value="'.mainModel::encryption($rows['proveedor_id']).'">
									<input type="hidden" name="modulo_proveedor" value="eliminar">
									<button type="submit" class="btn btn-warning">
										<i class="far fa-trash-alt"></i>
									</button>
								</form>
                            </td>
                        </tr>
                    ';
                    $contador++;
				}
				$pag_final=$contador-1;
			}else{
				if($total>=1){
					$tabla.='
						<tr class="text-center" >
							<td colspan="8">
								<a href="'.$url.'" class="btn btn-raised btn-primary btn-sm">
									Haga clic acá para recargar el listado
								</a>
							</td>
						</tr>
					';
				}else{
					$tabla.='
						<tr class="text-center" >
							<td colspan="8">
								No hay registros en el sistema
							</td>
						</tr>
					';
				}
			}

			$tabla.='</tbody></table></div>';

			if($total>0 && $pagina<=$Npaginas){
				$tabla.='<p class="text-right">Mostrando proveedores <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>';
			}

			### Paginacion ###
			if($total>=1 && $pagina<=$Npaginas){
				$tabla.=mainModel::paginador_tablas($pagina,$Npaginas,$url,7);
			}

			return $tabla;
		} /*-- Fin controlador --*/


		/*---------- Controlador actualizar proveedor ----------*/
		public function actualizar_proveedor_controlador(){

			/*== Recuperando id del proveedor ==*/
			$id=mainModel::decryption($_POST['proveedor_id_up']);
			$id=mainModel::limpiar_cadena($id);

			/*== Comprobando proveedor en la DB ==*/
            $check_proveedor=mainModel::ejecutar_consulta_simple("SELECT * FROM proveedor WHERE proveedor_id='$id'");
            if($check_proveedor->rowCount()<=0){
            	$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos encontrado el proveedor en el sistema.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
            }else{
            	$campos=$check_proveedor->fetch();
			}
			$check_proveedor->closeCursor();
			$check_proveedor=mainModel::desconectar($check_proveedor);
			
			$tipo_documento=mainModel::limpiar_cadena($_POST['proveedor_tipo_documento_up']);
			$numero_documento=mainModel::limpiar_cadena($_POST['proveedor_numero_documento_up']);
			$nombre=mainModel::limpiar_cadena($_POST['proveedor_nombre_up']);
			$direccion=mainModel::limpiar_cadena($_POST['proveedor_direccion_up']);
			$estado=mainModel::limpiar_cadena($_POST['proveedor_estado_up']);

			$encargado=mainModel::limpiar_cadena($_POST['proveedor_encargado_up']);
			$telefono=mainModel::limpiar_cadena($_POST['proveedor_telefono_up']);
			$email=mainModel::limpiar_cadena($_POST['proveedor_email_up']);

			/*== comprobar campos vacios ==*/
            if($numero_documento=="" || $nombre==""){
                $alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No has llenado todos los campos que son obligatorios",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
			
			/*== Verificando integridad de los datos ==*/
			if(mainModel::verificar_datos("[a-zA-Z0-9-]{7,30}",$numero_documento)){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El número de documento no coincide con el formato solicitado",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
			
            if(mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ., ]{4,75}",$nombre)){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El nombre o código de caja no coincide con el formato solicitado",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}

			if($direccion!=""){
				if(mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{4,97}",$direccion)){
					$alerta=[
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrió un error inesperado",
						"Texto"=>"La dirección no coincide con el formato solicitado",
						"Tipo"=>"error"
					];
					echo json_encode($alerta);
					exit();
				}
			}

			if($encargado!=""){
				if(mainModel::verificar_datos("[a-zA-Z ]{4,70}",$encargado)){
					$alerta=[
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrió un error inesperado",
						"Texto"=>"La dirección no coincide con el formato solicitado",
						"Tipo"=>"error"
					];
					echo json_encode($alerta);
					exit();
				}
			}

			if($telefono!=""){
				if(mainModel::verificar_datos("[0-9()+]{8,20}",$telefono)){
					$alerta=[
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrió un error inesperado",
						"Texto"=>"El teléfono no coincide con el formato solicitado",
						"Tipo"=>"error"
					];
					echo json_encode($alerta);
					exit();
				}
			}

			/*== Comprobando estado del proveedor ==*/
			if($estado!="Habilitado" && $estado!="Deshabilitado"){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El estado del proveedor no es correcto.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}

			/*== Comprobando tipo de documento ==*/
			if(!in_array($tipo_documento, DOCUMENTOS_PROVEEDORES)){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El tipo de documento no es correcto.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
			
			/*== Comprobando documento ==*/
			if($tipo_documento!=$campos['proveedor_tipo_documento'] || $numero_documento!=$campos['proveedor_numero_documento']){
				$check_documento=mainModel::ejecutar_consulta_simple("SELECT proveedor_id FROM proveedor WHERE proveedor_tipo_documento='$tipo_documento' AND proveedor_numero_documento='$numero_documento'");
				if($check_documento->rowCount()>0){
					$alerta=[
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrió un error inesperado",
						"Texto"=>"El número y tipo de documento ingresado ya se encuentra registrado en el sistema",
						"Tipo"=>"error"
					];
					echo json_encode($alerta);
					exit();
				}
				$check_documento->closeCursor();
				$check_documento=mainModel::desconectar($check_documento);
			}
            
			
			/*== Comprobando email ==*/
			if($email!=$campos['proveedor_email'] && $email!=""){
				if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
					$alerta=[
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrió un error inesperado",
						"Texto"=>"Ha ingresado un correo electrónico no valido.",
						"Tipo"=>"error"
					];
					echo json_encode($alerta);
					exit();
				}
			}
			
			/*== Comprobando privilegios ==*/
			if($_SESSION['cargo_svi']!="Administrador"){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No tienes los permisos necesarios para realizar esta operación en el sistema.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}

			/*== Preparando datos para enviarlos al modelo ==*/
			$datos_proveedor_up=[
				"proveedor_tipo_documento"=>[
					"campo_marcador"=>":Tipo",
					"campo_valor"=>$tipo_documento
				],
				"proveedor_numero_documento"=>[
					"campo_marcador"=>":Numero",
					"campo_valor"=>$numero_documento
				],
				"proveedor_nombre"=>[
					"campo_marcador"=>":Nombre",
					"campo_valor"=>$nombre
				],
				"proveedor_direccion"=>[
					"campo_marcador"=>":Direccion",
					"campo_valor"=>$direccion
				],
				"proveedor_telefono"=>[
					"campo_marcador"=>":Telefono",
					"campo_valor"=>$telefono
				],
				"proveedor_email"=>[
					"campo_marcador"=>":Email",
					"campo_valor"=>$email
				],
				"proveedor_contacto"=>[
					"campo_marcador"=>":Contacto",
					"campo_valor"=>$encargado
				],
				"proveedor_estado"=>[
					"campo_marcador"=>":Estado",
					"campo_valor"=>$estado
				]
			];

			$condicion=[
				"condicion_campo"=>"proveedor_id",
				"condicion_marcador"=>":ID",
				"condicion_valor"=>$id
			];

			if(mainModel::actualizar_datos("proveedor",$datos_proveedor_up,$condicion)){
				$alerta=[
					"Alerta"=>"recargar",
					"Titulo"=>"¡Proveedor actualizado!",
					"Texto"=>"El proveedor se actualizo con éxito en el sistema",
					"Tipo"=>"success"
				];
			}else{
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos podido actualizar los datos del proveedor, por favor intente nuevamente",
					"Tipo"=>"error"
				];
			}
			echo json_encode($alerta);
		} /*-- Fin controlador --*/


		/*---------- Controlador eliminar proveedor ----------*/
		public function eliminar_proveedor_controlador(){

			/*== Recuperando id del proveedor ==*/
			$id=mainModel::decryption($_POST['proveedor_id_del']);
			$id=mainModel::limpiar_cadena($id);

			/*== Comprobando proveedor en la DB ==*/
            $check_proveedor=mainModel::ejecutar_consulta_simple("SELECT proveedor_id FROM proveedor WHERE proveedor_id='$id'");
            if($check_proveedor->rowCount()<=0){
            	$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El proveedor que intenta eliminar no existe en el sistema.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
			$check_proveedor->closeCursor();
			$check_proveedor=mainModel::desconectar($check_proveedor);

			/*== Comprobando productos del proveedor ==*/
			$check_productos=mainModel::ejecutar_consulta_simple("SELECT proveedor_id FROM producto WHERE proveedor_id='$id' LIMIT 1");
			if($check_productos->rowCount()>0){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No podemos eliminar el proveedor debido a que tiene productos asociados, le recomendamos deshabilitar este proveedor si ya no será usado en el sistema.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
			$check_productos->closeCursor();
			$check_productos=mainModel::desconectar($check_productos);

			/*== Comprobando compras del proveedor ==*/
			$check_compras=mainModel::ejecutar_consulta_simple("SELECT proveedor_id FROM compra WHERE proveedor_id='$id' LIMIT 1");
			if($check_compras->rowCount()>0){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No podemos eliminar el proveedor debido a que tiene compras asociadas, le recomendamos deshabilitar este proveedor si ya no será usado en el sistema.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
			$check_compras->closeCursor();
			$check_compras=mainModel::desconectar($check_compras);

			/*== Comprobando privilegios ==*/
			if($_SESSION['cargo_svi']!="Administrador"){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No tienes los permisos necesarios para realizar esta operación en el sistema.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}

			$eliminar_proveedor=mainModel::eliminar_registro("proveedor","proveedor_id",$id);

			if($eliminar_proveedor->rowCount()==1){
				$alerta=[
					"Alerta"=>"recargar",
					"Titulo"=>"¡Proveedor eliminado!",
					"Texto"=>"El proveedor ha sido eliminado del sistema exitosamente.",
					"Tipo"=>"success"
				];
			}else{
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos podido eliminar el proveedor del sistema, por favor intente nuevamente.",
					"Tipo"=>"error"
				];
			}

			$eliminar_proveedor->closeCursor();
			$eliminar_proveedor=mainModel::desconectar($eliminar_proveedor);

			echo json_encode($alerta);
		} /*-- Fin controlador --*/
    }