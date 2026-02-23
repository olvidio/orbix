<?php


// Carreguem tots els analitzadors necessaris
require_once "analitzadors/analitzador_phtml.php";
require_once "analitzadors/analitzador_twig.php";
require_once "analitzadors/resolutor_vistes.php";
require_once "analitzadors/analitzador_llista.php";
require_once "analitzadors/analitzador_js.php"; // El nou per a fitxers .js

$root_dir = '/home/dani/orbix_local/orbix/';
$fitxer_inicial = "apps/actividades/controller/actividad_select.php";

$analitzats = [];
$cua = [$fitxer_inicial];

echo "🔍 ANÀLISI RECURSIVA: PHP + PHTML + JS\n";
echo str_repeat("-", 60) . "\n";

while (!empty($cua)) {
    $actual = array_shift($cua);
    if (in_array($actual, $analitzats)) continue;
    $analitzats[] = $actual;

    echo "\n📄 PROCESSANT PHP: $actual\n";
    $full_php = $root_dir . ltrim($actual, '/');

    if (!file_exists($full_php)) {
        echo "   ❌ ERROR: No es troba el fitxer PHP a: $full_php\n";
        continue;
    }

    $contingut_php = file_get_contents($full_php);
    $links_locals = [];

    // 1. RESOLUCIÓ DE VISTA (Amb la correcció de barres \ corregida al resolutor)
    $info = resoldre_ruta_vista($contingut_php, $root_dir);

    if ($info['rel_vista']) {
        echo "   🎯 Intentant carregar vista: " . $info['rel_vista'] . "\n";
        if (file_exists($info['full_vista'])) {
            echo "   ✅ Vista trobada! Analitzant " . $info['extensio'] . "...\n";
            $cont_vista = file_get_contents($info['full_vista']);

            // Analitzar contingut de la vista (links directes, formularis, fnjs_update_div)
            $links_v = ($info['extensio'] === 'twig')
                ? analitzar_twig($cont_vista, $contingut_php)
                : analitzar_phtml($cont_vista, $contingut_php);
            $links_locals = array_merge($links_locals, $links_v);

            // --- PUNT CLAU: ANALITZAR JS EXTERN (actividades.js) ---
            // Busquem etiquetes <script src="..."> per seguir el rastre
            // Dins del bloc del JS al test_recursivitat.php
            // Busquem qualsevol cosa que sembli una ruta a un .js dins d'una etiqueta <script>
            // El patró busca: src=, després opcionalment cometes i tags de PHP, i finalment la ruta .js
            if (preg_match_all('/src=["\']?(?:<\?=\s*[\'"])?([a-z0-9_\-\.\/]+\.js[^"\'>\s]*)/i', $cont_vista, $m_js)) {
                foreach ($m_js[1] as $js_brut) {
                    // 1. Netegem possibles tancaments de PHP o cometes que hagin quedat al final
                    $js_neta = preg_replace('/([\'"]?\s*\?>.*)$/', '', $js_brut);

                    // 2. Treure el ?rand() o paràmetres
                    $js_neta = preg_replace('/\?.*/', '', $js_neta);

                    // 3. Eliminar cometes sobrants
                    $js_neta = trim($js_neta, " '\"");

                    echo "   📦 Analitzant JS detectat: $js_neta\n";

                    $rutes_js = analitzar_fitxer_js($js_neta, $root_dir);
                    foreach ($rutes_js as $r) {
                        echo "      🌿 Branca trobada al JS -> $r\n";
                        if (!in_array($r, $analitzats) && !in_array($r, $cua)) {
                            $cua[] = $r;
                        }
                    }
                }
            }
        } else {
            echo "   ❌ ERROR: La vista no existeix a: " . $info['full_vista'] . "\n";
        }
    }

    // 2. ANALITZAR LLISTA (BOTONS JS)
    if ($info['es_llista']) {
        echo "   📋 Analitzant botons de 'Lista' dins del PHP...\n";
        $links_l = analitzar_llista_php($contingut_php);
        $links_locals = array_merge($links_locals, $links_l);
    }

    // 3. RECURSIVITAT I RESULTATS
    $links_locals = array_unique($links_locals);
    foreach ($links_locals as $l) {
        // Netegem rutes buides o caràcters estranys
        $l = trim($l);
        if (empty($l)) continue;

        if (strpos($l, '[') === false && strpos($l, '$') === false) {
            echo "   🌿 BRANCA TROBADA -> $l\n";
            if (!in_array($l, $analitzats) && !in_array($l, $cua)) {
                $cua[] = $l;
            }
        } else {
            // Això ens ajuda a veure on fallen les regex o hi ha variables PHP
            echo "   ⚠️ VARIABLE NO RESOLTA: $l\n";
        }
    }
}

echo "\n" . str_repeat("-", 60) . "\n";
echo "✅ Fi de l'anàlisi recursiva.\n";