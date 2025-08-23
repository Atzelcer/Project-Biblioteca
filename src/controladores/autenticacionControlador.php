<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../nucleo/vista.php';
require_once __DIR__ . '/../servicios/autenticacionServicio.php';

class AutenticacionControlador {
    private AutenticacionServicio $servicio;
    public function __construct(AutenticacionServicio $servicio){ $this->servicio=$servicio; }

    public function formulario(string $ok = '', string $error = ''): void {
        echo Vista::render('auth/inicio_sesion.html', ['titulo'=>'Iniciar sesion','ok'=>$ok,'error'=>$error]);
    }

    public function login(string $usuario, string $clave): void {
        if ($usuario === '' || $clave === '') { $this->formulario('', 'faltan datos'); return; }

        $u = $this->servicio->obtenerPorIdentificador($usuario); // correo o CI
        if ($u === null) { $this->formulario('', 'usuario no encontrado'); return; }

        if (!$this->servicio->validarClave($u, $clave)) { $this->formulario('', 'clave incorrecta'); return; }

        session_start();
        $_SESSION['idUsuario']=$u->getIdUsuario();
        $_SESSION['nombre']=$u->getNombre();
        $_SESSION['rol']=$u->getRol();
        $_SESSION['flash_ok']='bienvenido '.$u->getNombre();
        header('Location: ' . BASE_URL . '/src/entradas/panel.php');
    }
}
