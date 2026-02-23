<?php

require_once "analitzadors/analitzador_phtml.php";
require_once "analitzadors/analitzador_twig.php";

$fitxer_vista = $argv[1] ?? 'apps/actividades/templates/actividad_que.html.twig';
$root_dir = '/home/dani/orbix_local/orbix/';
$cami_vista_complet = $root_dir . ltrim($fitxer_vista, '/');

if (!file_exists($cami_vista_complet)) {
    die("❌ Error: La vista no existeix a: $cami_vista_complet\n");
}

// LÒGICA DE RUTES: Forcem que busqui a la carpeta 'controller'
$info = pathinfo($cami_vista_complet);
$nom_base = str_replace('.html.twig', '', $info['filename']);
$nom_base = str_replace('.html', '', $nom_base);
$directori_base = dirname($info['dirname']); // Puja un nivell des de 'view' o 'templates'
$fitxer_php = $directori_base . "/controller/" . $nom_base . ".php";

$contingut_php = file_exists($fitxer_php) ? file_get_contents($fitxer_php) : "";
$contingut_vista = file_get_contents($cami_vista_complet);
$extensio = (strpos($fitxer_vista, '.twig') !== false) ? 'twig' : 'phtml';

echo "🔍 Vista: " . basename($fitxer_vista) . "\n";
echo "🔍 Controller: " . (file_exists($fitxer_php) ? "OK (" . basename($fitxer_php) . ")" : "⚠️ NO TROBAT: $fitxer_php") . "\n";

$links = ($extensio === 'twig') ? analitzar_twig($contingut_vista, $contingut_php) : analitzar_phtml($contingut_vista, $contingut_php);

echo str_repeat("-", 45) . "\n";
if (empty($links)) {
    echo "No s'ha detectat cap enllaç.\n";
} else {
    foreach ($links as $link) {
        echo "👉 $link\n";
    }
}
echo str_repeat("-", 45) . "\n";