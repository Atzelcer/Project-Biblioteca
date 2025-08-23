<?php
class Libro {
    private int $idLibro;
    private string $codigoLibro;
    private string $nombre;
    private string $autor;
    private string $estado;
    private ?string $fotoLibro;

    public function __construct(int $idLibro, string $codigoLibro, string $nombre, string $autor, string $estado, ?string $fotoLibro) {
        $this->idLibro = $idLibro;
        $this->codigoLibro = $codigoLibro;
        $this->nombre = $nombre;
        $this->autor = $autor;
        $this->estado = $estado;
        $this->fotoLibro = $fotoLibro;
    }
    public function getIdLibro(): int { return $this->idLibro; }
    public function getCodigoLibro(): string { return $this->codigoLibro; }
    public function getNombre(): string { return $this->nombre; }
    public function getAutor(): string { return $this->autor; }
    public function getEstado(): string { return $this->estado; }
    public function getFotoLibro(): ?string { return $this->fotoLibro; }
}
