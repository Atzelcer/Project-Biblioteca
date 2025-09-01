// Menú desplegable lateral
document.addEventListener('DOMContentLoaded', function(){
	var sidebar = document.getElementById('sidebar');
	var btnSidebar = document.getElementById('btnSidebarToggle');
	btnSidebar.onclick = function(){
		sidebar.classList.toggle('mini');
	};
});
document.addEventListener('DOMContentLoaded', function(){
	// Carrusel de textos largos en libros
	setTimeout(function(){
		document.querySelectorAll('.libro .scroll-text').forEach(function(el){
			var parent = el.parentElement;
			var parentWidth = parent.offsetWidth;
			var textWidth = el.scrollWidth;
			if(textWidth > parentWidth){
				var end = -(textWidth - parentWidth + 10) + 'px';
				el.style.setProperty('--scroll-end', end);
				el.style.animation = 'scrollText 6s linear infinite';
			}else{
				el.style.animation = '';
			}
		});
	}, 300);
	// Botón de cambio de tema
	var btnTheme = document.getElementById('btnThemeToggle');
	var root = document.documentElement;
	var isLight = false;
	btnTheme.onclick = function(){
		isLight = !isLight;
		if(isLight){
			root.setAttribute('data-theme','light');
			btnTheme.textContent = 'Modo oscuro';
		}else{
			root.removeAttribute('data-theme');
			btnTheme.textContent = 'Modo claro';
		}
	};
	// MODAL CI USUARIO
	var modalCI = document.getElementById('modalCI');
	var modalContent = modalCI?.querySelector('.modal-content');
	var modalTitle = document.getElementById('modalTitleCI');
	var modalForm = document.getElementById('modalCIForm');
	var modalResultado = document.getElementById('modalCIResultado');
	var btnBuscarCI = document.getElementById('btnBuscarCI');
	var btnCancelarCI = document.getElementById('btnCancelarCI');
	var inputCI = document.getElementById('inputCI');
	var libroActual = null;
	var libroSeleccionado = {};

	// Abrir modal al hacer click en cualquier libro
	if (document.querySelector('.libros')) {
		document.querySelector('.libros').addEventListener('click', function(e){
			var libroDiv = e.target.closest('.libro');
			if (!libroDiv) return;
			modalCI.style.display = 'flex';
			// Guardar datos del libro seleccionado
			libroSeleccionado = {
				id: libroDiv.dataset.id || '',
				codigo: libroDiv.dataset.codigo || '',
				nombre: libroDiv.dataset.nombre || '',
				autor: libroDiv.dataset.autor || '',
				portada: libroDiv.querySelector('img')?.src || ''
			};
			// Reset modal para nueva búsqueda
			modalForm.style.display = 'block';
			modalTitle.style.display = 'block';
			modalResultado.style.display = 'none';
			modalContent.classList.remove('grande');
			inputCI.value = '';
		});
	}
	// Evento buscar CI
	btnBuscarCI?.addEventListener('click', function(){
		// Simular consulta de usuario según CI
		var usuariosDB = {
			'12345': {
				CI: '12345', nombre: 'Maria Elena', apellido: 'Antezana', correo: 'bibliotecario@biblioteca.edu', celular: '70123456', fotografia: '1_Maria_Martinez.jpg'
			},
			'11111': {
				CI: '11111', nombre: 'Juan Carlos', apellido: 'Perez', correo: 'juan.perez@gmail.com', celular: '70234567', fotografia: '7_Pedro_Lopez.jpg'
			},
			'22222': {
				CI: '22222', nombre: 'Ana Maria', apellido: 'Rodriguez', correo: 'ana.rodriguez@gmail.com', celular: '70345678', fotografia: '4_Ana_Gonzalez.jpg'
			},
			'33333': {
				CI: '33333', nombre: 'Carlos Alberto', apellido: 'Lopez', correo: 'carlos.lopez@gmail.com', celular: '70456789', fotografia: '10_Diego_Lopez.jpg'
			}
		};
		var ciIngresado = inputCI.value.trim();
		var usuario = usuariosDB[ciIngresado] || {
			CI: ciIngresado,
			nombre: 'Desconocido',
			apellido: '',
			correo: '',
			celular: '',
			fotografia: 'default-user.png'
		};
		var fotoUsuario = usuario.fotografia ? (window.baseUrl + '/public/images/users/' + usuario.fotografia) : (window.baseUrl + '/public/images/users/default-user.png');
		// Iconos SVG
		var iconUser = '<span class="icon">'+
			'<svg width="22" height="22" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="5" stroke="#2d7dff" stroke-width="1.5" fill="none"/><path d="M4 20c0-3.3 3.6-6 8-6s8 2.7 8 6" stroke="#7a2bff" stroke-width="1.5" fill="none"/></svg>'+
			'</span>';
		var iconBook = '<span class="icon">'+
			'<svg width="22" height="22" viewBox="0 0 24 24" fill="none"><rect x="4" y="4" width="16" height="16" rx="3" stroke="#ff3358" stroke-width="1.5" fill="none"/><path d="M8 8h8v8H8z" stroke="#2d7dff" stroke-width="1.5" fill="none"/></svg>'+
			'</span>';
		// Mostrar datos en el modal
		modalForm.style.display = 'none';
		modalTitle.style.display = 'none';
		modalResultado.style.display = 'block';
		modalResultado.innerHTML =
			'<div style="text-align:center;padding:18px 0 8px 0;font-size:1.18em;color:var(--blue);font-weight:700;letter-spacing:0.7px;text-shadow:0 2px 8px #7a2bff22;">Información detallada</div>'+
			'<div class="modal-info-grid">'+
				'<div class="modal-info-card">'+
					'<h4>'+iconUser+'Usuario</h4>'+
					'<hr>'+
					'<img src="'+fotoUsuario+'" alt="Foto de perfil" style="width:80px;height:80px;object-fit:cover;">'+
					'<p><b>CI:</b> '+usuario.CI+'</p>'+
					'<p><b>Nombre:</b> '+usuario.nombre+' '+usuario.apellido+'</p>'+
					'<p><b>Correo:</b> '+usuario.correo+'</p>'+
					'<p><b>Celular:</b> '+usuario.celular+'</p>'+
				'</div>'+
				'<div class="modal-info-card">'+
					'<h4 style="color:var(--violet);">'+iconBook+'Libro</h4>'+
					'<hr>'+
					(libroSeleccionado.portada ? '<img src="'+libroSeleccionado.portada+'" alt="Portada" style="width:80px;height:110px;">' : '')+
					'<p><b>Código:</b> '+libroSeleccionado.codigo+'</p>'+
					'<p><b>Título:</b> '+libroSeleccionado.nombre+'</p>'+
					'<p><b>Autor:</b> '+libroSeleccionado.autor+'</p>'+
				'</div>'+
			'</div>'+
			'<div class="modal-actions">'+
				'<button id="btnReservarModal" class="btn modal-btn confirm">Reservar</button> ' +
				'<button id="btnCancelarModal" class="btn modal-btn cancel">Cancelar</button>'+
			'</div>';
		modalContent.classList.add('grande');
		// Botón cancelar y reservar dentro del resultado
		document.getElementById('btnCancelarModal').onclick = function(){
			modalCI.style.display = 'none';
			modalForm.style.display = 'block';
			modalTitle.style.display = 'block';
			modalResultado.style.display = 'none';
			modalContent.classList.remove('grande');
			inputCI.value = '';
		};
		document.getElementById('btnReservarModal').onclick = function(){
			// Obtener datos necesarios
			var ciIngresado = inputCI.value.trim();
			var idLibro = libroSeleccionado?.id || '';
			
			if(!ciIngresado) {
				showErrorModal('Error', 'Por favor ingresa el CI del usuario');
				return;
			}
			
			if(!idLibro) {
				showErrorModal('Error', 'Error al obtener la información del libro seleccionado');
				return;
			}

			// Buscar el usuario por CI para obtener su ID
			fetch(window.baseUrl + '/src/entradas/api_usuarios.php?action=list&q=' + encodeURIComponent(ciIngresado))
				.then(response => response.json())
				.then(function(userData){
					if(!userData.ok || !userData.data || !userData.data.length) {
						showErrorModal('Error', 'No se encontró un usuario con el CI: ' + ciIngresado);
						return;
					}

					var usuario = userData.data[0];
					var idUsuario = usuario.id_usuario;

					// Mostrar estado de carga en el botón
					var btnReservar = document.getElementById('btnReservarModal');
					var textoOriginal = btnReservar.textContent;
					btnReservar.disabled = true;
					btnReservar.textContent = 'Procesando...';
					btnReservar.style.opacity = '0.6';

					// Crear la reserva con todas las validaciones
					fetch(window.baseUrl + '/src/entradas/api_reservas.php?action=crear', {
						method: 'POST',
						headers: {'Content-Type': 'application/json'},
						body: JSON.stringify({
							id_usuario: idUsuario,
							id_libro: idLibro
						})
					})
					.then(response => response.json())
					.then(function(resultado){
						// Restaurar el botón
						btnReservar.disabled = false;
						btnReservar.textContent = textoOriginal;
						btnReservar.style.opacity = '1';

						if(resultado.ok){
							// ✅ RESERVA EXITOSA
							showSuccessModal('¡Reserva Exitosa!', 
								resultado.msg || 'La reserva se ha creado correctamente para el usuario ' + usuario.nombre);
							
							// Cerrar el modal principal después de mostrar el éxito
							setTimeout(() => {
								modalCI.style.display = 'none';
								modalForm.style.display = 'block';
								modalTitle.style.display = 'block';
								modalResultado.style.display = 'none';
								modalContent.classList.remove('grande');
								inputCI.value = '';
							}, 2000);
						} else {
							// Manejar diferentes tipos de error según el código
							switch(resultado.code) {
								case 'USUARIO_BLOQUEADO':
									// ❌ USUARIO BLOQUEADO
									showErrorModal('Usuario Bloqueado', 
										'El usuario está bloqueado y no puede realizar reservas en este momento');
									break;
								
								case 'YA_ACTIVA':
									// ⚠️ YA TIENE UNA RESERVA
									showWarningModal('Reserva Existente', 
										'El usuario ya tiene una reserva activa. Debe completar la reserva actual antes de crear una nueva');
									break;
								
								case 'LIBRO_NO_DISPONIBLE':
									// ⚠️ LIBRO NO DISPONIBLE
									showWarningModal('Libro No Disponible', 
										'El libro seleccionado no está disponible en este momento');
									break;
								
								case 'LIBRO_NO_EXISTE':
									// ❌ LIBRO NO EXISTE
									showErrorModal('Error', 
										'El libro solicitado no existe en el sistema');
									break;
								
								default:
									// ❌ ERROR GENERAL
									showErrorModal('Error', 
										resultado.msg || 'No se pudo crear la reserva. Intenta nuevamente');
							}
						}
					})
					.catch(function(error) {
						// Restaurar el botón en caso de error de red
						btnReservar.disabled = false;
						btnReservar.textContent = textoOriginal;
						btnReservar.style.opacity = '1';
						
						showErrorModal('Error de Conexión', 
							'No se pudo conectar con el servidor. Verifica tu conexión e intenta nuevamente');
					});
				})
				.catch(function(error) {
					showErrorModal('Error de Conexión', 
						'No se pudo buscar la información del usuario. Verifica tu conexión e intenta nuevamente');
				});
		};
	});
	// Evento cancelar principal
	btnCancelarCI?.addEventListener('click', function(){
		modalCI.style.display = 'none';
		// Reset modal para próxima vez
		modalForm.style.display = 'block';
		modalTitle.style.display = 'block';
		modalResultado.style.display = 'none';
		modalContent.classList.remove('grande');
		inputCI.value = '';
	});

	// Guardar baseUrl global para fetch
	window.baseUrl = document.body.dataset.base || '';

	// Sistema de modales personalizados
	window.showSuccessModal = function(title, message) {
		createAndShowModal(title, message, 'success');
	};

	window.showErrorModal = function(title, message) {
		createAndShowModal(title, message, 'error');
	};

	window.showWarningModal = function(title, message) {
		createAndShowModal(title, message, 'warning');
	};

	window.createAndShowModal = function(title, message, type = 'info') {
		// Eliminar modal existente si existe
		const existingModal = document.getElementById('customModal');
		if (existingModal) {
			existingModal.remove();
		}

		// Crear modal HTML
		const modal = document.createElement('div');
		modal.id = 'customModal';
		modal.style.cssText = `
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: rgba(0,0,0,0.6);
			display: flex;
			justify-content: center;
			align-items: center;
			z-index: 10000;
		`;

		const colors = {
			success: { bg: '#d4edda', border: '#28a745', text: '#155724', icon: '✅' },
			error: { bg: '#f8d7da', border: '#dc3545', text: '#721c24', icon: '❌' },
			warning: { bg: '#fff3cd', border: '#ffc107', text: '#856404', icon: '⚠️' },
			info: { bg: '#d1ecf1', border: '#17a2b8', text: '#0c5460', icon: 'ℹ️' }
		};

		const color = colors[type] || colors.info;

		modal.innerHTML = `
			<div style="
				background: white;
				padding: 30px;
				border-radius: 10px;
				box-shadow: 0 8px 25px rgba(0,0,0,0.3);
				max-width: 450px;
				width: 90%;
				text-align: center;
				border-left: 6px solid ${color.border};
			">
				<div style="
					display: flex;
					align-items: center;
					justify-content: center;
					margin-bottom: 20px;
				">
					<span style="
						font-size: 28px;
						margin-right: 12px;
					">${color.icon}</span>
					<h3 style="
						margin: 0;
						color: ${color.text};
						font-size: 20px;
						font-weight: 600;
					">${title}</h3>
				</div>
				<div style="
					background: ${color.bg};
					padding: 18px;
					border-radius: 8px;
					border: 1px solid ${color.border};
					margin-bottom: 25px;
				">
					<p style="
						margin: 0;
						color: ${color.text};
						font-size: 15px;
						line-height: 1.4;
					">${message}</p>
				</div>
				<button onclick="hideCustomModal()" style="
					background: ${color.border};
					color: white;
					border: none;
					padding: 12px 25px;
					border-radius: 6px;
					cursor: pointer;
					font-size: 15px;
					font-weight: 500;
					transition: all 0.3s ease;
				" 
				onmouseover="this.style.opacity='0.9'"
				onmouseout="this.style.opacity='1'">Cerrar</button>
			</div>
		`;

		document.body.appendChild(modal);

		// Auto-cerrar éxito en 4 segundos
		if (type === 'success') {
			setTimeout(() => {
				hideCustomModal();
			}, 4000);
		}
	};

	window.hideCustomModal = function() {
		const modal = document.getElementById('customModal');
		if (modal) {
			modal.remove();
		}
	};
});
// JS principal para el panel del bibliotecario
// Puedes agregar aquí la lógica del modal y eventos de libros
