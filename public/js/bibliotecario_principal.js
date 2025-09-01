document.addEventListener('DOMContentLoaded', function(){
	var modal = document.getElementById('modalCI');
	var btnCancelar = document.getElementById('btnCancelarCI');
	var btnBuscar = document.getElementById('btnBuscarCI');
	var inputCI = document.getElementById('inputCI');
	var libroActual = null;

	// Abrir modal al hacer click en cualquier libro
	document.querySelector('.libros').addEventListener('click', function(e){
		var libroDiv = e.target.closest('.libro');
		if(libroDiv){
			libroActual = libroDiv;
			modal.style.display = 'block';
		}
	});

	// Cerrar modal con botón cancelar o fondo
	btnCancelar.onclick = function(){
		modal.style.display = 'none';
		inputCI.value = '';
		document.getElementById('modalCIInfo')?.remove();
	};
	modal.querySelector('.modal-bg').onclick = btnCancelar.onclick;

	// Buscar CI y mostrar datos
	btnBuscar.onclick = function(){
		var ci = inputCI.value.trim();
		if(!ci) return alert('Ingresa un CI válido');
			fetch(window.baseUrl + '/src/entradas/api_usuarios.php?action=list&q=' + encodeURIComponent(ci))
				.then(r=>r.json()).then(function(j){
					if(!j.ok || !j.data || !j.data.length) {
						alert('Usuario no encontrado');
						return;
					}
					var u = j.data[0];
					// Obtener datos del libro
				var img = libroActual.querySelector('img');
				var nombreLibro = libroActual.dataset.nombre || '';
				var autorLibro = libroActual.dataset.autor || '';
				var codigoLibro = libroActual.dataset.codigo || '';
					// Ocultar input y botones
					inputCI.style.display = 'none';
					btnBuscar.style.display = 'none';
					btnCancelar.style.display = 'none';
					// Mostrar info usuario y libro
					var infoHtml = document.createElement('div');
					infoHtml.id = 'modalCIInfo';
					infoHtml.style = 'margin-top:18px;background:#222;padding:16px;border-radius:8px;color:#fff;';
					infoHtml.innerHTML =
						'<h4>Datos del usuario</h4>'+
						'<p><b>CI:</b> '+u.CI+'</p>'+
						'<p><b>Nombre:</b> '+u.nombre+' '+u.apellido+'</p>'+
						'<p><b>Correo:</b> '+(u.correo||'')+'</p>'+
						'<p><b>Celular:</b> '+(u.celular||'')+'</p>'+
						'<hr style="margin:12px 0;">'+
						'<h4>Datos del libro</h4>'+
						'<img src="'+(img?.src||'')+'" alt="Portada" style="width:80px;height:110px;display:inline-block;margin-right:12px;vertical-align:middle;">'+
						'<p><b>Código:</b> '+codigoLibro+'</p>'+
						'<p><b>Título:</b> '+nombreLibro+'</p>'+
						'<p><b>Autor:</b> '+autorLibro+'</p>'+
						// Botones reservar/cancelar
									'<div style="margin-top:18px;text-align:right;">'+
										'<button id="btnReservarModal" class="btn primary">Reservar</button> '+
										'<button id="btnCancelarModal" class="btn">Cancelar</button>'+
									'</div>';
					document.getElementById('modalCIInfo')?.remove();
					modal.querySelector('.modal-content').appendChild(infoHtml);
					// Botón cancelar del modal info
					document.getElementById('btnCancelarModal').onclick = function(){
						modal.style.display = 'none';
						inputCI.value = '';
						inputCI.style.display = '';
						btnBuscar.style.display = '';
						btnCancelar.style.display = '';
						document.getElementById('modalCIInfo')?.remove();
					};
					// Botón reservar (sin funcionalidad real)
					document.getElementById('btnReservarModal').onclick = function(){
									// Obtener datos necesarios
									var idUsuario = u.id_usuario || u.id || '';
									var idLibro = libroActual.dataset.id || '';
									if(!idUsuario || !idLibro) return alert('Faltan datos para reservar');
									fetch(window.baseUrl + '/src/entradas/api_reservas.php?action=crear', {
										method: 'POST',
										headers: {'Content-Type':'application/json'},
										body: JSON.stringify({
											id_usuario: idUsuario,
											id_libro: idLibro
										})
									})
									.then(r=>r.json()).then(function(j){
										if(j.ok){
											alert('Reserva creada correctamente');
											modal.style.display = 'none';
											inputCI.value = '';
											inputCI.style.display = '';
											btnBuscar.style.display = '';
											btnCancelar.style.display = '';
											document.getElementById('modalCIInfo')?.remove();
										}else{
											alert(j.msg || 'No se pudo crear la reserva');
										}
									});
					};
				});
	};

	// Guardar baseUrl global para fetch
	window.baseUrl = document.body.dataset.base || '';
});
// JS principal para el panel del bibliotecario
// Puedes agregar aquí la lógica del modal y eventos de libros
