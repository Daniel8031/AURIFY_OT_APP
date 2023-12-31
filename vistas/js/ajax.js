const formularios_ajax = document.querySelectorAll(".FormularioAjax");

/*----------  Funcion enviar formularios ajax  ----------*/
function enviar_formulario_ajax(e){
	e.preventDefault();

	let data = new FormData(this);
	let method = this.getAttribute("method");
	let action = this.getAttribute("action");
	let tipo = this.getAttribute("data-form");

	let encabezados = new Headers();

	let config = { 
		method: method,
       	headers: encabezados,
       	mode: 'cors',
       	cache: 'no-cache',
       	body: data
    };

	let texto_alerta;

    if(tipo==="save"){
        texto_alerta="Los datos serán guardados en el sistema";
    }else if(tipo==="delete"){
        texto_alerta="Los datos serán eliminados completamente del sistema";
    }else if(tipo==="update"){
    	texto_alerta="Los datos serán actualizados en el sistema";
    }else if(tipo==="search"){
        texto_alerta="Se eliminará el término de búsqueda y tendrás que escribir uno nuevamente";
    }else if(tipo==="shop"){
    	texto_alerta="Desea remover el producto seleccionado";
    }else if(tipo==="sale_cliente"){
		texto_alerta="Desea remover el cliente seleccionado";
	}else{
        texto_alerta="Quieres realizar la operación solicitada";
	}
	
    
    Swal.fire({
		title: "¿Estás seguro?",
		text: texto_alerta,
		type: 'question',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Aceptar',
		cancelButtonText: 'Cancelar'
	}).then((result) => {
		if(result.value){

			var options =  {
				content: "Procesando información… Por favor espere",
				style: "snackbar",
				timeout: 3000
			}
			
			$.snackbar(options);

			fetch(action,config)
	        .then(respuesta => respuesta.json())
	        .then(respuesta =>{
				return alertas_ajax(respuesta);
			});	
	
		}
	});
}


/*----------  Funcion listar formularios  ----------*/
formularios_ajax.forEach(formularios => {
	formularios.addEventListener("submit", enviar_formulario_ajax);
});


/*----------  Funcion mostrar alertas  ----------*/

/*----------  Funcion mostrar alertas  ----------*/
function alertas_ajax(alerta){
	if(alerta.Alerta==="simple"){
		Swal.fire({
		  title: alerta.Titulo,
		  text: alerta.Texto,
		  type: alerta.Tipo,
		  confirmButtonText: 'Aceptar'
		});
	}else if(alerta.Alerta==="recargar"){
		Swal.fire({
		  title: alerta.Titulo,
		  text: alerta.Texto,
		  type: alerta.Tipo,
		  confirmButtonText: 'Aceptar'
		}).then((result)=>{
			if(result.value) {
				location.reload();
			}
		});
	}else if(alerta.Alerta==="limpiar"){
		Swal.fire({
		  title: alerta.Titulo,
		  text: alerta.Texto,
		  type: alerta.Tipo,
		  confirmButtonText: 'Aceptar'
		}).then((result)=>{
			if(result.value) {
				document.querySelector(".FormularioAjax").reset();
			}
		});
	}else if(alerta.Alerta==="venta"){
		Swal.fire({
			title: alerta.Titulo,
			text: alerta.Texto,
			type: alerta.Tipo,
			confirmButtonText: 'Aceptar'
		}).then((result)=>{
			if(result.value) {
				document.querySelector('#sale-barcode-input').value="";
			}
		});
	}else if(alerta.Alerta==="redireccionar"){
		window.location.href=alerta.URL;
	}Swal.fire({
		icon: 'success',
		title: alerta.Titulo,
		text: alerta.Texto,
		type: alerta.Tipo,
		showConfirmButton: false,
		timer: 1500,
	}).then(function () {
		// Redirige a la URL después de que se cierre el cuadro de diálogo de SweetAlert
		window.location.href = alerta.URL;
	});
}


