<?php

function analitzar_fitxer_js($cami_js_relatiu, $root_dir)
{
    $rutes = [];

    // 1. Neteja de la ruta del fitxer JS (treure rand, quotes de PHP, etc.)
    $neta_ruta = preg_replace('/\?.*/', '', $cami_js_relatiu);
    $neta_ruta = str_replace(["<?= '", "<?= \"", "' ?>", "\" ?>", " "], "", $neta_ruta);

    // 2. Intentar localitzar el fitxer físicament
    $full_path = "";
    if (strpos($neta_ruta, 'apps/') !== false) {
        $full_path = $root_dir . substr($neta_ruta, strpos($neta_ruta, 'apps/'));
    } else {
        $full_path = $root_dir . ltrim($neta_ruta, '/');
    }

    if (file_exists($full_path)) {
        $contingut = file_get_contents($full_path);

        // 3. REGEX ULTRA-PERMISSIVA
        // Busca qualsevol cadena que contingui 'apps/' i acabi en '.php'
        // Això agafarà: this.action = "apps/...", url: "apps/...", etc.
        $pattern = '/["\'](apps\/[a-z0-9_\-\.\/]+\.php)["\']/i';

        if (preg_match_all($pattern, $contingut, $m)) {
            foreach ($m[1] as $url) {
                $rutes[] = trim($url);
            }
        }
    }

    return array_unique($rutes);
}
