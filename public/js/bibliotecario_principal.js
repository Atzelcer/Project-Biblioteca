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
					// Botón reservar con validaciones mejoradas
					document.getElementById('btnReservarModal').onclick = function(){
						// Obtener datos necesarios
						var idUsuario = u.id_usuario || u.id || '';
						var idLibro = libroActual.dataset.id || '';
						
						if(!idUsuario || !idLibro) {
							showErrorModal('Error', 'Faltan datos para procesar la reserva');
							return;
						}

						// Mostrar loading en el botón
						var btnReservar = document.getElementById('btnReservarModal');
						var originalText = btnReservar.textContent;
						btnReservar.disabled = true;
						btnReservar.textContent = 'Procesando...';

						fetch(window.baseUrl + '/src/entradas/api_reservas.php?action=crear', {
							method: 'POST',
							headers: {'Content-Type':'application/json'},
							body: JSON.stringify({
								id_usuario: idUsuario,
								id_libro: idLibro
							})
						})
						.then(r=>r.json())
						.then(function(j){
							// Restaurar botón
							btnReservar.disabled = false;
							btnReservar.textContent = originalText;

							if(j.ok){
								// Reserva exitosa
								showSuccessModal('¡Reserva Exitosa!', j.msg || 'La reserva se ha creado correctamente');
								// Cerrar modal después de mostrar éxito
								setTimeout(() => {
									modal.style.display = 'none';
									inputCI.value = '';
									inputCI.style.display = '';
									btnBuscar.style.display = '';
									btnCancelar.style.display = '';
									document.getElementById('modalCIInfo')?.remove();
								}, 2000);
							} else {
								// Manejar diferentes tipos de error
								if(j.code === 'USUARIO_BLOQUEADO') {
									showErrorModal('Usuario Bloqueado', j.msg || 'El usuario está bloqueado y no puede realizar reservas');
								} else if(j.code === 'YA_ACTIVA') {
									showWarningModal('Reserva Existente', j.msg || 'El usuario ya tiene una reserva activa');
								} else if(j.code === 'LIBRO_NO_DISPONIBLE') {
									showWarningModal('Libro No Disponible', j.msg || 'El libro no está disponible en este momento');
								} else if(j.code === 'LIBRO_NO_EXISTE') {
									showErrorModal('Error', j.msg || 'El libro solicitado no existe');
								} else {
									showErrorModal('Error', j.msg || 'No se pudo crear la reserva');
								}
							}
						})
						.catch(function(error) {
							// Restaurar botón en caso de error
							btnReservar.disabled = false;
							btnReservar.textContent = originalText;
							showErrorModal('Error de Conexión', 'No se pudo conectar con el servidor. Intenta nuevamente.');
						});
					};
				});
	};

	// Guardar baseUrl global para fetch
	window.baseUrl = document.body.dataset.base || '';

	// ============== FUNCIONES DE MODALES ==============
	function showSuccessModal(title, message) {
		createAndShowModal('success', title, message, '#4CAF50');
		// Auto cerrar después de 3 segundos
		setTimeout(() => hideCustomModal(), 3000);
	}

	function showErrorModal(title, message) {
		createAndShowModal('error', title, message, '#f44336');
	}

	function showWarningModal(title, message) {
		createAndShowModal('warning', title, message, '#ff9800');
	}

	function createAndShowModal(type, title, message, color) {
		// Remover modal anterior si existe
		var existingModal = document.getElementById('customModal');
		if(existingModal) existingModal.remove();

		// Crear nuevo modal
		var modalHtml = `
			<div id="customModal" style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.7);z-index:2000;display:flex;align-items:center;justify-content:center;">
				<div style="background:#2d2d2d;color:#fff;padding:24px;border-radius:12px;min-width:320px;max-width:500px;text-align:center;box-shadow:0 8px 32px rgba(0,0,0,0.3);">
					<div style="width:60px;height:60px;margin:0 auto 16px;background:${color};border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:28px;color:white;">
						${type === 'success' ? '✓' : type === 'error' ? '✕' : '⚠'}
					</div>
					<h3 style="margin:0 0 12px;color:${color};">${title}</h3>
					<p style="margin:0 0 20px;color:#ccc;line-height:1.4;">${message}</p>
					<button onclick="hideCustomModal()" style="background:${color};color:white;border:none;padding:10px 24px;border-radius:6px;cursor:pointer;font-size:14px;">Aceptar</button>
				</div>
			</div>
		`;
		
		document.body.insertAdjacentHTML('beforeend', modalHtml);
	}

	function hideCustomModal() {
		var modal = document.getElementById('customModal');
		if(modal) modal.remove();
	}

	// Hacer las funciones globales para uso en onclick
	window.hideCustomModal = hideCustomModal;
});
// JS principal para el panel del bibliotecario
// Puedes agregar aquí la lógica del modal y eventos de libros
