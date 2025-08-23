<?php
class Vista {
    public static function render(string $ruta, array $data = []): string {
        $archivo = __DIR__ . '/../vistas/' . $ruta;
        if (!is_file($archivo)) return 'vista no encontrada';
        $html = file_get_contents($archivo);
        $data['base'] = BASE_URL;
        foreach ($data as $k => $v) {
            $html = str_replace('{{'.$k.'}}', $v, $html);
        }
        return $html;
    }
}
