<?php
	 $servidor = "localhost";
	 $usuario = "root";
	 $contrasena = "";
	 $basedatos = "game";
	 
	 $conexion = new mysqli($servidor, $usuario, $contrasena, $basedatos);
	 
	 // Verificar si hay error en la conexión
	 if ($conexion->connect_error) {
		 die("Error en la conexión: " . $conexion->connect_error);
	 }
	 
	 $peticion_ajax=true;
	 $code=(isset($_GET['code'])) ? $_GET['code'] : 0;
	 
	 /*---------- Incluyendo configuraciones ----------*/
	 require_once "../config/APP.php";
	 
	 /*---------- Instancia al controlador venta ----------*/
	 require_once "../controladores/movimientoControlador.php";
	 $ins_venta = new movimientoControlador();
	 
	 $sql = "SELECT * FROM movimiento INNER JOIN usuario ON movimiento.usuario_id=usuario.usuario_id WHERE movimiento_codigo='$code'";
	 $resultado = $conexion->query($sql);
	 
	 if($resultado->num_rows==1){
	 
		 /*---------- Datos de la venta ----------*/
		 $datos_venta=$resultado->fetch_assoc();
	 
		 /*---------- Seleccion de datos de la empresa ----------*/
		 $sql = "SELECT * FROM empresa LIMIT 1";
		 $resultado = $conexion->query($sql);
		 $datos_empresa=$resultado->fetch_assoc();
	 
		 require "./code128.php";
	 
		 $pdf = new PDF_Code128('P','mm','Letter');
		 $pdf->SetMargins(17,17,17);
		 $pdf->AddPage();
		 $pdf->Image(SERVERURL.'vistas/assets/img/logo.png',165,12,35,35,'PNG');
	 
		 $pdf->SetFont('Arial','B',16);
		 $pdf->SetTextColor(32,100,210);
		 $pdf->Cell(150,10,utf8_decode(strtoupper($datos_empresa['empresa_nombre'])),0,0,'L');
	 

		$pdf->Ln(9);

		$pdf->SetFont('Arial','',10);
		$pdf->SetTextColor(39,39,51);
		$pdf->Cell(150,9,utf8_decode($datos_empresa['empresa_tipo_documento'].": ".$datos_empresa['empresa_numero_documento']),0,0,'L');

		$pdf->Ln(5);

		$pdf->Cell(150,9,utf8_decode($datos_empresa['empresa_direccion']),0,0,'L');

		$pdf->Ln(5);

		$pdf->Cell(150,9,utf8_decode("Teléfono: ".$datos_empresa['empresa_telefono']),0,0,'L');

		$pdf->Ln(5);

		$pdf->Cell(150,9,utf8_decode("Email: ".$datos_empresa['empresa_email']),0,0,'L');

		$pdf->Ln(10);

		$pdf->SetFont('Arial','',10);
		$pdf->Cell(30,7,utf8_decode('Fecha de emisión:'),0,0);
		$pdf->SetTextColor(97,97,97);
		$pdf->Cell(116,7,utf8_decode(date("d/m/Y", strtotime($datos_venta['movimiento_fecha']))." ".$datos_venta['movimiento_hora']),0,0,'L');
		$pdf->SetFont('Arial','B',10);
		$pdf->SetTextColor(39,39,51);
		$pdf->Cell(35,7,utf8_decode(strtoupper('Factura Nro.')),0,0,'C');

		$pdf->Ln(7);

		$pdf->SetFont('Arial','',10);
		$pdf->Cell(12,7,utf8_decode('ING:'),0,0,'L');
		$pdf->SetTextColor(97,97,97);
		$pdf->Cell(134,7,utf8_decode($datos_venta['usuario_nombre']." ".$datos_venta['usuario_apellido']),0,0,'L');
		$pdf->SetFont('Arial','B',10);
		$pdf->SetTextColor(97,97,97);
		$pdf->Cell(35,7,utf8_decode(strtoupper($datos_venta['movimiento_id'])),0,0,'C');

		$pdf->Ln(10);

		if($datos_venta['usuario_id']==1){
			$pdf->SetFont('Arial','',10);
			$pdf->SetTextColor(39,39,51);
			$pdf->Cell(13,7,utf8_decode('Cliente:'),0,0);
			$pdf->SetTextColor(97,97,97);
			$pdf->Cell(60,7,utf8_decode("N/A"),0,0,'L');
			$pdf->SetTextColor(39,39,51);
			$pdf->Cell(8,7,utf8_decode("Doc: "),0,0,'L');
			$pdf->SetTextColor(97,97,97);
			$pdf->Cell(60,7,utf8_decode("N/A"),0,0,'L');
			$pdf->SetTextColor(39,39,51);
			$pdf->Cell(7,7,utf8_decode('Tel:'),0,0,'L');
			$pdf->SetTextColor(97,97,97);
			$pdf->Cell(35,7,utf8_decode("N/A"),0,0);
			$pdf->SetTextColor(39,39,51);

			$pdf->Ln(7);

			$pdf->SetTextColor(39,39,51);
			$pdf->Cell(6,7,utf8_decode('Dir:'),0,0);
			$pdf->SetTextColor(97,97,97);
			$pdf->Cell(109,7,utf8_decode("N/A"),0,0);
		}else{
			$pdf->SetFont('Arial','',10);
			$pdf->SetTextColor(39,39,51);
			$pdf->Cell(13,7,utf8_decode('Cliente:'),0,0);
			$pdf->SetTextColor(97,97,97);
			$pdf->Cell(60,7,utf8_decode($datos_venta['cliente_nombre']." ".$datos_venta['cliente_apellido']),0,0,'L');
			$pdf->SetTextColor(39,39,51);
			$pdf->Cell(8,7,utf8_decode("Doc: "),0,0,'L');
			$pdf->SetTextColor(97,97,97);
			$pdf->Cell(60,7,utf8_decode($datos_venta['cliente_tipo_documento']." ".$datos_venta['cliente_numero_documento']),0,0,'L');
			$pdf->SetTextColor(39,39,51);
			$pdf->Cell(7,7,utf8_decode('Tel:'),0,0,'L');
			$pdf->SetTextColor(97,97,97);
			$pdf->Cell(35,7,utf8_decode($datos_venta['cliente_telefono']),0,0);
			$pdf->SetTextColor(39,39,51);

			$pdf->Ln(7);

			$pdf->SetTextColor(39,39,51);
			$pdf->Cell(6,7,utf8_decode('Dir:'),0,0);
			$pdf->SetTextColor(97,97,97);
			$pdf->Cell(109,7,utf8_decode($datos_venta['cliente_provincia'].", ".$datos_venta['cliente_ciudad'].", ".$datos_venta['cliente_direccion']),0,0);
		}

		$pdf->Ln(9);

		$pdf->SetFillColor(23,83,201);
		$pdf->SetDrawColor(23,83,201);
		$pdf->SetTextColor(255,255,255);
		$pdf->Cell(100,8,utf8_decode('Descripción'),1,0,'C',true);
		$pdf->Cell(15,8,utf8_decode('Cant.'),1,0,'C',true);
		$pdf->Cell(32,8,utf8_decode('Precio'),1,0,'C',true);
		$pdf->Cell(34,8,utf8_decode('Subtotal'),1,0,'C',true);

		$pdf->Ln(8);

		$pdf->SetFont('Arial','',9);
		$pdf->SetTextColor(39,39,51);

		/*----------  Seleccionando detalles de la venta  ----------*/
		$venta_detalle=$ins_venta->datos_tabla("Normal","movimiento WHERE movimiento_codigo='".$datos_venta['movimiento_codigo']."'","*",0);
		$venta_detalle=$venta_detalle->fetchAll();

		foreach($venta_detalle as $detalle){
			if($detalle['movimiento_motivo']!="N/A"){
				$garantia_fabrica=" - Responsable: ".$detalle['movimiento_responsable'];
				$limite_caracteres=40;
			}else{
				$garantia_fabrica="";
				$limite_caracteres=60;
			}
			$pdf->Cell(100,7,utf8_decode($ins_venta->limitar_cadena($detalle['movimiento_descripcion'],$limite_caracteres,"...").$garantia_fabrica),'L',0,'C');
			$pdf->Cell(15,7,utf8_decode($detalle['movimiento_comentarios']),'L',0,'C');
			// $pdf->Cell(32,7,utf8_decode(MONEDA_SIMBOLO.number_format($detalle['venta_detalle_precio_venta'],MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR)),'L',0,'C');
			// $pdf->Cell(34,7,utf8_decode(MONEDA_SIMBOLO.number_format($detalle['venta_detalle_total'],MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR)),'LR',0,'C');
			$pdf->Ln(7);
		}

		$pdf->SetFont('Arial','B',9);
		
		if($datos_empresa['empresa_factura_impuestos']=="Si"){
			$pdf->Cell(100,7,utf8_decode(''),'T',0,'C');
			$pdf->Cell(15,7,utf8_decode(''),'T',0,'C');
			$pdf->Cell(32,7,utf8_decode('SUBTOTAL'),'T',0,'C');
			// $pdf->Cell(34,7,utf8_decode("+ ".MONEDA_SIMBOLO.number_format($datos_venta['venta_subtotal'],MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR)),'T',0,'C');

			$pdf->Ln(7);

			$pdf->Cell(100,7,utf8_decode(''),'',0,'C');
			$pdf->Cell(15,7,utf8_decode(''),'',0,'C');
			// $pdf->Cell(32,7,utf8_decode($datos_venta['venta_impuesto_nombre']." (".$datos_venta['venta_impuesto_porcentaje']."%)"),'',0,'C');
			// $pdf->Cell(34,7,utf8_decode("+ ".MONEDA_SIMBOLO.number_format($datos_venta['venta_impuestos'],MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR)),'',0,'C');

			$pdf->Ln(7);

			$pdf->Cell(100,7,utf8_decode(''),'',0,'C');
			$pdf->Cell(15,7,utf8_decode(''),'',0,'C');
		}else{
			$pdf->Cell(100,7,utf8_decode(''),'T',0,'C');
			$pdf->Cell(15,7,utf8_decode(''),'T',0,'C');
		}


		// $pdf->Cell(32,7,utf8_decode('TOTAL A PAGAR'),'T',0,'C');
		// $pdf->Cell(34,7,utf8_decode(MONEDA_SIMBOLO.number_format($datos_venta['venta_total_final'],MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR).' '.MONEDA_NOMBRE),'T',0,'C');

		$pdf->Ln(7);

		$pdf->Cell(100,7,utf8_decode(''),'',0,'C');
		$pdf->Cell(15,7,utf8_decode(''),'',0,'C');
		$pdf->Cell(32,7,utf8_decode('TOTAL PAGADO'),'',0,'C');
		// $pdf->Cell(34,7,utf8_decode(MONEDA_SIMBOLO.number_format($datos_venta['venta_pagado'],MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR).' '.MONEDA_NOMBRE),'',0,'C');

		$pdf->Ln(7);

		$pdf->Cell(100,7,utf8_decode(''),'',0,'C');
		$pdf->Cell(15,7,utf8_decode(''),'',0,'C');
		$pdf->Cell(32,7,utf8_decode('CAMBIO'),'',0,'C');
		// $pdf->Cell(34,7,utf8_decode(MONEDA_SIMBOLO.number_format($datos_venta['venta_cambio'],MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR).' '.MONEDA_NOMBRE),'',0,'C');

		$pdf->Ln(12);

		$pdf->SetFont('Arial','',9);
		// if($datos_venta['venta_pagado']<$datos_venta['venta_total_final'] && $datos_venta['venta_tipo']="Credito"){
		// 	$pdf->SetTextColor(97,97,97);
		// 	$pdf->MultiCell(0,9,utf8_decode("NOTA IMPORTANTE: Esta factura presenta un saldo pendiente de pago por la cantidad de ".MONEDA_SIMBOLO.number_format(($datos_venta['venta_total_final']-$datos_venta['venta_pagado']),MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR).' '.MONEDA_NOMBRE),0,'C',false);
		// }

		$pdf->SetTextColor(39,39,51);
		$pdf->MultiCell(0,9,utf8_decode("*** Para poder realizar un reclamo o devolución debe de presentar este formato ***"),0,'C',false);

		$pdf->Ln(9);

		$pdf->SetFillColor(39,39,51);
		$pdf->SetDrawColor(23,83,201);
        $pdf->Code128(72,$pdf->GetY(),$datos_venta['movimiento_codigo'],70,20);
        $pdf->SetXY(12,$pdf->GetY()+21);
        $pdf->SetFont('Arial','',12);
        $pdf->MultiCell(0,5,utf8_decode($datos_venta['movimiento_codigo']),0,'C',false);

		$pdf->Output("I","Factura_Nro".$datos_venta['movimiento_id'].".pdf",true);

	}else{
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<title><?php echo COMPANY; ?></title>
	<?php include '../vistas/inc/Head.php'; ?>
</head>
<body>
	<div class="full-box container-404">
		<div>
			<p class="text-center"><i class="fas fa-question-circle fa-10x"></i></p>
			<h1 class="text-center">¡Ocurrió un error!</h1>
			<p class="lead text-center">No hemos encontrado el codigo: <?php echo $code;?>, reintenta nuevamentel.</p>
		</div>
	</div>
	<?php include '../vistas/inc/Script.php'; ?>
</body>
</html>
<?php } ?>