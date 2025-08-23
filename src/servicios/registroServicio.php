<?php
require_once __DIR__ . '/../modelos/usuario.php';

class RegistroServicio {
    private UsuarioRepositorio $repo;

    public function __construct(UsuarioRepositorio $repo) {
        $this->repo = $repo;
    }

    public function registrar(array $post, array $files): void {
        $nombre    = trim($post['nombre']    ?? '');
        $apellido  = trim($post['apellido']  ?? '');
        $ci        = trim($post['ci']        ?? '');
        $celular   = trim($post['celular']   ?? '');
        $correo    = trim($post['correo']    ?? '');
        $direccion = trim($post['direccion'] ?? '');
        $pwd       = trim($post['pwd']       ?? '');

        if ($nombre==='' || $apellido==='' || $ci==='' || $correo==='' || $pwd==='') {
            throw new RuntimeException('Completa los campos obligatorios');
        }
        if ($this->repo->existeCI($ci))          throw new RuntimeException('CI ya registrado');
        if ($this->repo->existeCorreo($correo))  throw new RuntimeException('Correo ya registrado');

        $foto = null;
        if (!empty($files['foto']['name']) && is_uploaded_file($files['foto']['tmp_name'])) {
            $ext = strtolower(pathinfo($files['foto']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','webp','gif'])) $ext = 'jpg';
            $base = preg_replace('/[^a-z0-9_]/i','_', $ci . '_' . $nombre . '_' . $apellido);
            $foto = $base . '.' . $ext;

            $dir = __DIR__ . '/../../public/images/users';
            if (!is_dir($dir)) @mkdir($dir, 0777, true);
            @move_uploaded_file($files['foto']['tmp_name'], $dir . '/' . $foto);
        }

        $u = new Usuario();
        $u->setFotografia($foto);
        $u->setCI((int)$ci);
        $u->setNombre($nombre);
        $u->setApellido($apellido);
        $u->setRol('lector');
        $u->setCelular($celular === '' ? null : (int)$celular);
        $u->setPassword(sha1($pwd));
        $u->setDireccion($direccion);
        $u->setCorreo($correo);

        $this->repo->crear($u);
    }
}
