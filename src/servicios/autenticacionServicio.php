<?php
require_once __DIR__ . '/../repositorios/usuarioRepositorio.php';

class AutenticacionServicio {
    private UsuarioRepositorioContrato $usuarios;
    public function __construct(UsuarioRepositorioContrato $usuarios){ $this->usuarios=$usuarios; }

    public function obtenerPorIdentificador(string $ident): ?Usuario {
        $ident = trim($ident);
        if (ctype_digit($ident)) {
            return $this->usuarios->buscarPorCi((int)$ident);
        }
        return $this->usuarios->buscarPorCorreo($ident);
    }

    public function validarClave(Usuario $u, string $clave): bool {
        $hash = $u->getPassword();
        if (strlen($hash) === 40) return hash('sha1', $clave) === $hash;
        return password_verify($clave, $hash);
    }
}
