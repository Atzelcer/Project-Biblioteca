<?php
require_once __DIR__ . '/../repositorios/libroRepositorio.php';
require_once __DIR__ . '/../repositorios/reservaRepositorio.php';

class ReservaServicio {
    private LibroRepositorioContrato $libros;
    private ReservaRepositorioContrato $reservas;

    public function __construct(LibroRepositorioContrato $libros, ReservaRepositorioContrato $reservas){
        $this->libros = $libros;
        $this->reservas = $reservas;
    }

    private function generarFicha(): string {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        do {
            $f = '';
            for($i=0;$i<6;$i++) $f .= $chars[random_int(0, strlen($chars)-1)];
        } while ($this->reservas->existeFicha($f));
        return $f;
    }

    public function reservar(int $idUsuario, int $idLibro): array {
        // Validar que el usuario no tenga ya una reserva activa
        if ($this->reservas->tieneActiva($idUsuario)) {
            return ['ok'=>false,'code'=>'YA_ACTIVA','msg'=>'Tienes una reserva pendiente'];
        }

        $libro = $this->libros->obtenerPorId($idLibro);
        if($libro === null) return ['ok'=>false,'msg'=>'libro no existe'];
        if($libro->getEstado() !== 'Disponible') return ['ok'=>false,'msg'=>'no disponible'];

        $ficha = $this->generarFicha();
        $this->reservas->crearPendiente($idUsuario, $idLibro, $ficha);
        $this->libros->actualizarEstado($idLibro, 'Reservado'); 

        return [
            'ok'=>true,
            'ficha'=>$ficha,
            'libroNombre'=>$libro->getNombre(),
            'libroAutor'=>$libro->getAutor(),
            'estado'=>'pendiente'
        ];
    }
}
