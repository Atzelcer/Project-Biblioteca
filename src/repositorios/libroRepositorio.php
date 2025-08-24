<?php
require_once __DIR__ . '/../config/baseDatos.php';
require_once __DIR__ . '/../modelos/libro.php';

interface LibroRepositorioContrato {
    public function listarDisponibles(string $q=''): array;
    public function obtenerPorId(int $id): ?Libro;
    public function actualizarEstado(int $idLibro, string $estado): bool;
}

class LibroRepositorio implements LibroRepositorioContrato {
    private PDO $db;
    public function __construct(PDO $db){ $this->db = $db; }

    public function listarLibros(): array {
        $st = $this->db->prepare("SELECT id_libro,codigo_libro,nombre,autor,estado,foto_libro
                                   FROM libros ORDER BY nombre");
        $st->execute();
        $rows = $st->fetchAll();
        $out = [];
        foreach($rows as $r){
            $out[] = new Libro((int)$r['id_libro'],$r['codigo_libro'],$r['nombre'],$r['autor'],$r['estado'],$r['foto_libro']);
        }
        return $out;
    }

    public function listarDisponibles(string $q=''): array {
        if ($q==='') {
            $st = $this->db->prepare("SELECT id_libro,codigo_libro,nombre,autor,estado,foto_libro
                                      FROM libros WHERE estado='Disponible' ORDER BY nombre");
            $st->execute();
        } else {
            $like = '%'.$q.'%';
            $st = $this->db->prepare("SELECT id_libro,codigo_libro,nombre,autor,estado,foto_libro
                                      FROM libros
                                      WHERE estado='Disponible' AND (nombre LIKE ? OR autor LIKE ?)
                                      ORDER BY nombre");
            $st->execute([$like,$like]);
        }
        $rows = $st->fetchAll();
        $out=[];
        foreach($rows as $r){
            $out[] = new Libro((int)$r['id_libro'],$r['codigo_libro'],$r['nombre'],$r['autor'],$r['estado'],$r['foto_libro']);
        }
        return $out;
    }

    public function obtenerPorId(int $id): ?Libro {
        $st = $this->db->prepare("SELECT id_libro,codigo_libro,nombre,autor,estado,foto_libro FROM libros WHERE id_libro=? LIMIT 1");
        $st->execute([$id]);
        $r = $st->fetch();
        if(!$r) return null;
        return new Libro((int)$r['id_libro'],$r['codigo_libro'],$r['nombre'],$r['autor'],$r['estado'],$r['foto_libro']);
    }

    public function actualizarEstado(int $idLibro, string $estado): bool {
        $st = $this->db->prepare("UPDATE libros SET estado=? WHERE id_libro=?");
        return $st->execute([$estado,$idLibro]);
    }
}
