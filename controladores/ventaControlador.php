<?php

	if($peticion_ajax){
		require_once "../modelos/mainModel.php";
	}else{
		require_once "./modelos/mainModel.php";
	}

	class ventaControlador extends mainModel{
        
        /*---------- Controlador agregar producto a venta ----------*/
        public function agregar_producto_carrito_controlador(){
 
            if($_SESSION['lector_codigo_svi']=="Barras"){
                $campo_tabla="producto_codigo";
                $txt_codigo="de barras";
            }else{
                $campo_tabla="producto_sku";
                $txt_codigo="SKU";
            }

            if($_SESSION['venta_tipo']=="normal"){
                $campo_precio="producto_precio_venta";
                $url_venta=SERVERURL."sale-new/";
            }else{
                $campo_precio="producto_precio_mayoreo";
                $url_venta=SERVERURL."sale-new/wholesale/";
            }

            /*== Recuperando codigo del producto ==*/
            $codigo=mainModel::limpiar_cadena($_POST['producto_codigo_add']);

            if($codigo==""){
                $alerta=[
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrió un error inesperado",
                    "Texto"=>"Debes de introducir el código $txt_codigo del producto.",
                    "Tipo"=>"error"
                ];
                echo json_encode($alerta);
                exit();
            }

            /*== Verificando integridad de los datos ==*/
            if(mainModel::verificar_datos("[a-zA-Z0-9- ]{1,70}",$codigo)){
                $alerta=[
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrió un error inesperado",
                    "Texto"=>"El código $txt_codigo no coincide con el formato solicitado",
                    "Tipo"=>"error"
                ];
                echo json_encode($alerta);
                exit();
            }

            /*== Comprobando producto en la DB ==*/
            $check_producto=mainModel::ejecutar_consulta_simple("SELECT * FROM producto WHERE $campo_tabla='$codigo'");
            if($check_producto->rowCount()<=0){
                $alerta=[
                    "Alerta"=>"venta",
                    "Titulo"=>"Ocurrió un error inesperado",
                    "Texto"=>"No hemos encontrado el producto con código $txt_codigo : '$codigo'.",
                    "Tipo"=>"error"
                ];
                echo json_encode($alerta);
                exit();
            }else{
                $campos=$check_producto->fetch();
            }
            $check_producto->closeCursor();
			$check_producto=mainModel::desconectar($check_producto);

            /*== Obteniendo datos de la empresa ==*/
            $check_empresa=mainModel::ejecutar_consulta_simple("SELECT * FROM empresa LIMIT 1");
            if($check_empresa->rowCount()<1){
                $alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos podido obtener algunos datos de los impuestos para agregar el producto.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
            }else{
                $datos_empresa=$check_empresa->fetch();
            }
            $check_empresa->closeCursor();
			$check_empresa=mainModel::desconectar($check_empresa);

            if($datos_empresa['empresa_impuesto_nombre']!=$_SESSION['venta_impuesto_nombre'] || $datos_empresa['empresa_impuesto_porcentaje']!=$_SESSION['venta_impuesto_porcentaje']){
                $alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"Hemos detectado un cambio en los datos de la empresa e impuestos, por favor recargue la página e intente nuevamente o verifique los datos de la empresa.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
            }

            /*== Codigo de producto ==*/
            $codigo=$campos['producto_codigo'];

            /*== Garantia de producto ==*/
            if($campos['producto_garantia_unidad']=="0" || $campos['producto_garantia_tiempo']=="N/A"){
                $producto_garantia="N/A";
            }else{
                $producto_garantia=$campos['producto_garantia_unidad']." ".$campos['producto_garantia_tiempo'];
            }



            if(empty($_SESSION['datos_producto_venta'][$codigo])){

                $detalle_cantidad=1;

                $stock_total=$campos['producto_stock_total']-$detalle_cantidad;

                if($stock_total<0){
                    $alerta=[
                        "Alerta"=>"simple",
                        "Titulo"=>"Ocurrió un error inesperado",
                        "Texto"=>"Lo sentimos, no hay existencias disponibles del producto seleccionado.",
                        "Tipo"=>"error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }

                $detalle_descuento_total=$campos[$campo_precio]*($campos['producto_descuento']/100);
                $detalle_descuento_total=number_format($detalle_descuento_total,MONEDA_DECIMALES,'.','');

                $precio_con_descuento=$campos[$campo_precio]-$detalle_descuento_total;
                $precio_con_descuento=number_format($precio_con_descuento,MONEDA_DECIMALES,'.','');

                $detalle_total=$detalle_cantidad*$precio_con_descuento;
                $detalle_total=number_format($detalle_total,MONEDA_DECIMALES,'.','');

                $detalle_subtotal=$detalle_total/(($datos_empresa['empresa_impuesto_porcentaje']/100)+1);
                $detalle_subtotal=number_format($detalle_subtotal,MONEDA_DECIMALES,'.','');

                $detalle_impuestos=$detalle_total-$detalle_subtotal;
                $detalle_impuestos=number_format($detalle_impuestos,MONEDA_DECIMALES,'.','');

                $detalle_costos=$campos['producto_precio_compra']*$detalle_cantidad;
                $detalle_costos=number_format($detalle_costos,MONEDA_DECIMALES,'.','');

                $detalle_utilidad=$detalle_total-$detalle_costos;
                $detalle_utilidad=number_format($detalle_utilidad,MONEDA_DECIMALES,'.','');

                $_SESSION['datos_producto_venta'][$codigo]=[
                    "tipo_precio"=>$_SESSION['venta_tipo'],
                    "producto_id"=>$campos['producto_id'],
					"producto_codigo"=>$campos['producto_codigo'],
					"producto_sku"=>$campos['producto_sku'],
					"producto_stock_total"=>$stock_total,
					"producto_stock_total_old"=>$campos['producto_stock_total'],
					"producto_stock_vendido"=>$campos['producto_stock_vendido'],
                    "producto_stock_vendido_old"=>$campos['producto_stock_vendido'],
                    "producto_garantia"=>$producto_garantia,
                    "venta_detalle_precio_compra"=>$campos['producto_precio_compra'],
                    "venta_detalle_precio_regular"=>$campos[$campo_precio],
                    "venta_detalle_precio_venta"=>$precio_con_descuento,
                    "venta_detalle_cantidad"=>1,
                    "venta_detalle_subtotal"=>$detalle_subtotal,
                    "venta_detalle_impuestos"=>$detalle_impuestos,
                    "venta_detalle_descuento_porcentaje"=>$campos['producto_descuento'],
                    "venta_detalle_descuento_total"=>$detalle_descuento_total,
                    "venta_detalle_total"=>$detalle_total,
                    "venta_detalle_costos"=>$detalle_costos,
                    "venta_detalle_utilidad"=>$detalle_utilidad,
                    "venta_detalle_descripcion"=>$campos['producto_nombre']
                ];
                
                $_SESSION['alerta_producto_agregado']="Se agrego <strong>".$campos['producto_nombre']."</strong> a la venta";
            }else{
                $detalle_cantidad=($_SESSION['datos_producto_venta'][$codigo]['venta_detalle_cantidad'])+1;

                $stock_total=$campos['producto_stock_total']-$detalle_cantidad;

                if($stock_total<0){
                    $alerta=[
                        "Alerta"=>"simple",
                        "Titulo"=>"Ocurrió un error inesperado",
                        "Texto"=>"Lo sentimos, no hay existencias disponibles del producto seleccionado.",
                        "Tipo"=>"error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }

                $detalle_descuento_total=$campos[$campo_precio]*($campos['producto_descuento']/100);
                $detalle_descuento_total=number_format($detalle_descuento_total,MONEDA_DECIMALES,'.','');

                $precio_con_descuento=$campos[$campo_precio]-$detalle_descuento_total;
                $precio_con_descuento=number_format($precio_con_descuento,MONEDA_DECIMALES,'.','');

                $detalle_total=$detalle_cantidad*$precio_con_descuento;
                $detalle_total=number_format($detalle_total,MONEDA_DECIMALES,'.','');

                $detalle_subtotal=$detalle_total/(($datos_empresa['empresa_impuesto_porcentaje']/100)+1);
                $detalle_subtotal=number_format($detalle_subtotal,MONEDA_DECIMALES,'.','');

                $detalle_impuestos=$detalle_total-$detalle_subtotal;
                $detalle_impuestos=number_format($detalle_impuestos,MONEDA_DECIMALES,'.','');

                $detalle_costos=$campos['producto_precio_compra']*$detalle_cantidad;
                $detalle_costos=number_format($detalle_costos,MONEDA_DECIMALES,'.','');

                $detalle_utilidad=$detalle_total-$detalle_costos;
                $detalle_utilidad=number_format($detalle_utilidad,MONEDA_DECIMALES,'.','');

                $_SESSION['datos_producto_venta'][$codigo]=[
                    "tipo_precio"=>$_SESSION['venta_tipo'],
                    "producto_id"=>$campos['producto_id'],
					"producto_codigo"=>$campos['producto_codigo'],
					"producto_sku"=>$campos['producto_sku'],
					"producto_stock_total"=>$stock_total,
					"producto_stock_total_old"=>$campos['producto_stock_total'],
					"producto_stock_vendido"=>$campos['producto_stock_vendido'],
                    "producto_stock_vendido_old"=>$campos['producto_stock_vendido'],
                    "producto_garantia"=>$producto_garantia,
                    "venta_detalle_precio_compra"=>$campos['producto_precio_compra'],
                    "venta_detalle_precio_regular"=>$campos[$campo_precio],
                    "venta_detalle_precio_venta"=>$precio_con_descuento,
                    "venta_detalle_cantidad"=>$detalle_cantidad,
                    "venta_detalle_subtotal"=>$detalle_subtotal,
                    "venta_detalle_impuestos"=>$detalle_impuestos,
                    "venta_detalle_descuento_porcentaje"=>$campos['producto_descuento'],
                    "venta_detalle_descuento_total"=>$detalle_descuento_total,
                    "venta_detalle_total"=>$detalle_total,
                    "venta_detalle_costos"=>$detalle_costos,
                    "venta_detalle_utilidad"=>$detalle_utilidad,
                    "venta_detalle_descripcion"=>$campos['producto_nombre']
                ];

                $_SESSION['alerta_producto_agregado']="Se agrego +1 <strong>".$campos['producto_nombre']."</strong> a la venta. Total en carrito: <strong>$detalle_cantidad</strong>";
            }

            $alerta=[
                "Alerta"=>"redireccionar",
                "URL"=>$url_venta
            ];

            echo json_encode($alerta);
        } /*-- Fin controlador --*/


        /*---------- Controlador eliminar producto a venta ----------*/
        public function eliminar_producto_carrito_controlador(){        

            /*== Recuperando codigo del producto ==*/
            $codigo=mainModel::limpiar_cadena($_POST['producto_codigo_del']);

            unset($_SESSION['datos_producto_venta'][$codigo]);

            if(empty($_SESSION['datos_producto_venta'][$codigo])){
				$alerta=[
					"Alerta"=>"recargar",
					"Titulo"=>"¡Producto removido!",
					"Texto"=>"El producto se ha removido de la venta.",
					"Tipo"=>"success"
				];
			}else{
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos podido remover el producto, por favor intente nuevamente.",
					"Tipo"=>"error"
				];
            }
            echo json_encode($alerta);
        } /*-- Fin controlador --*/


        /*---------- Controlador actualizar producto a venta ----------*/
        public function actualizar_producto_carrito_controlador(){    

            if($_SESSION['venta_tipo']=="normal"){
                $campo_precio="producto_precio_venta";
                $url_venta=SERVERURL."sale-new/";
            }else{
                $campo_precio="producto_precio_mayoreo";
                $url_venta=SERVERURL."sale-new/wholesale/";
            }

            /*== Recuperando codigo & cantidad del producto ==*/
            $codigo=mainModel::limpiar_cadena($_POST['producto_codigo_up']);
            $cantidad=mainModel::limpiar_cadena($_POST['producto_cantidad_up']);

            /*== comprobando campos vacios ==*/
            if($codigo=="" || $cantidad==""){
                $alerta=[
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrió un error inesperado",
                    "Texto"=>"No podemos actualizar la cantidad de productos debido a que faltan algunos parámetros de configuración.",
                    "Tipo"=>"error"
                ];
                echo json_encode($alerta);
                exit();
            }

            /*== comprobando cantidad de productos ==*/
            if($cantidad<=0){
                $alerta=[
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrió un error inesperado",
                    "Texto"=>"Debes de introducir una cantidad mayor a 0.",
                    "Tipo"=>"error"
                ];
                echo json_encode($alerta);
                exit();
            }

            /*== Comprobando producto en la DB ==*/
            $check_producto=mainModel::ejecutar_consulta_simple("SELECT * FROM producto WHERE producto_codigo='$codigo'");
            if($check_producto->rowCount()<=0){
                $alerta=[
                    "Alerta"=>"venta",
                    "Titulo"=>"Ocurrió un error inesperado",
                    "Texto"=>"No hemos encontrado el producto con código de barras : '$codigo'.",
                    "Tipo"=>"error"
                ];
                echo json_encode($alerta);
                exit();
            }else{
                $campos=$check_producto->fetch();
            }
            $check_producto->closeCursor();
			$check_producto=mainModel::desconectar($check_producto);

            /*== Obteniendo datos de la empresa ==*/
            $check_empresa=mainModel::ejecutar_consulta_simple("SELECT * FROM empresa LIMIT 1");
            if($check_empresa->rowCount()<1){
                $alerta=[
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrió un error inesperado",
                    "Texto"=>"No hemos podido obtener algunos datos de los impuestos para agregar el producto.",
                    "Tipo"=>"error"
                ];
                echo json_encode($alerta);
                exit();
            }else{
                $datos_empresa=$check_empresa->fetch();
            }
            $check_empresa->closeCursor();
			$check_empresa=mainModel::desconectar($check_empresa);

            if($datos_empresa['empresa_impuesto_nombre']!=$_SESSION['venta_impuesto_nombre'] || $datos_empresa['empresa_impuesto_porcentaje']!=$_SESSION['venta_impuesto_porcentaje']){
                $alerta=[
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrió un error inesperado",
                    "Texto"=>"Hemos detectado un cambio en los datos de la empresa e impuestos, por favor recargue la página e intente nuevamente o verifique los datos de la empresa.",
                    "Tipo"=>"error"
                ];
                echo json_encode($alerta);
                exit();
            }

            /*== comprobando producto en carrito ==*/
            if(!empty($_SESSION['datos_producto_venta'][$codigo])){

                if($_SESSION['datos_producto_venta'][$codigo]["venta_detalle_cantidad"]==$cantidad){
                    $alerta=[
                        "Alerta"=>"simple",
                        "Titulo"=>"Ocurrió un error inesperado",
                        "Texto"=>"No has modificado la cantidad de productos",
                        "Tipo"=>"error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }

                if($cantidad>$_SESSION['datos_producto_venta'][$codigo]["venta_detalle_cantidad"]){
                    $diferencia_productos="agrego +".($cantidad-$_SESSION['datos_producto_venta'][$codigo]["venta_detalle_cantidad"]);
                }else{
                    $diferencia_productos="quito -".($_SESSION['datos_producto_venta'][$codigo]["venta_detalle_cantidad"]-$cantidad);
                }
                

                $detalle_cantidad=$cantidad;

                $stock_total=$campos['producto_stock_total']-$detalle_cantidad;

                if($stock_total<0){
                    $alerta=[
                        "Alerta"=>"simple",
                        "Titulo"=>"Ocurrió un error inesperado",
                        "Texto"=>"Lo sentimos, no hay existencias suficientes del producto seleccionado. Existencias disponibles: ".($stock_total+$detalle_cantidad)."",
                        "Tipo"=>"error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }

                $detalle_descuento_total=$campos[$campo_precio]*($campos['producto_descuento']/100);
                $detalle_descuento_total=number_format($detalle_descuento_total,MONEDA_DECIMALES,'.','');

                $precio_con_descuento=$campos[$campo_precio]-$detalle_descuento_total;
                $precio_con_descuento=number_format($precio_con_descuento,MONEDA_DECIMALES,'.','');

                $detalle_total=$detalle_cantidad*$precio_con_descuento;
                $detalle_total=number_format($detalle_total,MONEDA_DECIMALES,'.','');

                $detalle_subtotal=$detalle_total/(($datos_empresa['empresa_impuesto_porcentaje']/100)+1);
                $detalle_subtotal=number_format($detalle_subtotal,MONEDA_DECIMALES,'.','');

                $detalle_impuestos=$detalle_total-$detalle_subtotal;
                $detalle_impuestos=number_format($detalle_impuestos,MONEDA_DECIMALES,'.','');

                $detalle_costos=$campos['producto_precio_compra']*$detalle_cantidad;
                $detalle_costos=number_format($detalle_costos,MONEDA_DECIMALES,'.','');

                $detalle_utilidad=$detalle_total-$detalle_costos;
                $detalle_utilidad=number_format($detalle_utilidad,MONEDA_DECIMALES,'.','');

                /*== Garantia de producto ==*/
                if($campos['producto_garantia_unidad']=="0" || $campos['producto_garantia_tiempo']=="N/A"){
                    $producto_garantia="N/A";
                }else{
                    $producto_garantia=$campos['producto_garantia_unidad']." ".$campos['producto_garantia_tiempo'];
                }

                $_SESSION['datos_producto_venta'][$codigo]=[
                    "tipo_precio"=>$_SESSION['venta_tipo'],
                    "producto_id"=>$campos['producto_id'],
					"producto_codigo"=>$campos['producto_codigo'],
					"producto_sku"=>$campos['producto_sku'],
					"producto_stock_total"=>$stock_total,
					"producto_stock_total_old"=>$campos['producto_stock_total'],
					"producto_stock_vendido"=>$campos['producto_stock_vendido'],
                    "producto_stock_vendido_old"=>$campos['producto_stock_vendido'],
                    "producto_garantia"=>$producto_garantia,
                    "venta_detalle_precio_compra"=>$campos['producto_precio_compra'],
                    "venta_detalle_precio_regular"=>$campos[$campo_precio],
                    "venta_detalle_precio_venta"=>$precio_con_descuento,
                    "venta_detalle_cantidad"=>$detalle_cantidad,
                    "venta_detalle_subtotal"=>$detalle_subtotal,
                    "venta_detalle_impuestos"=>$detalle_impuestos,
                    "venta_detalle_descuento_porcentaje"=>$campos['producto_descuento'],
                    "venta_detalle_descuento_total"=>$detalle_descuento_total,
                    "venta_detalle_total"=>$detalle_total,
                    "venta_detalle_costos"=>$detalle_costos,
                    "venta_detalle_utilidad"=>$detalle_utilidad,
                    "venta_detalle_descripcion"=>$campos['producto_nombre']
                ];

                $_SESSION['alerta_producto_agregado']="Se $diferencia_productos <strong>".$campos['producto_nombre']."</strong> a la venta. Total en carrito <strong>$detalle_cantidad</strong>";

                $alerta=[
                    "Alerta"=>"redireccionar",
                    "URL"=>$url_venta
                ];
    
                echo json_encode($alerta);
                exit(); 
            }else{
                $alerta=[
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrió un error inesperado",
                    "Texto"=>"No hemos encontrado el producto que desea actualizar en el carrito.",
                    "Tipo"=>"error"
                ];
                echo json_encode($alerta);
                exit();
            }
        } /*-- Fin controlador --*/


        /*---------- Controlador buscar cliente ----------*/
        public function buscar_cliente_venta_controlador(){

            /*== Recuperando termino de busqueda ==*/
			$cliente=mainModel::limpiar_cadena($_POST['buscar_cliente']);

			/*== Comprobando que no este vacio el campo ==*/
			if($cliente==""){
				return '<div class="alert alert-warning" role="alert">
					<p class="text-center mb-0">
						<i class="fas fa-exclamation-triangle fa-2x"></i><br>
						Debes de introducir el Numero de documento, Nombre, Apellido o Teléfono del cliente
					</p>
				</div>';
				exit();
            }
            
            /*== Seleccionando clientes en la DB ==*/
            $datos_cliente=mainModel::ejecutar_consulta_simple("SELECT * FROM cliente WHERE (cliente_id!='1') AND (cliente_numero_documento LIKE '%$cliente%' OR cliente_nombre LIKE '%$cliente%' OR cliente_apellido LIKE '%$cliente%' OR cliente_telefono LIKE '%$cliente%') ORDER BY cliente_nombre ASC");
            
            if($datos_cliente->rowCount()>=1){

				$datos_cliente=$datos_cliente->fetchAll();

				$tabla='<div class="table-responsive" ><table class="table table-hover table-bordered table-sm"><tbody>';

				foreach($datos_cliente as $rows){
					$tabla.='
					<tr class="text-center">
                        <td>'.$rows['cliente_nombre'].' '.$rows['cliente_apellido'].' ('.$rows['cliente_tipo_documento'].': '.$rows['cliente_numero_documento'].')</td>
                        <td>
                            <button type="button" class="btn btn-primary" onclick="agregar_cliente('.$rows['cliente_id'].')"><i class="fas fa-user-plus"></i></button>
                        </td>
                    </tr>
                    ';
				}

				$tabla.='</tbody></table></div>';
				return $tabla;
			}else{
				return '<div class="alert alert-warning" role="alert">
					<p class="text-center mb-0">
						<i class="fas fa-exclamation-triangle fa-2x"></i><br>
						No hemos encontrado ningún cliente en el sistema que coincida con <strong>“'.$cliente.'”</strong>
					</p>
				</div>';
				exit();
			}
        } /*-- Fin controlador --*/


       

        /*---------- Controlador eliminar cliente ----------*/
        public function eliminar_cliente_venta_controlador(){

			unset($_SESSION['datos_cliente_venta']);

			if(empty($_SESSION['datos_cliente_venta'])){
				$alerta=[
					"Alerta"=>"recargar",
					"Titulo"=>"¡Cliente removido!",
					"Texto"=>"Los datos del cliente se han removido.",
					"Tipo"=>"success"
				];
			}else{
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos podido remover el cliente, por favor intente nuevamente.",
					"Tipo"=>"error"
				];
			}
			echo json_encode($alerta);
        } /*-- Fin controlador --*/


        /*---------- Controlador buscar codigo de producto ----------*/
        public function buscar_codigo_venta_controlador(){

            /*== Recuperando codigo de busqueda ==*/
			$producto=mainModel::limpiar_cadena($_POST['buscar_codigo']);

			/*== Comprobando que no este vacio el campo ==*/
			if($producto==""){
				return '<div class="alert alert-warning" role="alert">
					<p class="text-center mb-0">
						<i class="fas fa-exclamation-triangle fa-2x"></i><br>
						Debes de introducir el Noddddmbre, Marca o Modelo del producto
					</p>
				</div>';
				exit();
            }

            /*== Seleccionando productos en la DB ==*/
            $datos_productos=mainModel::ejecutar_consulta_simple("SELECT * FROM producto WHERE (producto_nombre LIKE '%$producto%' OR producto_marca LIKE '%$producto%' OR producto_modelo LIKE '%$producto%') ORDER BY producto_nombre ASC");
            
            if($datos_productos->rowCount()>=1){

                if($_SESSION['lector_codigo_svi']=="Barras"){
                    $campo_codigo="producto_codigo";
                }else{
                    $campo_codigo="producto_sku";
                }

				$datos_productos=$datos_productos->fetchAll();

				$tabla='<div class="table-responsive" ><table class="table table-hover table-bordered table-sm"><tbody>';

				foreach($datos_productos as $rows){
					$tabla.='
					<tr class="text-center">
                        <td>'.$rows['producto_nombre'].'</td>
                        <td>
                            <button type="button" class="btn btn-primary" onclick="agregar_codigo(\''.$rows[$campo_codigo].'\')"><i class="fas fa-plus-circle"></i></button>
                        </td>
                    </tr>
                    ';
				}

				$tabla.='</tbody></table></div>';
				return $tabla;
			}else{
				return '<div class="alert alert-warning" role="alert">
					<p class="text-center mb-0">
						<i class="fas fa-exclamation-triangle fa-2x"></i><br>
						No hemos encontrado ningún producto en el sistema que coincida con <strong>“'.$producto.'”</strong>
					</p>
				</div>';
				exit();
			}
        } /*-- Fin controlador --*/


        /*---------- Controlador aplicar descuento a venta ----------*/
        public function aplicar_descuento_venta_controlador(){    

            /*== Recuperando descuento ==*/
            $descuento=mainModel::limpiar_cadena($_POST['venta_descuento_add']);
            
            /*== Comprobando que no este vacio el campo y que sea mayor a 0 ==*/
			if($descuento=="" || $descuento<=0){
                $alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"Debe de ingresar una cantidad mayor a 0.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
            }

            /*== Comprobando formato de descuento ==*/
            if(mainModel::verificar_datos("[0-9]{1,2}",$descuento)){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El descuento no coincide con el formato solicitado",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
            }
       
            /*== Comprobando que se hayan agregado productos y que la venta sea mayor a 0 ==*/
            if($_SESSION['venta_total']<=0 || (!isset($_SESSION['datos_producto_venta']) && count($_SESSION['datos_producto_venta'])<=0)){
                $alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No podemos aplicar el descuento ya que no ha agregado productos a esta venta.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
            }

            $_SESSION['venta_descuento']=$descuento;

            if($_SESSION['venta_descuento']==$descuento){
                $alerta=[
					"Alerta"=>"recargar",
					"Titulo"=>"¡Descuento aplicado!",
					"Texto"=>"El descuento ha sido aplicado con éxito en la venta.",
					"Tipo"=>"success"
				];
            }else{
                $alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No podemos aplicar el descuento debido a un error, por favor intente nuevamente.",
					"Tipo"=>"error"
				];
            }
            echo json_encode($alerta);
        } /*-- Fin controlador --*/

        
        /*---------- Controlador paginador ventas ----------*/
		public function paginador_venta_controlador($pagina,$registros,$url,$fecha_inicio,$fecha_final){
           
            $registros=mainModel::limpiar_cadena($registros);
            $codigo_grupo = mainModel::limpiar_cadena($pagina);
            $url=mainModel::limpiar_cadena($url);
            $url=SERVERURL.$url."/";
    
            $tipo=('Busqueda');
            $fecha_inicio=mainModel::limpiar_cadena($fecha_inicio);
            $fecha_final=mainModel::limpiar_cadena($fecha_final);
            $tabla="";
    
            $pagina = (isset($pagina) && $pagina>0) ? (int) $pagina : 1;
            $inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;
            
           
            
            $campos_tablas="catalogo.catalogo_id,catalogo.titulo_game,catalogo.familia_game,catalogo.id_juego,catalogo.payout_game,catalogo.denominacion_game,catalogo.codigo_grupo";

            if($tipo=="Busqueda" && $fecha_inicio!="" && $fecha_final!=""){
               
            }else{
                $consulta = "SELECT  SQL_CALC_FOUND_ROWS $campos_tablas FROM catalogo where catalogo.codigo_grupo = '$codigo_grupo' ORDER BY catalogo.catalogo_id";
               
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
                            <th>Nombre matemático</th>
                            <th>Game Skin Name</th>
                            <th>Payout Porcentaje</th>
                            <th>Denominaciones</th>
                            
                            <th>Opciones</th>
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
                            <td>'.$rows['id_juego']."_".$rows['titulo_game'].'</td>
                            <td>'.$rows['familia_game'].'</td>
                            <td>'.$rows['payout_game'].'</td>
                            <td>'.$rows['denominacion_game'].'</td>
                            
                            <td>
                                <button type="button" class="btn btn-info" data-toggle="popover" data-trigger="hover" title="'.$rows['familia_game'].'" data-content="'.$rows['familia_game'].'" >
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
    
            
            return $tabla;
        }
        
        /*-- Fin controlador --*/
        public function paginador_controlador($pagina,$registros,$url,$tipo)
        {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $url = mainModel::limpiar_cadena($url);
            $url = SERVERURL . $url . "/";
            $tipo = mainModel::limpiar_cadena($tipo);
        
            $tabla = "";
        
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;
        
            if ($tipo == "Busqueda") {
                if (mainModel::verificar_fecha($registros)) {
                    return '
                        <div class="alert alert-danger text-center" role="alert">
                            <p><i class="fas fa-exclamation-triangle fa-5x"></i></p>
                            <h4 class="alert-heading">¡Ocurrió un error inesperado!</h4>
                            <p class="mb-0">Lo sentimos, no podemos realizar la búsqueda ya que al parecer ha ingresado una fecha incorrecta.</p>
                        </div>
                    ';
                    exit();
                }
            }
        
            $campos_tablas = "catalogo.catalogo_id,catalogo.titulo_game,catalogo.id_juego,catalogo.payout_game,catalogo.denominacion_game,catalogo.codigo_grupo,catalogo.usuario_id,catalogo.rol_id,rol.rol_numero,rol.rol_nombre,usuario.usuario_nombre,usuario.usuario_apellido,game_group.group_codigo,game_group.group_name,game_group.group_theme,game_group.usuario_id,game_group.rol_id";
        
            if ($tipo == "Busqueda") {
            } else {
                $consulta = "SELECT SQL_CALC_FOUND_ROWS * FROM game_group ORDER BY id_group DESC LIMIT $inicio, $registros";
            }
            $conexion = mainModel::conectar();
        
            $datos = $conexion->query($consulta);
        
            $datos = $datos->fetchAll();
        
            $total = $conexion->query("SELECT FOUND_ROWS()");
            $total = (int) $total->fetchColumn();
        
            $Npaginas =ceil($total/$registros);
        
            $tabla .= '<div class="table-responsive">';
            // $tabla .= '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">';
            $tabla .= '<table class="table table-hover table-bordered table-sm">';
            $tabla .= '<thead class="bg-info">';
            $tabla .= '<tr class="text-center">';
            $tabla .= '<th>#</th>';
            $tabla .= '<th>CODIGO GRUPO <i class="fas fa-code"></i></th>';
            $tabla .= '<th>NOMBRE DEL GRUPO <i class="fas fa-users"></i></th>';
            $tabla .= '<th>TEMA <i class="fas fa-gamepad"></i></th>';
            $tabla .= '<th>OPCIONES <i class="fas fa-cogs"></i></th>';
            $tabla .= '<th>PRINT <i class="fas fa-print"></i></th>';
            $tabla .= '</tr>';
            $tabla .= '</thead>';
            $tabla .= '<tbody>';
        
            if ($total >= 1 && $pagina <= $Npaginas) {
                $contador = $inicio + 1;
                $pag_inicio = $inicio + 1;
                foreach ($datos as $rows) {
                    $tabla .= '
                            <tr class="text-center">
                                <td>' . $contador . '</td>
                                <td>' . $rows['group_codigo'] . '</td>
                                <td>' . $rows['group_name'] . '</td>
                                <td>' . $rows['group_theme'] . '</td>
                                <td>
                                <form class="FormularioAjax" action="'.SERVERURL.'ajax/ventaAjax.php" method="POST" data-form="shop" autocomplete="off">
                                <input type="hidden" name="venta_codigo_del" value="'.mainModel::encryption($rows['group_codigo']).'">
                                <input type="hidden" name="modulo_venta" value="eliminar_venta">
                                <button type="submit" class="btn btn-warning" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Remover_'.$rows['group_name'].' " >
                                    <i class="far fa-trash-alt"></i>
                                </button>
                            </form>
                               </td>
                                <td>
                                <label class="content-input">
                                <input type="checkbox" name="grupos[]" value="' . $rows['group_codigo'] . '"><i></i>
                                 </label>
                                </td>
                            </tr>
                        ';
                    $contador++;
                }
                $pag_final = $contador - 1;
            } else {
                if ($total >= 1) {
                    $tabla .= '
                            <tr class="text-center" >
                                <td colspan="9">
                                    <a href="' . $url . '" class="btn btn-raised btn-primary btn-sm">
                                        Haga clic acá para recargar el listado
                                    </a>
                                </td>
                            </tr>
                        ';
                } else {
                    $tabla .= '
                            <tr class="text-center" >
                                <td colspan="9">
                                    No hay registros en el sistema
                                </td>
                            </tr>
                        ';
                }
            }
        
            $tabla .= '</tbody></table>';
            if ($total > 0 && $pagina <= $Npaginas) {
                $tabla .= '<p class="text-right">Mostrando movimientos <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>';
            }
        
            $tabla .= '<button type="button" class="btn btn-raised btn-secondary" onclick="printSelectedGroups()"><i class="fas fa-file-excel"></i> Generar XML</button>
            <button type="button" class="btn btn-raised  btn-success" ><i class="fas fa-table"></i> Generar Excel</button>
            ';
            $tabla .= '</form></div>';
        
            if ($total >= 1 && $pagina <= $Npaginas) {
                $tabla .= mainModel::paginador_tablas($pagina, $Npaginas, $url, 7);
            }
        
            return $tabla;
        }

// Manejar el envío del
/*-- Fin controlador --*/
    //final de controlador
              /*---------- Controlador registrar venta ----------*/
              public function registrar_venta_controlador(){    

                $codigo_grupo=mainModel::limpiar_cadena($_POST['codigo']);
    
                /*== Comprobando integridad de los datos ==*/
              
                /*== Comprobando cliente en la DB ==*/
                $check_cliente=mainModel::ejecutar_consulta_simple("SELECT group_codigo FROM game_group WHERE group_codigo='".$codigo_grupo."'");
                if($check_cliente->rowCount()<=0){
                    $alerta=[
                        "Alerta"=>"simple",
                        "Titulo"=>"Ocurrió un error inesperado",
                        "Texto"=>"No hemos encontrado el grupo en el sistema.",
                        "Tipo"=>"error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }
                $check_cliente->closeCursor();
                $check_cliente=mainModel::desconectar($check_cliente);

                $check_grupo=mainModel::ejecutar_consulta_simple("SELECT codigo_grupo FROM catalogo WHERE codigo_grupo='".$codigo_grupo."'");
                if($check_grupo->rowCount()<=0){
                    $alerta=[
                        "Alerta"=>"simple",
                        "Titulo"=>"Ocurrió un error inesperado",
                        "Texto"=>"No has registrado Juegos en el Grupo.",
                        "Tipo"=>"error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }
                $check_grupo->closeCursor();
                $check_grupo=mainModel::desconectar($check_grupo);
    
    
    
                /*== Vaciando variables de sesion ==*/
                $_SESSION['grupo_codigo']=$codigo_grupo;

                $alerta=[
                    "Alerta"=>"cambiar",
                    "Titulo"=>"¡Juegos Registrados!",
                    "Texto"=>"Los juegos fueron añadidos correctamente al sistema",
                    "Tipo"=>"success",
                    "URL"=>SERVERURL."sale-search-date/"
                ];
                echo json_encode($alerta);
            } /*-- Fin controlador --*/
    

        /*---------- Controlador agregar pagos de ventas ----------*/
        public function agregar_pago_venta_controlador(){
            
            /*== Recuperando el codigo de la venta y monto ==*/
            $venta_codigo=mainModel::limpiar_cadena($_POST['pago_codigo_reg']);
            $pago_monto=mainModel::limpiar_cadena($_POST['pago_monto_reg']);

            /*== Comprobando venta ==*/
			$check_venta=mainModel::ejecutar_consulta_simple("SELECT * FROM venta WHERE venta_codigo='$venta_codigo' AND venta_estado='Pendiente' AND venta_tipo='Credito'");
			if($check_venta->rowCount()<=0){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos encontrado en la base de datos la venta seleccionada para realizar el pago. También es posible que la venta ya haya sido cancelada o no es una venta al crédito por lo tanto no podemos agregar pagos",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
            }else{
                $datos_venta=$check_venta->fetch();
            }
            $check_venta->closeCursor();
            $check_venta=mainModel::desconectar($check_venta);
            
            /*== Comprobando pago ==*/
            if($pago_monto=="" || $pago_monto<=0){
                $alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"Debes de introducir una cantidad (monto) que sea mayor a 0 para poder realizar el pago.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
            }

            /*== Comprobando integridad de los datos ==*/
            if(mainModel::verificar_datos("[0-9.]{1,25}",$pago_monto)){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El monto no coincide con el formato solicitado",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
            }

            /*== Comprobando caja en la DB ==*/
            $check_caja=mainModel::ejecutar_consulta_simple("SELECT * FROM caja WHERE caja_id='".$_SESSION['caja_svi']."' AND caja_estado='Habilitada'");
			if($check_caja->rowCount()<=0){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"La caja se encuentra deshabilitada o no está registrada en el sistema.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
            }else{
                $datos_caja=$check_caja->fetch();
            }
            $check_caja->closeCursor();
            $check_caja=mainModel::desconectar($check_caja);

            /*== Calculando total pendiente ==*/
            $venta_pendiente=$datos_venta['venta_total_final']-$datos_venta['venta_pagado'];
            $venta_pendiente=number_format($venta_pendiente,MONEDA_DECIMALES,'.','');

            /*== Calculando el cambio ==*/
            if($pago_monto<$venta_pendiente){
                $venta_estado="Pendiente";
                $venta_cambio=0.00;
                $venta_cambio=number_format($venta_cambio,MONEDA_DECIMALES,'.','');
            }else{
                $venta_estado="Cancelado";
                $venta_cambio=$pago_monto-$venta_pendiente;
                $venta_cambio=number_format($venta_cambio,MONEDA_DECIMALES,'.','');
            }

            /*== Calculando total en caja ==*/
            $movimiento_cantidad=$pago_monto-$venta_cambio;
            $movimiento_cantidad=number_format($movimiento_cantidad,MONEDA_DECIMALES,'.','');

            $total_caja=$datos_caja['caja_efectivo']+$movimiento_cantidad;
            $total_caja=number_format($total_caja,MONEDA_DECIMALES,'.','');

            /*== Calculando total pagado de la venta ==*/
            $venta_pagado=($pago_monto+$datos_venta['venta_pagado'])-$venta_cambio;
            $venta_pagado=number_format($venta_pagado,MONEDA_DECIMALES,'.','');

            /*== Generando fecha y hora ==*/
            $pago_fecha=date("Y-m-d");
            $pago_hora=date("h:i a");

            /*== Preparando datos para enviarlos al modelo ==*/
            $datos_pago=[
                "pago_fecha"=>[
                    "campo_marcador"=>":Fecha",
                    "campo_valor"=>$pago_fecha
                ],
                "pago_monto"=>[
                    "campo_marcador"=>":Monto",
                    "campo_valor"=>$movimiento_cantidad
                ],
                "venta_codigo"=>[
                    "campo_marcador"=>":Codigo",
                    "campo_valor"=>$venta_codigo
                ],
                "usuario_id"=>[
                    "campo_marcador"=>":Usuario",
                    "campo_valor"=>$_SESSION['id_svi']
                ],
                "caja_id"=>[
                    "campo_marcador"=>":Caja",
                    "campo_valor"=>$_SESSION['caja_svi']
                ]
            ];

            $agregar_pago=mainModel::guardar_datos("pago",$datos_pago);

            if($agregar_pago->rowCount()<1){
                $alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos podido agregar el pago, por favor intente nuevamente.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
            }
            $agregar_pago->closeCursor();
            $agregar_pago=mainModel::desconectar($agregar_pago);

            /*== Preparando datos para enviarlos al modelo ==*/
            $datos_venta=[
                "venta_pagado"=>[
                    "campo_marcador"=>":Pagado",
                    "campo_valor"=>$venta_pagado
                ],
                "venta_estado"=>[
                    "campo_marcador"=>":Estado",
                    "campo_valor"=>$venta_estado
                ]
            ];

            $condicion=[
                "condicion_campo"=>"venta_codigo",
                "condicion_marcador"=>":Codigo",
                "condicion_valor"=>$venta_codigo
            ];

            /*== Reestableciendo DB debido a errores ==*/
            if(!mainModel::actualizar_datos("venta",$datos_venta,$condicion)){
                
                /*== Eliminando pago ==*/
                $check_pago=mainModel::ejecutar_consulta_simple("SELECT pago_id FROM pago WHERE pago_fecha='$pago_fecha' AND venta_codigo='$venta_codigo' AND usuario_id='".$_SESSION['id_svi']."' ORDER BY pago_id DESC LIMIT 1");
                $datos_pago=$check_pago->fetch();

                mainModel::eliminar_registro("pago","pago_id",$datos_pago['pago_id']);

                $check_pago->closeCursor();
                $check_pago=mainModel::desconectar($check_pago);

                $alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos podido actualizar algunos datos de la venta para poder agregar el pago.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
            }

            /*== Agregando movimiento de caja ==*/
            $correlativo=mainModel::ejecutar_consulta_simple("SELECT movimiento_id FROM movimiento");
			$correlativo=($correlativo->rowCount())+1;

            $codigo_movimiento=mainModel::generar_codigo_aleatorio(8,$correlativo);

            /*== Preparando datos para enviarlos al modelo ==*/
            $datos_movimiento=[
                "movimiento_codigo"=>[
                    "campo_marcador"=>":Codigo",
                    "campo_valor"=>$codigo_movimiento
                ],
                "movimiento_fecha"=>[
                    "campo_marcador"=>":Fecha",
                    "campo_valor"=>$pago_fecha
                ],
                "movimiento_hora"=>[
                    "campo_marcador"=>":Hora",
                    "campo_valor"=>$pago_hora
                ],
                "movimiento_tipo"=>[
                    "campo_marcador"=>":Tipo",
                    "campo_valor"=>"Entrada de efectivo"
                ],
                "movimiento_motivo"=>[
                    "campo_marcador"=>":Motivo",
                    "campo_valor"=>"Pago de venta al crédito"
                ],
                "movimiento_saldo_anterior"=>[
                    "campo_marcador"=>":Anterior",
                    "campo_valor"=>$datos_caja['caja_efectivo']
                ],
                "movimiento_cantidad"=>[
                    "campo_marcador"=>":Cantidad",
                    "campo_valor"=>$movimiento_cantidad
                ],
                "movimiento_saldo_actual"=>[
                    "campo_marcador"=>":Actual",
                    "campo_valor"=>$total_caja
                ],
                "usuario_id"=>[
                    "campo_marcador"=>":Usuario",
                    "campo_valor"=>$_SESSION['id_svi']
                ],
                "caja_id"=>[
                    "campo_marcador"=>":Caja",
                    "campo_valor"=>$_SESSION['caja_svi']
                ]
            ];

            $agregar_movimiento=mainModel::guardar_datos("movimiento",$datos_movimiento);

            /*== Reestableciendo DB debido a errores ==*/
            if($agregar_movimiento->rowCount()<1){

                /*== Actualizando venta ==*/
                $datos_venta=[
                    "venta_pagado"=>[
                        "campo_marcador"=>":Pagado",
                        "campo_valor"=>$datos_venta['venta_pagado']
                    ],
                    "venta_estado"=>[
                        "campo_marcador"=>":Estado",
                        "campo_valor"=>$datos_venta['venta_estado']
                    ]
                ];

                $condicion=[
                    "condicion_campo"=>"venta_codigo",
                    "condicion_marcador"=>":Codigo",
                    "condicion_valor"=>$venta_codigo
                ];

                mainModel::actualizar_datos("venta",$datos_venta,$condicion);

                /*== Eliminando pago ==*/
                $check_pago=mainModel::ejecutar_consulta_simple("SELECT pago_id FROM pago WHERE pago_fecha='$pago_fecha' AND venta_codigo='$venta_codigo' AND usuario_id='".$_SESSION['id_svi']."' ORDER BY pago_id DESC LIMIT 1");
                $datos_pago=$check_pago->fetch();

                mainModel::eliminar_registro("pago","pago_id",$datos_pago['pago_id']);

                $check_pago->closeCursor();
                $check_pago=mainModel::desconectar($check_pago);

                $alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos podido actualizar algunos datos de la caja para poder agregar el pago.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
                exit();
            }
            $agregar_movimiento->closeCursor();
            $agregar_movimiento=mainModel::desconectar($agregar_movimiento);

            /*== Actualizando efectivo en caja ==*/
            $datos_caja_up=[
                "caja_efectivo"=>[
                    "campo_marcador"=>":Efectivo",
                    "campo_valor"=>$total_caja
                ]
            ];

            $condicion_caja=[
                "condicion_campo"=>"caja_id",
                "condicion_marcador"=>":ID",
                "condicion_valor"=>$_SESSION['caja_svi']
            ];

            /*== Reestableciendo DB debido a errores ==*/
            if(!mainModel::actualizar_datos("caja",$datos_caja_up,$condicion_caja)){
                
                /*== Eliminando movimiento ==*/
                mainModel::eliminar_registro("movimiento","movimiento_codigo",$codigo_movimiento);

                /*== Actualizando venta ==*/
                $datos_venta=[
                    "venta_pagado"=>[
                        "campo_marcador"=>":Pagado",
                        "campo_valor"=>$datos_venta['venta_pagado']
                    ],
                    "venta_estado"=>[
                        "campo_marcador"=>":Estado",
                        "campo_valor"=>$datos_venta['venta_estado']
                    ]
                ];

                $condicion=[
                    "condicion_campo"=>"venta_codigo",
                    "condicion_marcador"=>":Codigo",
                    "condicion_valor"=>$venta_codigo
                ];

                mainModel::actualizar_datos("venta",$datos_venta,$condicion);

                /*== Eliminando pago ==*/
                $check_pago=mainModel::ejecutar_consulta_simple("SELECT pago_id FROM pago WHERE pago_fecha='$pago_fecha' AND venta_codigo='$venta_codigo' AND usuario_id='".$_SESSION['id_svi']."' ORDER BY pago_id DESC LIMIT 1");
                $datos_pago=$check_pago->fetch();

                mainModel::eliminar_registro("pago","pago_id",$datos_pago['pago_id']);

                $check_pago->closeCursor();
                $check_pago=mainModel::desconectar($check_pago);

                $alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos podido actualizar el efectivo de la caja para poder agregar el pago.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
                exit();
            }

            $alerta=[
                "Alerta"=>"recargar",
                "Titulo"=>"¡Pago agregado!",
                "Texto"=>"El pago de la venta ha sido registrado exitosamente",
                "Tipo"=>"success"
            ];
            echo json_encode($alerta);

        } /*-- Fin controlador --*/


        
        public function agregar_producto_personalizado(){

            $modelo_de_maquina=mainModel::limpiar_cadena($_POST['familia_name']);
            $skin_juego=mainModel::limpiar_cadena($_POST['prioridad2']);
			$porcentaje_payout=mainModel::limpiar_cadena($_POST['prioridad3']);
            $denominaciones=mainModel::limpiar_cadena($_POST['prioridad4']);
            $codigo_grupo=mainModel::limpiar_cadena($_POST['codigo']);
            $id_grupo=mainModel::limpiar_cadena($_POST['prioridad1']);

           if($modelo_de_maquina=="" || $skin_juego=="" || $porcentaje_payout=="" || $denominaciones=="" || $codigo_grupo=="" || $id_grupo==""){
            $alerta=[
                "Alerta"=>"simple",
                "Titulo"=>"Ocurrió un error inesperado",
                "Texto"=>"No has llenado todos los campos que son obligatorios",
                "Tipo"=>"error"
            ];
            echo json_encode($alerta);
            exit();
        }

            $correlativo=mainModel::ejecutar_consulta_simple("SELECT catalogo_id FROM catalogo");
            $correlativo=($correlativo->rowCount())+1;
            $datos_movimiento_reg=[
                "titulo_game"=>[
					"campo_marcador"=>":Titulo_game",
					"campo_valor"=>$modelo_de_maquina
				],
                "familia_game"=>[
					"campo_marcador"=>":Skin_juego",
					"campo_valor"=>$skin_juego
				],
                "id_juego"=>[
					"campo_marcador"=>":ID_juego",
					"campo_valor"=>$id_grupo
				],
                "payout_game"=>[
					"campo_marcador"=>":Payout",
					"campo_valor"=>$porcentaje_payout
				],
                "denominacion_game"=>[
					"campo_marcador"=>":Denominacion_game",
					"campo_valor"=>$denominaciones
				],
                "codigo_grupo"=>[
					"campo_marcador"=>":Codigo_grupo",
					"campo_valor"=>$codigo_grupo
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
            $agregar_movimiento=mainModel::guardar_datos("catalogo",$datos_movimiento_reg);
           
            if($agregar_movimiento->rowCount()==1){
				
                $alerta=[
					"Alerta"=>"recargar",
					"Titulo"=>"¡Juego Añadido!",
					"Texto"=>"El juego fue añadido con éxito en el sistema",
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


        }
        /*---------- Controlador eliminar venta ----------*/
        public function eliminar_venta_controlador(){
            /*== Recuperando codigo de venta ==*/
			$codigo=mainModel::decryption($_POST['venta_codigo_del']);
            $codigo=mainModel::limpiar_cadena($codigo);
            
            /*== Comprobando venta en la BD ==*/
			$check_venta=mainModel::ejecutar_consulta_simple("SELECT id_group FROM game_group WHERE group_codigo='$codigo'");
			if($check_venta->rowCount()<=0){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"La modulo que intenta eliminar no existe en el sistema.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
			$check_venta->closeCursor();
            $check_venta=mainModel::desconectar($check_venta);

            $check_venta_detalle=mainModel::ejecutar_consulta_simple("SELECT catalogo_id FROM catalogo WHERE codigo_grupo='$codigo' LIMIT 1");
			if($check_venta_detalle->rowCount()>0){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No podemos eliminar el grupo ya que existen juegos asociados, para eliminar este grupo debe de eliminar todos los juegos del grupo e intentar nuevamente.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
			$check_venta_detalle->closeCursor();
            $check_venta_detalle=mainModel::desconectar($check_venta_detalle);

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
    

            mainModel::eliminar_registro("catalogo","codigo_grupo",$codigo);
           
            
            /*== Comprobando privilegios ==*/
		
            /*== Eliminado venta ==*/
            $eliminar_venta=mainModel::eliminar_registro("game_group","group_codigo",$codigo);

			if($eliminar_venta->rowCount()==1){
				$alerta=[
					"Alerta"=>"recargar",
					"Titulo"=>"¡Grupo eliminado!",
					"Texto"=>"El Grupo a sido eliminado exitosamente.",
					"Tipo"=>"success"
				];
			}else{
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos podido eliminar el modulo del sistema, por favor intente nuevamente.",
					"Tipo"=>"error"
				];
			}

			$eliminar_venta->closeCursor();
			$eliminar_venta=mainModel::desconectar($eliminar_venta);

			echo json_encode($alerta);
        } /*-- Fin controlador --*/
    }