<?php
class RegistroControlador {
    private RegistroServicio $svc;

    public function __construct(RegistroServicio $svc) {
        $this->svc = $svc;
    }

    public function formulario(): void {
        require_once __DIR__ . '/../nucleo/vista.php';
        session_start();
        $err = $_SESSION['flash_err'] ?? '';
        unset($_SESSION['flash_err']);

        echo Vista::render('auth/registro.html', [
            'base'          => BASE_URL,
            'mensaje_error' => $err
        ]);
    }

    public function guardar(array $post, array $files): void {
        session_start();
        try {
            $this->svc->registrar($post, $files);
            $_SESSION['flash_ok'] = 'Cuenta creada. Ya puedes iniciar sesión.';
            header('Location: ' . BASE_URL . '/src/entradas/login.php');
        } catch (RuntimeException $e) {
            $_SESSION['flash_err'] = $e->getMessage();
            header('Location: ' . BASE_URL . '/src/entradas/registro.php');
        }
    }
}
