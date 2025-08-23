<?php
class UsuarioRepositorio {
    private PDO $db;
    public function __construct(PDO $db) { $this->db = $db; }

    public function buscarPorCIoCorreo(string $usuario): ?array {
        if (strpos($usuario, '@') !== false) {
            $st = $this->db->prepare('SELECT * FROM usuario WHERE correo=? LIMIT 1');
            $st->execute([$usuario]);
        } else {
            $st = $this->db->prepare('SELECT * FROM usuario WHERE CI=? LIMIT 1');
            $st->execute([$usuario]);
        }
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function existeCI(string $ci): bool {
        $st = $this->db->prepare('SELECT 1 FROM usuario WHERE CI=? LIMIT 1');
        $st->execute([$ci]);
        return (bool)$st->fetchColumn();
    }

    public function existeCorreo(string $correo): bool {
        $st = $this->db->prepare('SELECT 1 FROM usuario WHERE correo=? LIMIT 1');
        $st->execute([$correo]);
        return (bool)$st->fetchColumn();
    }

    public function crear(Usuario $u): int {
        $sql = 'INSERT INTO usuario (fotografia,CI,nombre,apellido,rol,celular,password,direccion,correo)
                VALUES (?,?,?,?,?,?,?,?,?)';
        $st = $this->db->prepare($sql);
        $st->execute([
            $u->getFotografia(),
            $u->getCI(),
            $u->getNombre(),
            $u->getApellido(),
            $u->getRol(),
            $u->getCelular(),
            $u->getPassword(),
            $u->getDireccion(),
            $u->getCorreo()
        ]);
        return (int)$this->db->lastInsertId();
    }
}
