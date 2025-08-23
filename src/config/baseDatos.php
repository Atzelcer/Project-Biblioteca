<?php
class BaseDatos {
    private static ?PDO $instancia = null;

    public static function conexion(): PDO {
        if (self::$instancia === null) {
            $dsn = 'mysql:host=127.0.0.1;dbname=biblioteca_bd;charset=utf8mb4';
            self::$instancia = new PDO($dsn, 'root', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        }
        return self::$instancia;
    }
}
