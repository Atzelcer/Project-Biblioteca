<?php
require_once __DIR__ . '/../modelos/usuario.php';

class AutenticacionServicio {
    private UsuarioRepositorio $repo;
    public function __construct(UsuarioRepositorio $repo) { $this->repo = $repo; }

    public function autenticar(string $usuario, string $clave): Usuario {
        $row = $this->repo->buscarPorCIoCorreo($usuario);
        if (!$row) throw new RuntimeException('Usuario no encontrado');
        if ($row['password'] !== sha1($clave)) throw new RuntimeException('Clave incorrecta');
        // Si el usuario está bloqueado por su estado
        if (isset($row['estado']) && strtolower($row['estado']) === 'bloqueado') {
            throw new RuntimeException('bloqueado');
        }
        return Usuario::desdeArray($row);
    }
}
