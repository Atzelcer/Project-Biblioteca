<?php
require_once __DIR__ . '/../config/baseDatos.php';
require_once __DIR__ . '/../modelos/reserva.php';

interface ReservaRepositorioContrato {
    public function crearPendiente(int $idUsuario, int $idLibro, string $ficha): int;
    public function existeFicha(string $ficha): bool;
}

class ReservaRepositorio implements ReservaRepositorioContrato {
    private PDO $db;
    public function __construct(PDO $db){ $this->db = $db; }

    public function crearPendiente(int $idUsuario, int $idLibro, string $ficha): int {
        $sql = "INSERT INTO reservas (id_usuario,id_libro,fichaReserva,fechaReserva,fechaDevolucion,fechaDevolucionReal,estado)
                VALUES (?,?,?,?,?,?,?)";
        $st = $this->db->prepare($sql);
        $st->execute([$idUsuario,$idLibro,$ficha,null,null,null,'pendiente']);
        return (int)$this->db->lastInsertId();
    }

    public function existeFicha(string $ficha): bool {
        $st = $this->db->prepare("SELECT 1 FROM reservas WHERE fichaReserva=? LIMIT 1");
        $st->execute([$ficha]);
        return (bool)$st->fetchColumn();
    }
}
