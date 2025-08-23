<?php
require_once __DIR__ . '/../config/baseDatos.php';
require_once __DIR__ . '/../modelos/usuario.php';

interface UsuarioRepositorioContrato {
    public function buscarPorCorreo(string $correo): ?Usuario;
    public function buscarPorCi(int $ci): ?Usuario;
}

class UsuarioRepositorio implements UsuarioRepositorioContrato {
    private PDO $db;
    public function __construct(PDO $db){ $this->db=$db; }

    public function buscarPorCorreo(string $correo): ?Usuario {
        $st = $this->db->prepare('SELECT id_usuario,fotografia,CI,nombre,apellido,rol,celular,password,direccion,correo FROM usuario WHERE correo=? LIMIT 1');
        $st->execute([$correo]);
        $f = $st->fetch();
        return $f ? $this->map($f) : null;
    }

    public function buscarPorCi(int $ci): ?Usuario {
        $st = $this->db->prepare('SELECT id_usuario,fotografia,CI,nombre,apellido,rol,celular,password,direccion,correo FROM usuario WHERE CI=? LIMIT 1');
        $st->execute([$ci]);
        $f = $st->fetch();
        return $f ? $this->map($f) : null;
    }

    private function map(array $f): Usuario {
        return new Usuario(
            (int)$f['id_usuario'],
            $f['fotografia'],
            (int)$f['CI'],
            $f['nombre'],
            $f['apellido'],
            $f['rol'],
            $f['celular'] !== null ? (int)$f['celular'] : null,
            $f['password'],
            $f['direccion'],
            $f['correo']
        );
    }
}
