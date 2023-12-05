<?php

	if($peticion_ajax){
		require_once "../modelos/mainModel.php";
	}else{
		require_once "./modelos/mainModel.php";
	}

	class cajaControlador extends mainModel{

        /*---------- Controlador agregar caja ----------*/
        public function agregar_caja_controlador(){

            $numero=mainModel::limpiar_cadena($_POST['caja_numero_reg']);
			$nombre=mainModel::limpiar_cadena($_POST['caja_nombre_reg']);
			$estado=mainModel::limpiar_cadena($_POST['caja_estado_reg']);
			

            /*== comprobar campos vacios ==*/
            if($numero=="" || $nombre==""){
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
			if(mainModel::verificar_datos("[0-9]{1,5}",$numero)){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El número de rol no coincide con el formato solicitado",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
			
            if(mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ:# ]{3,70}",$nombre)){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El nombre o código de rol no coincide con el formato solicitado",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}

			
			/*== Comprobando estado de la caja ==*/
			if($estado!="Habilitada" && $estado!="Deshabilitada"){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El estado del rol no es correcto.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
            
            /*== Comprobando numero de caja ==*/
			$check_numero=mainModel::ejecutar_consulta_simple("SELECT rol_numero FROM rol WHERE rol_numero='$numero'");
			if($check_numero->rowCount()>0){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El número de rol ingresado ya se encuentra registrado en el sistema",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
			$check_numero->closeCursor();
			$check_numero=mainModel::desconectar($check_numero);

            /*== Comprobando nombre de caja ==*/
			$check_nombre=mainModel::ejecutar_consulta_simple("SELECT rol_nombre FROM rol WHERE rol_nombre='$nombre'");
			if($check_nombre->rowCount()>0){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El nombre o código del rol ingresado ya se encuentra registrado en el sistema",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
			$check_nombre->closeCursor();
			$check_nombre=mainModel::desconectar($check_nombre);
			
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
			$datos_caja_reg=[
				"rol_numero"=>[
					"campo_marcador"=>":Numero",
					"campo_valor"=>$numero
				],
				"rol_nombre"=>[
					"campo_marcador"=>":Nombre",
					"campo_valor"=>$nombre
				],
				"rol_estado"=>[
					"campo_marcador"=>":Estado",
					"campo_valor"=>$estado
				]
			];
            
			$agregar_movimiento=mainModel::guardar_datos("rol",$datos_caja_reg);


				if($agregar_movimiento->rowCount()==1){
                    $alerta=[
                        "Alerta"=>"recargar",
                        "Titulo"=>"¡Rol registrado!",
                        "Texto"=>"Los datos del nuevo rol se registro con éxito en el sistema",
                        "Tipo"=>"success"
                    ];
                }else{
                    $alerta=[
                        "Alerta"=>"simple",
                        "Titulo"=>"Ocurrió un error inesperado",
                        "Texto"=>"No hemos podido registrar el rol, por favor intente nuevamente",
                        "Tipo"=>"error"
                    ];
                }
                echo json_encode($alerta);
            } /*-- Fin controlador --*/
        

			 /*-- Fin controlador --*/
        /*---------- Controlador paginador caja ----------*/
		public function paginador_caja_controlador($pagina,$registros,$url,$busqueda){

			$pagina=mainModel::limpiar_cadena($pagina);
			$registros=mainModel::limpiar_cadena($registros);

			$url=mainModel::limpiar_cadena($url);
			$url=SERVERURL.$url."/";

			$busqueda=mainModel::limpiar_cadena($busqueda);
			$tabla="";

			$pagina = (isset($pagina) && $pagina>0) ? (int) $pagina : 1;
			$inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;

			if(isset($busqueda) && $busqueda!=""){
				$consulta="SELECT SQL_CALC_FOUND_ROWS * FROM rol WHERE rol_numero LIKE '%$busqueda%' OR rol_nombre LIKE '%$busqueda%' OR rol_estado LIKE '%$busqueda%' ORDER BY rol_numero ASC LIMIT $inicio,$registros";
			}else{
				$consulta="SELECT SQL_CALC_FOUND_ROWS * FROM rol ORDER BY rol_numero ASC LIMIT $inicio,$registros";
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
							<th>NUMERO DE ROL</th>
							<th>NOMBRE / CODIGO</th>
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
							<td>'.$rows['rol_numero'].'</td>
							<td>'.$rows['rol_nombre'].'</td>
							<td>'.$rows['rol_estado'].'</td>
							<td>
								<a class="btn btn-success" href="'.SERVERURL.'cashier-update/'.mainModel::encryption($rows['rol_id']).'/" >
									<i class="fas fa-sync fa-fw"></i>
								</a>
							</td>
                            <td>
								<form class="FormularioAjax" action="'.SERVERURL.'ajax/rolAjax.php" method="POST" data-form="delete" autocomplete="off" >
									<input type="hidden" name="caja_id_del" value="'.mainModel::encryption($rows['rol_id']).'">
									<input type="hidden" name="modulo_rol" value="eliminar">
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
							<td colspan="6">
								<a href="'.$url.'" class="btn btn-raised btn-primary btn-sm">
									Haga clic acá para recargar el listado
								</a>
							</td>
						</tr>
					';
				}else{
					$tabla.='
						<tr class="text-center" >
							<td colspan="6">
								No hay registros en el sistema
							</td>
						</tr>
					';
				}
			}

			$tabla.='</tbody></table></div>';

			if($total>0 && $pagina<=$Npaginas){
				$tabla.='<p class="text-right">Mostrando cajas <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>';
			}

			### Paginacion ###
			if($total>=1 && $pagina<=$Npaginas){
				$tabla.=mainModel::paginador_tablas($pagina,$Npaginas,$url,7);
			}

			return $tabla;
		} /*-- Fin controlador --*/


        
        public function actualizar_caja_controlador(){

			/*== Recuperando id de la caja ==*/
			$id=mainModel::decryption($_POST['caja_id_up']);
			$id=mainModel::limpiar_cadena($id);

			/*== Comprobando caja en la DB ==*/
            $check_caja=mainModel::ejecutar_consulta_simple("SELECT * FROM rol WHERE rol_id='$id'");
            if($check_caja->rowCount()<=0){
            	$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos encontrado el rol en el sistema.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
            }else{
            	$campos=$check_caja->fetch();
			}
			$check_caja->closeCursor();
			$check_caja=mainModel::desconectar($check_caja);

		
			$nombre=mainModel::limpiar_cadena($_POST['caja_nombre_up']);
			$estado=mainModel::limpiar_cadena($_POST['caja_estado_up']);

			/*== Comprobando que los campos no estén vacios ==*/
            if($estado=="" || $nombre==""){
                $alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No has llenado todos los campos que son requeridos.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
			
			/*== Verificando integridad de los datos ==*/
			if($id==1){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No podemos actualizar el rol principal del sistema. Le recomendamos que no modificar el rol",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}

			/*== Preparando datos para enviarlos al modelo ==*/
			$datos_caja_up=[
				"rol_nombre"=>[
					"campo_marcador"=>":Nombre",
					"campo_valor"=>$nombre
				],
				"rol_estado"=>[
					"campo_marcador"=>":Estado",
					"campo_valor"=>$estado
				]
				
			];

            $condicion=[
				"condicion_campo"=>"rol_id",
				"condicion_marcador"=>":ID",
				"condicion_valor"=>$id
			];

			if(mainModel::actualizar_datos("rol",$datos_caja_up,$condicion)){
				$alerta=[
					"Alerta"=>"recargar",
					"Titulo"=>"¡Rol actualizado!",
					"Texto"=>"El rol se actualizo con éxito en el sistema",
					"Tipo"=>"success"
				];
			}else{
				
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos podido actualizar los datos del eol, por favor intente nuevamente",
					"Tipo"=>"error"
				];
			}
			echo json_encode($alerta);
		} /*-- Fin controlador --*/

		/*---------- Controlador eliminar caja ----------*/
		public function eliminar_caja_controlador(){

			/*== Recuperando id de la caja ==*/
			$id=mainModel::decryption($_POST['caja_id_del']);
			$id=mainModel::limpiar_cadena($id);

			/*== Comprobando caja en la DB ==*/
            $check_caja=mainModel::ejecutar_consulta_simple("SELECT rol_id FROM rol WHERE rol_id='$id'");
            if($check_caja->rowCount()<=0){
            	$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El rol que intenta eliminar no existe en el sistema.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
			$check_caja->closeCursor();
			$check_caja=mainModel::desconectar($check_caja);

			/*== Comprobando caja principal ==*/
			if($id==1){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No podemos eliminar el rol principal del sistema. Le recomendamos deshabilitar este rol, si ya no será usado en el sistema",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
			
			/*== Comprobando ventas de la caja ==*/
			$check_ventas=mainModel::ejecutar_consulta_simple("SELECT rol_id FROM usuario WHERE rol_id='$id' LIMIT 1");
			if($check_ventas->rowCount()>0){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No podemos eliminar el rol debido a que tiene usuarios asociados, le recomendamos deshabilitar esta caja si ya no será usada en el sistema.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
			$check_ventas->closeCursor();
			$check_ventas=mainModel::desconectar($check_ventas);

			/*== Comprobando movimientos de la caja ==*/
			$check_movimientos=mainModel::ejecutar_consulta_simple("SELECT rol_id FROM movimiento WHERE rol_id='$id' LIMIT 1");
			if($check_movimientos->rowCount()>0){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No podemos eliminar el rol debido a que tiene movimientos asociados, le recomendamos deshabilitar esta caja si ya no será usada en el sistema.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
			$check_movimientos->closeCursor();
			$check_movimientos=mainModel::desconectar($check_movimientos);

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

			$eliminar_caja=mainModel::eliminar_registro("rol","rol_id",$id);

			if($eliminar_caja->rowCount()==1){
				$alerta=[
					"Alerta"=>"recargar",
					"Titulo"=>"¡Rol eliminado!",
					"Texto"=>"El rol ha sido eliminado del sistema exitosamente.",
					"Tipo"=>"success"
				];
			}else{
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos podido eliminar el rol del sistema, por favor intente nuevamente.",
					"Tipo"=>"error"
				];
			}

			$eliminar_caja->closeCursor();
			$eliminar_caja=mainModel::desconectar($eliminar_caja);

			echo json_encode($alerta);
		} /*-- Fin controlador --*/
    }