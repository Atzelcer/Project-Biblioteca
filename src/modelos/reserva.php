<?php
class Reserva {
    private int $idReserva;
    private int $idUsuario;
    private int $idLibro;
    private string $fichaReserva;
    private ?string $fechaReserva;
    private ?string $fechaDevolucion;
    private ?string $fechaDevolucionReal;
    private string $estado;

    public function __construct(
        int $idReserva, int $idUsuario, int $idLibro, string $fichaReserva,
        ?string $fechaReserva, ?string $fechaDevolucion, ?string $fechaDevolucionReal, string $estado
    ) {
        $this->idReserva=$idReserva; $this->idUsuario=$idUsuario; $this->idLibro=$idLibro;
        $this->fichaReserva=$fichaReserva; $this->fechaReserva=$fechaReserva;
        $this->fechaDevolucion=$fechaDevolucion; $this->fechaDevolucionReal=$fechaDevolucionReal;
        $this->estado=$estado;
    }
    public function getFicha(): string { return $this->fichaReserva; }
}
