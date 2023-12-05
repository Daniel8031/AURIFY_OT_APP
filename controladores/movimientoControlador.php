
<?php

	if($peticion_ajax){
		require_once "../modelos/mainModel.php";
	}else{
		require_once "./modelos/mainModel.php";
	}

	class movimientoControlador extends mainModel{

        /*---------- Controlador agregar movimiento ----------*/
        public function agregar_movimiento_controlador(){

            // $caja=mainModel::limpiar_cadena($_POST['movimiento_caja_reg']);
            $reporte=mainModel::limpiar_cadena($_POST['movimiento_reg']);
            $tipo=mainModel::limpiar_cadena($_POST['movimiento_tipo_reg']);

            $folio=mainModel::limpiar_cadena($_POST['folio']);
			$sala=mainModel::limpiar_cadena($_POST['sala']);
            $hora=mainModel::limpiar_cadena($_POST['hora_entrada']);
			// todo funciona
            $hora_salida=mainModel::limpiar_cadena($_POST['hora_salida']);
            $fecha=mainModel::limpiar_cadena($_POST['fecha']);
            $ciudad=mainModel::limpiar_cadena($_POST['city']);
            $inge=mainModel::limpiar_cadena($_POST['ingeniero']);
            $responsable=mainModel::limpiar_cadena($_POST['respon']);
            $terminal=mainModel::limpiar_cadena($_POST['termi']);
            $descripcion=mainModel::limpiar_cadena($_POST['descripcion']);
            $pieza=mainModel::limpiar_cadena($_POST['pieza']);
            $comentarios=mainModel::limpiar_cadena($_POST['comentarios']);
           
             
			if($tipo=="" || $folio=="" || $sala=="" || $hora=="" || $hora_salida=="" || $fecha=="" || $ciudad=="" || $tipo=="" || $inge=="" || $responsable=="" || $terminal=="" || $descripcion=="" || $comentarios==""){
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
$correlativo=mainModel::ejecutar_consulta_simple("SELECT movimiento_id FROM movimiento");
$correlativo=($correlativo->rowCount())+1;
$codigo=mainModel::generar_codigo_aleatorio(8,$correlativo);

			$datos_movimiento_reg=[
                "movimiento_codigo"=>[
					"campo_marcador"=>":Codigo",
					"campo_valor"=>$codigo
				],
                "movimiento_folio"=>[
					"campo_marcador"=>":Nombre",
					"campo_valor"=>$folio
				],
                "movimiento_fecha"=>[
					"campo_marcador"=>":Fecha",
					"campo_valor"=>$fecha
				],
                "movimiento_hora"=>[
					"campo_marcador"=>":Hora",
					"campo_valor"=>$hora
				],
                "movimiento_hora_salida"=>[
					"campo_marcador"=>":Hora_salida",
					"campo_valor"=>$hora_salida
				],
				"movimiento_tipo"=>[
					"campo_marcador"=>":Movimiento",
					"campo_valor"=>$tipo
				],
                "movimiento_motivo"=>[
					"campo_marcador"=>":Motivo",
					"campo_valor"=>$reporte
				],
                "movimiento_ciudad"=>[
					"campo_marcador"=>":Ciudad",
					"campo_valor"=>$ciudad
				],
				"movimiento_sala"=>[
					"campo_marcador"=>":Sala",
					"campo_valor"=>$sala
                ],
                "movimiento_ingeniero"=>[
					"campo_marcador"=>":Ingeniero",
					"campo_valor"=>$inge
				],
                "movimiento_responsable"=>[
					"campo_marcador"=>":Responsable",
					"campo_valor"=>$responsable
				],
                "movimiento_terminales"=>[
					"campo_marcador"=>":Terminales",
					"campo_valor"=>$terminal
				],
                "movimiento_descripcion"=>[
					"campo_marcador"=>":Descripcion",
					"campo_valor"=>$descripcion
				],
                "movimiento_pieza"=>[
					"campo_marcador"=>":Pieza",
					"campo_valor"=>$pieza
				],
                "movimiento_comentarios"=>[
					"campo_marcador"=>":Comentarios",
					"campo_valor"=>$comentarios
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
            
            $agregar_movimiento=mainModel::guardar_datos("movimiento",$datos_movimiento_reg);

			$_SESSION['factura']=$codigo;
            if($agregar_movimiento->rowCount()==1){
				
                $alerta=[
					"Alerta"=>"recargar",
					"Titulo"=>"¡Reporte registrado!",
					"Texto"=>"Los datos del reporte se registraron con éxito en el sistema",
					"Tipo"=>"success"
				];
                
			}else{
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos podido registrar el reporte, por favor intente nuevamente",
					"Tipo"=>"error"
				];
			}

			echo json_encode($alerta);
                exit();
			
			$agregar_movimiento->closeCursor();
			$agregar_movimiento=mainModel::desconectar($agregar_movimiento);
 /*-- Fin controlador --*/
    }
    public function paginador_movimiento_controlador($pagina,$registros,$url,$tipo,$fecha_inicio,$fecha_final){

        $pagina=mainModel::limpiar_cadena($pagina);
        $registros=mainModel::limpiar_cadena($registros);

        $url=mainModel::limpiar_cadena($url);
        $url=SERVERURL.$url."/";

        $tipo=mainModel::limpiar_cadena($tipo);
        $fecha_inicio=mainModel::limpiar_cadena($fecha_inicio);
        $fecha_final=mainModel::limpiar_cadena($fecha_final);
        $id_search = $_SESSION['id_svi'];
        $tabla="";

        $pagina = (isset($pagina) && $pagina>0) ? (int) $pagina : 1;
        $inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;
        
        if($tipo=="Busqueda"){
            if(mainModel::verificar_fecha($fecha_inicio) || mainModel::verificar_fecha($fecha_final)){
                return '
                    <div class="alert alert-danger text-center" role="alert">
                        <p><i class="fas fa-exclamation-triangle fa-5x"></i></p>
                        <h4 class="alert-heading">¡Ocurrió un error inesperado!</h4>
                        <p class="mb-0">Lo sentimos, no podemos realizar la búsqueda ya que al parecer a ingresado una fecha incorrecta.</p>
                    </div>
                ';
                exit();
            }
        }
        
        $campos_tablas="movimiento.movimiento_id,movimiento.movimiento_codigo,movimiento.movimiento_folio,movimiento.movimiento_fecha,movimiento.movimiento_hora,movimiento.movimiento_hora_salida,movimiento.movimiento_tipo,movimiento.movimiento_motivo,movimiento.usuario_id,rol.rol_numero,rol.rol_nombre,usuario.usuario_nombre,usuario.usuario_apellido";

        if($tipo=="Busqueda" && $fecha_inicio!="" && $fecha_final!=""){
            $consulta="SELECT SQL_CALC_FOUND_ROWS $campos_tablas FROM movimiento INNER JOIN rol ON movimiento.usuario_id=rol.rol_id INNER JOIN usuario ON movimiento.usuario_id=usuario.usuario_id WHERE (movimiento_fecha BETWEEN '$fecha_inicio' AND '$fecha_final') ORDER BY movimiento_id DESC LIMIT $inicio,$registros";
        }else{
            $consulta="SELECT  SQL_CALC_FOUND_ROWS $campos_tablas FROM movimiento INNER JOIN rol ON rol.rol_id = movimiento.rol_id INNER JOIN usuario ON usuario.usuario_id=movimiento.usuario_id where usuario.usuario_id= '$id_search' ORDER BY movimiento.movimiento_id DESC LIMIT $inicio,$registros";
           



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
                        <th>ROL</th>
                        <th>FECHA Y HORA</th>
                        <th>TIPO RPT</th>
                        <th>HORA S</th>
                        <th>FOLIO</th>
                        <th>CODIGO</th>
                        <th>USUARIO</th>
                        <th>MOTIVO</th>
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
                        <td>'.$rows['rol_nombre'].'</td>
                        <td>'.date("d-m-Y", strtotime($rows['movimiento_fecha'])).' '.$rows['movimiento_hora'].'</td>
                        <td>'.$rows['movimiento_tipo'].'</td>
                        <td>'.$rows['movimiento_hora_salida'].'</td>
                        <td>'.$rows['movimiento_folio'].'</td>
                        <td>'.$rows['movimiento_codigo'].'</td>
                        <td>'.$rows['usuario_nombre'].' '.$rows['usuario_apellido'].'</td>
                        <td>
                            <button type="button" class="btn btn-info" data-toggle="popover" data-trigger="hover" title="'.$rows['movimiento_tipo'].'" data-content="'.$rows['movimiento_motivo'].'" >
                                <i class="fas fa-info-circle"></i>
                            </button>
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
                        <td colspan="9">
                            <a href="'.$url.'" class="btn btn-raised btn-primary btn-sm">
                                Haga clic acá para recargar el listado
                            </a>
                        </td>
                    </tr>
                ';
            }else{
                $tabla.='
                    <tr class="text-center" >
                        <td colspan="9">
                            No hay registros en el sistema
                        </td>
                    </tr>
                ';
            }
        }

        $tabla.='</tbody></table></div>';

        if($total>0 && $pagina<=$Npaginas){
            $tabla.='<p class="text-right">Mostrando movimientos <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>';
        }

        ### Paginacion ###
        if($total>=1 && $pagina<=$Npaginas){
            $tabla.=mainModel::paginador_tablas($pagina,$Npaginas,$url,7);
        }

        return $tabla;
    } /*-- Fin controlador --*/
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
        
        $agregar_movimiento=mainModel::guardar_datos("game_group",$datos_movimiento_reg);

        // $_SESSION['factura']=$codigo;
        if($agregar_movimiento->rowCount()==1){
            
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
        
        $agregar_movimiento->closeCursor();
        $agregar_movimiento=mainModel::desconectar($agregar_movimiento);
/*-- Fin controlador --*/
}
}



// <?php

// es un codigo de respaldo

// 	if($peticion_ajax){
// 		require_once "../modelos/mainModel.php";
// 	}else{
// 		require_once "./modelos/mainModel.php";
// 	}

// 	class movimientoControlador extends mainModel{

//         /*---------- Controlador agregar movimiento ----------*/
//         public function agregar_movimiento_controlador(){

//             // $caja=mainModel::limpiar_cadena($_POST['movimiento_caja_reg']);
//             $reporte=mainModel::limpiar_cadena($_POST['movimiento_reg']);
//             $tipo=mainModel::limpiar_cadena($_POST['movimiento_tipo_reg']);

//             $folio=mainModel::limpiar_cadena($_POST['folio']);
// 			$sala=mainModel::limpiar_cadena($_POST['sala']);
//             $hora=mainModel::limpiar_cadena($_POST['hora_entrada']);
// 			// todo funciona
//             $hora_salida=mainModel::limpiar_cadena($_POST['hora_salida']);
//             $fecha=mainModel::limpiar_cadena($_POST['fecha']);
//             $ciudad=mainModel::limpiar_cadena($_POST['city']);
//             $inge=mainModel::limpiar_cadena($_POST['ingeniero']);
//             $responsable=mainModel::limpiar_cadena($_POST['respon']);
//             $terminal=mainModel::limpiar_cadena($_POST['termi']);
//             $descripcion=mainModel::limpiar_cadena($_POST['descripcion']);
//             $pieza=mainModel::limpiar_cadena($_POST['pieza']);
//             $comentarios=mainModel::limpiar_cadena($_POST['comentarios']);
            
           
//             if($tipo=="" || $sala==""){
//                 $alerta=[
// 					"Alerta"=>"simple",
// 					"Titulo"=>"Ocurrió un error inesperado",
// 					"Texto"=>"No has llenado todos los campos que son obligatorios",
// 					"Tipo"=>"error"
// 				];
// 				echo json_encode($alerta);
// 				exit();
//             }
//             if(mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{3,100}",$sala)){
// 				$alerta=[
// 					"Alerta"=>"simple",
// 					"Titulo"=>"Ocurrió un error inesperado",
// 					"Texto"=>"El pasillo o ubicación de la categoría no coincide con el formato solicitado",
// 					"Tipo"=>"error"
// 				];
// 				echo json_encode($alerta);
// 				exit();
//             }
            
			
//             if($_SESSION['cargo_svi']!="Administrador"){
// 				$alerta=[
// 					"Alerta"=>"simple",
// 					"Titulo"=>"Ocurrió un error inesperado",
// 					"Texto"=>"No tienes los permisos necesarios para realizar esta operación en el sistema.",
// 					"Tipo"=>"error"
// 				];
// 				echo json_encode($alerta);
// 				exit();
// 			}

// /*== Ajustando parametros del movimiento ==*/
// $correlativo=mainModel::ejecutar_consulta_simple("SELECT movimiento_id FROM movimiento");
// $correlativo=($correlativo->rowCount())+1;
// $codigo=mainModel::generar_codigo_aleatorio(8,$correlativo);
// 			$datos_movimiento_reg=[
//                 "movimiento_codigo"=>[
// 					"campo_marcador"=>":Codigo",
// 					"campo_valor"=>$codigo
// 				],
//                 "movimiento_folio"=>[
// 					"campo_marcador"=>":Nombre",
// 					"campo_valor"=>$folio
// 				],
//                 "movimiento_fecha"=>[
// 					"campo_marcador"=>":Fecha",
// 					"campo_valor"=>$fecha
// 				],
//                 "movimiento_hora"=>[
// 					"campo_marcador"=>":Hora",
// 					"campo_valor"=>$hora
// 				],
//                 "movimiento_hora_salida"=>[
// 					"campo_marcador"=>":Hora_salida",
// 					"campo_valor"=>$hora_salida
// 				],
// 				"movimiento_tipo"=>[
// 					"campo_marcador"=>":Movimiento",
// 					"campo_valor"=>$tipo
// 				],
//                 "movimiento_motivo"=>[
// 					"campo_marcador"=>":Motivo",
// 					"campo_valor"=>$reporte
// 				],
//                 "movimiento_ciudad"=>[
// 					"campo_marcador"=>":Ciudad",
// 					"campo_valor"=>$ciudad
// 				],
// 				"movimiento_sala"=>[
// 					"campo_marcador"=>":Sala",
// 					"campo_valor"=>$sala
//                 ],
//                 "movimiento_ingeniero"=>[
// 					"campo_marcador"=>":Ingeniero",
// 					"campo_valor"=>$inge
// 				],
//                 "movimiento_responsable"=>[
// 					"campo_marcador"=>":Responsable",
// 					"campo_valor"=>$responsable
// 				],
//                 "movimiento_terminales"=>[
// 					"campo_marcador"=>":Terminales",
// 					"campo_valor"=>$terminal
// 				],
//                 "movimiento_descripcion"=>[
// 					"campo_marcador"=>":Descripcion",
// 					"campo_valor"=>$descripcion
// 				],
//                 "movimiento_pieza"=>[
// 					"campo_marcador"=>":Pieza",
// 					"campo_valor"=>$pieza
// 				],
//                 "movimiento_comentarios"=>[
// 					"campo_marcador"=>":Pieza",
// 					"campo_valor"=>$pieza
// 				],
//                 "usuario_id"=>[
// 					"campo_marcador"=>":Usuario",
// 					"campo_valor"=>$_SESSION['id_svi']
// 				]

// 			];

//             $agregar_movimiento=mainModel::guardar_datos("movimiento",$datos_movimiento_reg);
            
//             if($agregar_movimiento->rowCount()==1){
// 				$alerta=[
// 					"Alerta"=>"limpiar",
// 					"Titulo"=>"¡Categoría registrada!",
// 					"Texto"=>"La categoría se registró con éxito en el sistema",
// 					"Tipo"=>"success"
                    
// 				];
//                 $_SESSION['factura']=$folio;
// 			}else{
// 				$alerta=[
// 					"Alerta"=>"simple",
// 					"Titulo"=>"Ocurrió un error inesperado",
// 					"Texto"=>"No hemos podido registrar la categoría, por favor intente nuevamente",
// 					"Tipo"=>"error"
// 				];
// 			}
			
// 			$agregar_movimiento->closeCursor();
// 			$agregar_movimiento=mainModel::desconectar($agregar_movimiento);
               
// 			echo json_encode($alerta);
           
            
//  /*-- Fin controlador --*/
//     }
// }







