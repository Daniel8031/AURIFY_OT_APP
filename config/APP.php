<?php

	/*----------  Ruta o dominio del servidor  ----------*/
	const SERVERURL="http://localhost/Sistema_de_Generacion_OT/";


	/*----------  Nombre de la empresa o compañia  ----------*/
	const COMPANY="OT";

	/*---------- Nombre de la sesion (Solo numeros y letras sin espacios ejemplo: VENTAS) ----------*/
	const SESSION_NAME="OT";

	/*----------  Tipos de documentos  ----------*/
	const DOCUMENTOS_USUARIOS=["DUI","DNI","Cedula","Licencia","Pasaporte","Otro"];
	const DOCUMENTOS_PROVEEDORES=["DNI","Cedula","NIT","RUC","Otro"];
	const DOCUMENTOS_EMPRESA=["DNI","Cedula","NIT","RUC","Otro"];



	/*----------  Tipos de unidades de productos  ----------*/
	const PRODUCTO_UNIDAD=["Unidad","Libra","Kilogramo","Caja","Paquete","Lata","Galon","Botella","Tira","Sobre","Bolsa","Saco","Tarjeta","Otro"];

	/*----------  Garantia de productos  ----------*/
	const GARANTIA_TIEMPO=["N/A","Dias","Semanas","Mes","Meses","Año","Años"];


	/*----------  Marcador de campos obligatorios  ----------*/
	const CAMPO_OBLIGATORIO='&nbsp; <i class="fab fa-font-awesome-alt"></i> &nbsp;';


	/*----------  Configuración de codigos de barras

		BARCODE_FORMAT -> CODE128 | CODE39 | EAN | EAN-13 | EAN-8 | EAN-5 | EAN-2 | UPC | ITF | ITF-14 | MSI | MSI10 | MSI11 | MSI1010 | MSI1110 | Pharmacode

		BARCODE_TEXT_ALIGN -> center | left | right

		BARCODE_TEXT_POSITION -> top | bottom

	----------*/

	const BARCODE_FORMAT="CODE128";
	const BARCODE_TEXT_ALIGN="center";
	const BARCODE_TEXT_POSITION="bottom";


	/*----------  Tamaño de papel de impresora termica (en milimetros)  
		THERMAL_PRINT_SIZE -> 80 | 57
	----------*/
	const THERMAL_PRINT_SIZE="80";

/*----------  Configuración de moneda  ----------*/
const MONEDA_SIMBOLO="$";
const MONEDA_NOMBRE="MX";
const MONEDA_DECIMALES="2";
const MONEDA_SEPARADOR_MILLAR=",";
const MONEDA_SEPARADOR_DECIMAL=".";
	/*----------  Zona horaria  ----------*/
	date_default_timezone_set("America/El_Salvador");

	/*
		Configuración de zona horaria de tu país, para más información visita
		http://php.net/manual/es/function.date-default-timezone-set.php
		http://php.net/manual/es/timezones.php
	*/