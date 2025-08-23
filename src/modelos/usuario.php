<?php
class Usuario {
    private int $idUsuario;
    private ?string $fotografia;
    private int $ci;
    private string $nombre;
    private string $apellido;
    private string $rol;
    private ?int $celular;
    private string $password;
    private ?string $direccion;
    private string $correo;

    public function __construct(
        int $idUsuario,
        ?string $fotografia,
        int $ci,
        string $nombre,
        string $apellido,
        string $rol,
        ?int $celular,
        string $password,
        ?string $direccion,
        string $correo
    ) {
        $this->idUsuario=$idUsuario; $this->fotografia=$fotografia; $this->ci=$ci;
        $this->nombre=$nombre; $this->apellido=$apellido; $this->rol=$rol;
        $this->celular=$celular; $this->password=$password; $this->direccion=$direccion; $this->correo=$correo;
    }

    public function getIdUsuario(): int { return $this->idUsuario; }
    public function getFotografia(): ?string { return $this->fotografia; }
    public function getCi(): int { return $this->ci; }
    public function getNombre(): string { return $this->nombre; }
    public function getApellido(): string { return $this->apellido; }
    public function getRol(): string { return $this->rol; }
    public function getCelular(): ?int { return $this->celular; }
    public function getPassword(): string { return $this->password; }
    public function getDireccion(): ?string { return $this->direccion; }
    public function getCorreo(): string { return $this->correo; }
}
