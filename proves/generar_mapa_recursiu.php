<?php


require_once "analitzadors/analitzador_twig.php";
require_once "analitzadors/analitzador_phtml.php";

$root_dir = '/home/dani/orbix_local/orbix/';
$entry_point = 'apps/actividades/controller/actividad_que.php';
$output_file = $root_dir . 'proves/actividades/mapa_recursivo.md';

$mapa_global = [];
$visitats = [];

function rastrejar($ruta_relativa)
{
    global $root_dir, $mapa_global, $visitats;

    $ruta_relativa = ltrim($ruta_relativa, '/');
    if (in_array($ruta_relativa, $visitats) || !file_exists($root_dir . $ruta_relativa)) return;

    $visitats[] = $ruta_relativa;
    $nom_node = basename($ruta_relativa);
    $contingut_php = file_get_contents($root_dir . $ruta_relativa);

    if (!isset($mapa_global[$nom_node])) $mapa_global[$nom_node] = ['links' => []];

    // IDENTIFICAR LA VISTA
    // Busquem: renderizar('...twig', $a_campos) o ViewPhtml('...phtml', $a_campos)
    if (preg_match('/(?:renderizar|ViewPhtml)\([\'"]([a-z0-9_\-\.]+\.(twig|phtml))[\'"]/i', $contingut_php, $m_view)) {
        $nom_vista = $m_view[1];
        $ext = $m_view[2];
        $ruta_v = localitzar_vista($nom_vista, dirname($ruta_relativa));

        if ($ruta_v) {
            $contingut_v = file_get_contents($root_dir . $ruta_v);
            $trobats = ($ext === 'twig') ? analitzar_twig($contingut_v, $contingut_php) : analitzar_phtml($contingut_v, $contingut_php);

            foreach ($trobats as $url) {
                processar_troballa($nom_node, $url, $ruta_relativa);
            }
        }
    }

    // TAMBÉ BUSCAR DINS DEL PHP (per si hi ha Hash::link o redireccions)
    preg_match_all('/[\'"]([a-z0-9_\-\.\/]+\.php)[\'"]/i', $contingut_php, $m_php);
    foreach ($m_php[1] as $url) {
        processar_troballa($nom_node, $url, $ruta_relativa);
    }
}

function processar_troballa($origen, $url, $context)
{
    $nom_f = basename($url);
    if ($nom_f === $origen) return;

    $cami = localitzar_fitxer_php($nom_f, dirname($context));
    if ($cami) {
        global $mapa_global;
        $mapa_global[$origen]['links'][] = $nom_f;
        rastrejar($cami);
    }
}

function localitzar_vista($nom, $dir_actual)
{
    global $root_dir;
    $paths = [$dir_actual . '/' . $nom, 'apps/actividades/view/' . $nom, 'apps/actividades/controller/' . $nom];
    foreach ($paths as $p) if (file_exists($root_dir . $p)) return $p;
    return null;
}

function localitzar_fitxer_php($nom, $dir_actual)
{
    global $root_dir;
    $paths = [$dir_actual . '/' . $nom, 'apps/actividades/controller/' . $nom, 'apps/procesos/controller/' . $nom];
    foreach ($paths as $p) if (file_exists($root_dir . $p)) return $p;
    return null;
}

// Iniciar
rastrejar($entry_point);

// Generar Mermaid (simplificat)
$md = "```mermaid\ngraph TD\n";
foreach ($mapa_global as $origen => $dades) {
    foreach (array_unique($dades['links']) as $desti) {
        $md .= "    $origen --> $desti\n";
    }
}
$md .= "```";
file_put_contents($output_file, $md);
echo "Fet!\n";