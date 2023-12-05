<?php

	if($peticion_ajax){
		require_once "../modelos/mainModel.php";
	}else{
		require_once "./modelos/mainModel.php";
	}

	class productoControlador extends mainModel{

    
		/*---------- Controlador actualizar imagen de producto ----------*/
		public function actualizar_imagen_producto_controlador(){



			/*== Recuperando id del producto ==*/
			$id=mainModel::decryption($_POST['producto_img_id_up']);
			$id=mainModel::limpiar_cadena($id);

			/*== Comprobando producto en la DB ==*/
            $check_producto=mainModel::ejecutar_consulta_simple("SELECT * FROM empresa WHERE empresa_id='$id'");
            if($check_producto->rowCount()<=0){
            	$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos encontrado el producto registrado en el sistema.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
            }else{
            	$campos=$check_producto->fetch();
			}
			$check_producto->closeCursor();
			$check_producto=mainModel::desconectar($check_producto);
			
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
			
			/*== Comprobando si se ha seleccionado una imagen ==*/
            if($_FILES['empresa_foto']['name']=="" || $_FILES['empresa_foto']['size']<=0){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"Parece que no ha seleccionado una imagen.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}

			/* Comprobando formato de las imagenes */
			if($_FILES['empresa_foto']['type']!="image/jpeg" && $_FILES['empresa_foto']['type']!="image/png"){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"La imagen que ha seleccionado es de un formato que no está permitido.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}

			/* Comprobando que la imagen no supere el peso permitido */
			$img_max_size=3072;
			if(($_FILES['empresa_foto']['size']/1024)>$img_max_size){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"La imagen que ha seleccionado supera el límite de peso permitido.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}

			/* Almacenando extencion de la imagen */
			switch ($_FILES['empresa_foto']['type']) {
				case 'image/jpeg':
				  $img_ext=".jpg";
				break;
				case 'image/png':
				  $img_ext=".png";
				break;
			}

			/* Nombre final de la imagen */
			$codigo_img=mainModel::generar_codigo_aleatorio(10,$id);	
			$img_final_name=$codigo_img.$img_ext;

			/* Directorios de imagenes */
			$img_dir='../vistas/assets/empresa/';

			/* Cambiando permisos al directorio */
			chmod($img_dir, 0777);

			/* Moviendo imagen al directorio */
			if(!move_uploaded_file($_FILES['empresa_foto']['tmp_name'], $img_dir.$img_final_name)){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No podemos subir la imagen al sistema en este momento, por favor intente nuevamente.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}

			/* Eliminando la imagen anterior */
			if(is_file($img_dir.$campos['empresa_foto'])){
				chmod($img_dir, 0777);
				unlink($img_dir.$campos['empresa_foto']);
			}

			/*== Preparando datos para enviarlos al modelo ==*/
			$datos_producto_up=[
				"empresa_foto"=>[
					"campo_marcador"=>":Foto",
					"campo_valor"=>$img_final_name
				]
			];

			$condicion=[
				"condicion_campo"=>"empresa_id",
				"condicion_marcador"=>":ID",
				"condicion_valor"=>$id
			];

			if(mainModel::actualizar_datos("empresa",$datos_producto_up,$condicion)){
				$alerta=[
					"Alerta"=>"recargar",
					"Titulo"=>"¡Imagen actualizada!",
					"Texto"=>"La imagen del producto se actualizo con éxito",
					"Tipo"=>"success"
				];
			}else{

				if(is_file($img_dir.$img_final_name)){
					chmod($img_dir, 0777);
					unlink($img_dir.$img_final_name);
				}
				
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos podido actualizar la imagen, por favor intente nuevamente",
					"Tipo"=>"error"
				];
			}
			echo json_encode($alerta);
		} /*-- Fin controlador --*/
		
		/*---------- Controlador eliminar imagen de producto ----------*/
		public function eliminar_imagen_producto_controlador(){

			/*== Recuperando id del producto ==*/
			$id=mainModel::decryption($_POST['producto_img_id_del']);
			$id=mainModel::limpiar_cadena($id);

			/*== Comprobando producto en la DB ==*/
            $check_producto=mainModel::ejecutar_consulta_simple("SELECT * FROM empresa WHERE empresa_id='$id'");
            if($check_producto->rowCount()<=0){
            	$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos encontrado el producto registrado en el sistema.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
            }else{
            	$campos=$check_producto->fetch();
			}
			$check_producto->closeCursor();
			$check_producto=mainModel::desconectar($check_producto);

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

			/* Directorios de imagenes */
			$img_dir='../vistas/assets/empresa/';

			/* Eliminando la imagen anterior */
			if(is_file($img_dir.$campos['empresa_foto'])){
				chmod($img_dir, 0777);
				if(!unlink($img_dir.$campos['empresa_foto'])){
					$alerta=[
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrió un error inesperado",
						"Texto"=>"No hemos podido eliminar la imagen del producto, por favor intente nuevamente",
						"Tipo"=>"error"
					];
					echo json_encode($alerta);
					exit();
				}
			}

			/*== Preparando datos para enviarlos al modelo ==*/
			$datos_producto_up=[
				"empresa_foto"=>[
					"campo_marcador"=>":Foto",
					"campo_valor"=>""
				]
			];

			$condicion=[
				"condicion_campo"=>"empresa_id",
				"condicion_marcador"=>":ID",
				"condicion_valor"=>$id
			];

			if(mainModel::actualizar_datos("empresa",$datos_producto_up,$condicion)){
				$alerta=[
					"Alerta"=>"recargar",
					"Titulo"=>"¡Imagen eliminada!",
					"Texto"=>"La imagen del producto se elimino con éxito",
					"Tipo"=>"success"
				];
			}else{		
				$alerta=[
					"Alerta"=>"recargar",
					"Titulo"=>"¡Imagen eliminada!",
					"Texto"=>"Hemos tratado de eliminar la imagen del producto, sin embargo, tuvimos algunos inconvenientes en caso de que la imagen no este eliminada por favor intente nuevamente",
					"Tipo"=>"error"
				];
			}
			echo json_encode($alerta);
		} /*-- Fin controlador --*/

		
    }