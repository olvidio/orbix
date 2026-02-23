<?php


require_once "analitzadors/analitzador_phtml.php";
require_once "analitzadors/analitzador_twig.php";
require_once "analitzadors/resolutor_vistes.php";
require_once "analitzadors/analitzador_llista.php";
require_once "analitzadors/analitzador_js.php";

// Paràmetres de línia de comandes
if ($argc < 2) {
    die("❌ Error: Cal especificar el fitxer inicial\nÚs: php generar_mapa.php <fitxer_inicial>\nExemple: php generar_mapa.php apps/actividades/controller/actividad_select.php\n");
}

$fitxer_inicial = $argv[1];

// Configuració de rutes
$root_dir = '/home/dani/orbix_local/orbix/';
$nom_fitxer = pathinfo($fitxer_inicial, PATHINFO_FILENAME);

// Descobrir el mòdul a partir del path
if (preg_match('#apps/([^/]+)/#', $fitxer_inicial, $matches)) {
    $modul = $matches[1];
} elseif (preg_match('#frontend/([^/]+)/#', $fitxer_inicial, $matches)) {
    $modul = $matches[1];
} else {
    $modul = 'unknown';
}

$output_file = $root_dir . "documentacion/Documentación Obix/" . $modul . "/mapa_$nom_fitxer.md";

$analitzats = [];
$cua = [$fitxer_inicial];
$connexions = [];
$errors = [];

echo "🚀 Generant diagrama Mermaid a: $output_file...\n";

while (!empty($cua)) {
    $actual = array_shift($cua);
    if (in_array($actual, $analitzats)) continue;
    $analitzats[] = $actual;

    $full_php = $root_dir . ltrim($actual, '/');
    $id_origen = str_replace(['/', '.', '-'], '_', $actual);
    $nom_origen = basename($actual);

    if (!file_exists($full_php)) {
        $errors[] = $id_origen;
        continue;
    }

    $contingut_php = file_get_contents($full_php);
    $links_locals = [];

    // 1. DETERMINAR VISTA I LLISTA
    $info = resoldre_ruta_vista($contingut_php, $root_dir, $actual);

    // --- ANALITZAR VISTA (PHTML / TWIG) ---
    if ($info['rel_vista']) {
        $id_vista = str_replace(['/', '.', '-'], '_', $info['rel_vista']);
        $nom_vista = basename($info['rel_vista']);

        if (file_exists($info['full_vista'])) {
            // Connexió Controlador --> Vista
            $connexions[] = "    " . $id_origen . "([\"" . $nom_origen . "\"]):::controller --> " . $id_vista . "[[\"" . $nom_vista . "\"]]:::vista";

            $cont_vista = file_get_contents($info['full_vista']);

            // Analitzar enllaços interns de la vista
            $links_v = ($info['extensio'] === 'twig')
                ? analitzar_twig($cont_vista, $contingut_php)
                : analitzar_phtml($cont_vista, $contingut_php);

            foreach ($links_v as $l) {
                $links_locals[] = ['desti' => $l, 'via' => 'vista', 'origen_id' => $id_vista];
            }

            // --- ANALITZAR JS DINS LA VISTA ---
            if (preg_match_all('/src=["\']?(?:<\?=\s*[\'"])?([a-z0-9_\-\.\/]+\.js[^"\'>\s]*)/i', $cont_vista, $m_js)) {
                foreach ($m_js[1] as $js_brut) {
                    $js_neta = preg_replace('/([\'"]?\s*\?>.*)$/', '', $js_brut);
                    $js_neta = preg_replace('/\?.*/', '', $js_neta);
                    $js_neta = trim($js_neta, " '\"");

                    $rutes_js = analitzar_fitxer_js($js_neta, $root_dir);
                    foreach ($rutes_js as $r) {
                        $links_locals[] = ['desti' => $r, 'via' => 'js', 'origen_id' => $id_origen];
                    }
                }
            }
        } else {
            $errors[] = $id_vista;
            $connexions[] = "    " . $id_origen . " --> " . $id_vista . "[[\"" . $nom_vista . " (NOT FOUND)\"]]:::error";
        }
    }

    // --- ANALITZAR LLISTA (BOTONS PHP) ---
    if ($info['es_llista']) {
        $links_l = analitzar_llista_php($contingut_php);
        foreach ($links_l as $l) {
            $links_locals[] = ['desti' => $l, 'via' => 'lista', 'origen_id' => $id_origen];
        }
    }

    // 2. PROCESSAR BRANQUES I ACUMULAR CONNEXIONS
    foreach ($links_locals as $item) {
        $desti = trim($item['desti']);

        // Saltar variables sense resoldre: $variable, twig, php tags
        $te_variable = strpos($desti, '$') !== false;
        $te_twig = (strpos($desti, chr(123).chr(123)) !== false);
        $te_php = (strpos($desti, chr(60).chr(63)) !== false);

        if (empty($desti) || $te_variable || $te_twig || $te_php) {
            // Afegir com a comentari si conté variables no resoltes
            if (!empty($desti)) {
                $connexions[] = "    %% DESTÍ NO RESOLT des de " . $item['origen_id'] . ": " . addslashes($desti);
            }
            continue;
        }

        $id_desti = str_replace(['/', '.', '-'], '_', $desti);
        $nom_desti = basename($desti);

        // Estil de fletxa segons si és JS o directe
        $fletxa = ($item['via'] == 'js') ? "-. JS .->" : "-->";

        $connexions[] = "    " . $item['origen_id'] . " " . $fletxa . " " . $id_desti . "([\"" . $nom_desti . "\"]):::controller";

        if (!in_array($desti, $analitzats)) {
            $cua[] = $desti;
        }
    }
}

// 3. CONSTRUCCIÓ DEL FITXER FINAL
$mermaid = "```mermaid\nflowchart TD\n";
$mermaid .= "    %% Estils de nodes\n";
$mermaid .= "    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;\n";
$mermaid .= "    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;\n";
$mermaid .= "    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;\n\n";

$mermaid .= implode("\n", array_unique($connexions));

// Aplicar estils d'error als fitxers que no existeixen
if (!empty($errors)) {
    $mermaid .= "\n    class " . implode(",", array_unique($errors)) . " error;";
}

$mermaid .= "\n```";

// Crear directori si no existeix
if (!is_dir(dirname($output_file))) {
    mkdir(dirname($output_file), 0777, true);
}

file_put_contents($output_file, $mermaid);
echo "✅ Fitxer Markdown generat a: $output_file\n";
echo "💡 Copia el contingut a https://mermaid.live per veure el mapa.\n";