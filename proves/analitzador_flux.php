<?php

/**
 * Script per analitzar els punts d'entrada i les connexions entre fitxers
 * per facilitar la creació d'un mapa mental de testing.
 */

$root_dir = '/home/dani/orbix_local/orbix/';
$entry_point = 'apps/actividades/controller/actividad_que.php';

function analitzar_fitxer($ruta_relativa, &$mapa = [], $visitats = [])
{
    global $root_dir;
    $ruta_completa = $root_dir . $ruta_relativa;

    if (in_array($ruta_relativa, $visitats) || !file_exists($ruta_completa)) {
        return;
    }

    $visitats[] = $ruta_relativa;
    $contingut = file_get_contents($ruta_completa);
    $connexions = [];

    // 1. Cercar enllaços via Hash::link o rutes hardcoded
    // Patró: cerca cadenes que semblin rutes de l'app (apps/.../controller/....php)
    preg_match_all('/apps\/[a-z0-9_\/]+\.php/i', $contingut, $matches);
    if (!empty($matches[0])) {
        foreach ($matches[0] as $url) {
            $connexions['links'][] = $url;
        }
    }

    // 2. Cercar crides AJAX (jQuery $.post, $.get, o url: '...')
    // Busquem fitxers .php dins de blocs de text que semblin AJAX
    preg_match_all('/[\'"]([a-z0-9_\/\.]+\.php)[\'"]/i', $contingut, $ajax_matches);
    if (!empty($ajax_matches[1])) {
        foreach ($ajax_matches[1] as $url) {
            // Filtrem per evitar fitxers de sistema com header.inc
            if (strpos($url, 'controller') !== false) {
                $connexions['ajax'][] = $url;
            }
        }
    }

    // 3. Cercar la Vista (si s'utilitza ViewTwig o ViewPhtml)
    // Sovint el nom de la vista es dedueix del controlador o es passa com a paràmetre
    if (preg_match('/new\s+ViewTwig\s*\(\s*[\'"]([^\'"]+)[\'"]/', $contingut, $view_match)) {
        $connexions['vista'] = $view_match[1];
    }

    $mapa[$ruta_relativa] = $connexions;

    // Recursivitat limitada per no entrar en bucles infinits (opcional)
    foreach ($connexions as $tipus => $urls) {
        if (is_array($urls)) {
            foreach ($urls as $url) {
                analitzar_fitxer($url, $mapa, $visitats);
            }
        }
    }
}

$resultat = [];
analitzar_fitxer($entry_point, $resultat);

// Output en format llista per al mapa mental
echo "MAPA DE CONNEXIONS TROBADES:\n";
echo "============================\n";
foreach ($resultat as $origen => $destins) {
    echo "\n[PUNT D'ENTRADA]: $origen\n";
    if (!empty($destins['links'])) {
        foreach (array_unique($destins['links']) as $d) echo "  --> (Link/Form): $d\n";
    }
    if (!empty($destins['ajax'])) {
        foreach (array_unique($destins['ajax']) as $a) echo "  --> (AJAX): $a\n";
    }
}