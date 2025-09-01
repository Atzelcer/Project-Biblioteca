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
			alert('Reserva creada correctamente (simulado)');
			modalCI.style.display = 'none';
			modalForm.style.display = 'block';
			modalTitle.style.display = 'block';
			modalResultado.style.display = 'none';
			modalContent.classList.remove('grande');
			inputCI.value = '';
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
});
// JS principal para el panel del bibliotecario
// Puedes agregar aquí la lógica del modal y eventos de libros
