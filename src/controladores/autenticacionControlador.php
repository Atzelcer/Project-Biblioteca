<?php
class AutenticacionControlador {
    private AutenticacionServicio $svc;

    public function __construct(AutenticacionServicio $svc) {
        $this->svc = $svc;
    }

    public function formulario(): void {
        require_once __DIR__ . '/../nucleo/vista.php';
        session_start();
        $msgErr = $_SESSION['flash_err'] ?? '';
        $msgOk  = $_SESSION['flash_ok']  ?? '';
        unset($_SESSION['flash_err'], $_SESSION['flash_ok']);

        echo Vista::render('auth/inicio_sesion.html', [
            'base'          => BASE_URL,
            'mensaje_error' => $msgErr,
            'mensaje_ok'    => $msgOk
        ]);
    }

    public function login(string $usuario, string $clave): void {
        session_start();
        try {
            $u = $this->svc->autenticar($usuario, $clave);
            $_SESSION['idUsuario'] = $u->getId();
            $_SESSION['nombre']    = $u->getNombre();
            $_SESSION['apellido']  = $u->getApellido();
            $_SESSION['rol']       = $u->getRol();
            $_SESSION['foto']      = $u->getFotografia() ?: 'default-user.png';
            $_SESSION['estado']    = $u->getEstado();
            header('Location: ' . BASE_URL . '/src/entradas/panel.php');
        } catch (RuntimeException $e) {
            $_SESSION['flash_err'] = $e->getMessage();
            header('Location: ' . BASE_URL . '/src/entradas/login.php');
        }
    }
}
