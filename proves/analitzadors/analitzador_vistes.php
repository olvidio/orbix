<?php
function extreure_rutes_de_vista($ruta_completa_vista, $contingut_php_controlador) {
    if (!file_exists($ruta_completa_vista)) return [];

    $contingut_vista = file_get_contents($ruta_completa_vista);
    $rutes_trobades = [];

    // 1. Cercar text literal (strings tipus 'fitxer.php' o "fitxer.php")
    // Útil per a jQuery $.post('actividad_tipo_get.php', ...)
    preg_match_all('/[\'"]([a-z0-9_\-]+\.php)[\'"]/i', $contingut_vista, $matches_literals);
    foreach ($matches_literals[1] as $url) {
        $rutes_trobades[] = $url;
    }

    // 2. Cercar variables Twig {{ url_variable }}
    preg_match_all('/\{\{\s*([a-z0-9_]+)\s*\}\}/i', $contingut_vista, $matches_twig);
    foreach ($matches_twig[1] as $var_twig) {
        // Busquem la definició d'aquesta variable al controlador PHP
        // Ex: 'url_actualizar' => $url_actualizar
        $pattern = '/[\'"]' . $var_twig . '[\'"]\s*=>\s*\$([a-z0-9_]+)/i';
        if (preg_match($pattern, $contingut_php_controlador, $m_php_var)) {
            $nom_var_php = $m_php_var[1];
            // Ara busquem on s'assigna valor a la variable PHP
            // Ex: $url_actualizar = ConfigGlobal::getWeb().'/.../fitxer.php';
            $pattern_url = '/\$' . $nom_var_php . '\s*=\s*[^;]*[\'"]([^\'"]+\.php)[\'"]/i';
            if (preg_match($pattern_url, $contingut_php_controlador, $m_url)) {
                $rutes_trobades[] = $m_url[1];
            }
        }
    }

    return array_unique($rutes_trobades);
}