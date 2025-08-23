<?php
class Usuario {
    private ?int $id = null;
    private ?string $fotografia = null;
    private int $CI;
    private string $nombre;
    private string $apellido;
    private string $rol = 'lector';
    private ?int $celular = null;
    private string $password;
    private ?string $direccion = null;
    private string $correo;

    public static function desdeArray(array $a): self {
        $u = new self();
        $u->id = (int)$a['id_usuario'];
        $u->fotografia = $a['fotografia'] ?? null;
        $u->CI = (int)$a['CI'];
        $u->nombre = $a['nombre'];
        $u->apellido = $a['apellido'];
        $u->rol = $a['rol'];
        $u->celular = isset($a['celular']) ? (int)$a['celular'] : null;
        $u->password = $a['password'];
        $u->direccion = $a['direccion'] ?? null;
        $u->correo = $a['correo'];
        return $u;
    }

    public function getId(): ?int { return $this->id; }
    public function getFotografia(): ?string { return $this->fotografia; }
    public function getCI(): int { return $this->CI; }
    public function getNombre(): string { return $this->nombre; }
    public function getApellido(): string { return $this->apellido; }
    public function getRol(): string { return $this->rol; }
    public function getCelular(): ?int { return $this->celular; }
    public function getPassword(): string { return $this->password; }
    public function getDireccion(): ?string { return $this->direccion; }
    public function getCorreo(): string { return $this->correo; }

    public function setFotografia(?string $v): void { $this->fotografia = $v; }
    public function setCI(int $v): void { $this->CI = $v; }
    public function setNombre(string $v): void { $this->nombre = $v; }
    public function setApellido(string $v): void { $this->apellido = $v; }
    public function setRol(string $v): void { $this->rol = $v; }
    public function setCelular(?int $v): void { $this->celular = $v; }
    public function setPassword(string $v): void { $this->password = $v; }
    public function setDireccion(?string $v): void { $this->direccion = $v; }
    public function setCorreo(string $v): void { $this->correo = $v; }
}
